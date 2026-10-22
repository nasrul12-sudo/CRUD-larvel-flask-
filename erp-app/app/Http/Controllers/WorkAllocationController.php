<?php

namespace App\Http\Controllers;

use App\Models\Workallocation;
use App\Models\Workallocationitem;
use App\Models\Workcompletion;
use App\Models\Workcompletionitem;
use App\Models\Employee;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkAllocationController extends Controller
{
    /**
     * Tampilkan daftar SPKO
     */
    public function index()
    {
        $data = Workallocation::with('employee')->orderBy('ID', 'desc')->get();
        return view('workallocation.index', compact('data'));
    }

    /**
     * Form create
     */
    public function create()
    {
        $employees = Employee::all();
        $products  = Product::all();
        return view('workallocation.create', compact('employees', 'products'));
    }

    /**
     * Generate nomor SPKO otomatis: SPKO + yy + mm + 3 digit urut
     */
    private function generateSPKO()
    {
        $prefix = 'SPKO' . date('ym'); // contoh: SPKO2506
        $last = Workallocation::where('SW', 'like', $prefix . '%')
                              ->orderBy('SW', 'desc')
                              ->first();

        if ($last) {
            $num = (int) substr($last->SW, -3) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Simpan SPKO baru + auto-create Nota Terima Kerja (WorkCompletion)
     */
    public function store(Request $request)
    {
        $request->validate([
            'Employee' => 'required|exists:employee,Id_employee',
            'Process'  => 'required|in:Cor,Brush,Bombing,Sleep',
            'TransDate'=> 'required|date',
            'qty'      => 'required|array|min:1',
            'qty.*'    => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $sw = $this->generateSPKO();

            // 1) Buat header SPKO
            $wa = Workallocation::create([
                'Remarks'   => $request->Remarks,
                'Employee'  => $request->Employee,
                'TransDate' => $request->TransDate,
                'Process'   => $request->Process,
                'SW'        => $sw,
            ]);

            // 2) Buat item SPKO (hanya qty > 0)
            $ordinal = 1;
            $savedItems = [];
            foreach ($request->qty as $productId => $qty) {
                if ($qty > 0) {
                    Workallocationitem::create([
                        'IDM'    => $wa->ID,
                        'Ordinal'=> $ordinal,
                        'Qty'    => $qty,
                        'Weight' => 0,
                        'FG'     => $productId,
                    ]);
                    $savedItems[] = ['ordinal' => $ordinal, 'fg' => $productId, 'qty' => $qty];
                    $ordinal++;
                }
            }

            // 3) Auto-create Nota Terima Kerja (WorkCompletion)
            $wc = Workcompletion::create([
                'Remarks'       => null,
                'Employee'      => $request->Employee,
                'TransDate'     => $request->TransDate,
                'Process'       => $request->Process,
                'WorkAllocation'=> $sw,
            ]);

            $ordinal = 1;
            foreach ($savedItems as $item) {
                Workcompletionitem::create([
                    'IDM'    => $wc->ID,
                    'Ordinal'=> $ordinal,
                    'Qty'    => $item['qty'],
                    'Weight' => 0,
                    'LinkID' => $wa->ID,
                    'LinkOrd'=> $item['ordinal'],
                    'FG'     => $item['fg'],
                ]);
                $ordinal++;
            }

            DB::commit();
            return redirect()->route('workallocation.index')
                             ->with('success', 'SPKO ' . $sw . ' berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal simpan: ' . $e->getMessage()]);
        }
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $wa = Workallocation::with('items.product')->findOrFail($id);
        $employees = Employee::all();
        return view('workallocation.edit', compact('wa', 'employees'));
    }

    /**
     * Update SPKO: tanggal, operator, qty per item
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Employee' => 'required|exists:employee,Id_employee',
            'TransDate'=> 'required|date',
            'qty'      => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $wa = Workallocation::findOrFail($id);
            $wa->update([
                'Employee'  => $request->Employee,
                'TransDate' => $request->TransDate,
            ]);

            // Update qty per item (index ordinal)
            foreach ($request->qty as $ordinal => $qty) {
                Workallocationitem::where('IDM', $wa->ID)
                                  ->where('Ordinal', $ordinal)
                                  ->update(['Qty' => $qty]);
            }

            // Sinkron qty ke WorkCompletion (berdasarkan LinkID & LinkOrd)
            foreach ($request->qty as $ordinal => $qty) {
                Workcompletionitem::where('LinkID', $wa->ID)
                                  ->where('LinkOrd', $ordinal)
                                  ->update(['Qty' => $qty]);
            }

            // Sinkron tanggal & operator ke WorkCompletion
            Workcompletion::where('WorkAllocation', $wa->SW)->update([
                'Employee'  => $request->Employee,
                'TransDate' => $request->TransDate,
            ]);

            DB::commit();
            return redirect()->route('workallocation.index')
                             ->with('success', 'SPKO berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal update: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus SPKO + item + WorkCompletion terkait
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $wa = Workallocation::findOrFail($id);

            // Hapus workcompletionitem & workcompletion terkait
            $wc = Workcompletion::where('WorkAllocation', $wa->SW)->first();
            if ($wc) {
                Workcompletionitem::where('IDM', $wc->ID)->delete();
                $wc->delete();
            }

            // Hapus item SPKO lalu header
            Workallocationitem::where('IDM', $wa->ID)->delete();
            $wa->delete();

            DB::commit();
            return redirect()->route('workallocation.index')
                             ->with('success', 'SPKO berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal hapus: ' . $e->getMessage()]);
        }
    }

    /**
     * Tampilan print
     */
    public function print($id)
    {
        $wa = Workallocation::with(['employee', 'items.product'])->findOrFail($id);
        return view('workallocation.print', compact('wa'));
    }
}

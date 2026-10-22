<?php

namespace App\Http\Controllers;

use App\Models\Workcompletion;
use App\Models\Workcompletionitem;
use App\Models\Workallocation;
use App\Models\Workallocationitem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkCompletionController extends Controller
{
    public function index()
    {
        $data = Workcompletion::with(['employee', 'items.product'])
            ->orderBy('ID', 'desc')
            ->get();
        return view('workcompletion.index', compact('data'));
    }

    public function create()
    {
        // Tampilkan SPKO yang belum dibuatkan NTHKO
        $allocations = Workallocation::whereNotIn('SW', function($query) {
            $query->select('WorkAllocation')->from('workcompletion');
        })->orderBy('ID', 'desc')->get();
        
        return view('workcompletion.create', compact('allocations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'WorkAllocation' => 'required|exists:workallocation,SW',
            'items'          => 'required|array',
            'items.*.ordinal'=> 'required|integer',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.weight' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $wa = Workallocation::where('SW', $request->WorkAllocation)->firstOrFail();

            $wc = Workcompletion::create([
                'Remarks'        => 'Penerimaan Hasil Kerja',
                'Employee'       => $wa->Employee,
                'TransDate'      => date('Y-m-d'),
                'Process'        => $wa->Process, // Ambil proses dari SPKO
                'WorkAllocation' => $wa->SW,
            ]);

            foreach ($request->items as $item) {
                $wai = Workallocationitem::where('IDM', $wa->ID)->where('Ordinal', $item['ordinal'])->first();
                
                Workcompletionitem::create([
                    'IDM'     => $wc->ID,
                    'Ordinal' => $item['ordinal'],
                    'Qty'     => $item['qty'],
                    'Weight'  => $item['weight'], // Berat Aktual
                    'LinkID'  => $wa->ID,
                    'LinkOrd' => $item['ordinal'],
                    'FG'      => $wai->FG,
                ]);
            }

            DB::commit();
            return redirect()->route('workcompletion.index')->with('success', 'NTHKO berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $wc = Workcompletion::findOrFail($id);
            Workcompletionitem::where('IDM', $wc->ID)->delete();
            $wc->delete();
            DB::commit();
            return redirect()->route('workcompletion.index')->with('success', 'NTHKO berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

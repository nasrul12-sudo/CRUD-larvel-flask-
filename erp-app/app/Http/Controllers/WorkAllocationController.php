<?php

namespace App\Http\Controllers;

use App\Models\Workallocation;
use App\Models\Workallocationitem;
use App\Models\Employee;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkAllocationController extends Controller
{
    public function index()
    {
        $data = Workallocation::with(['employee', 'items.product'])
            ->orderBy('ID', 'desc')
            ->get();
        return view('workallocation.index', compact('data'));
    }

    public function create()
    {
        $employees = Employee::orderBy('nama')->get();
        $categories = config('categories');
        return view('workallocation.create', compact('employees', 'categories'));
    }

    private function generateSPKO(): string
    {
        $prefix = 'SPKO' . date('ym');
        $last = Workallocation::where('SW', 'like', $prefix . '%')->orderBy('SW', 'desc')->lockForUpdate()->first();
        $num = $last ? ((int) substr($last->SW, -3) + 1) : 1;
        return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Employee'  => 'required|exists:employee,Id_employee',
            'Process'   => 'required|in:Cor,Brush,Bombing,Slep',
            'TransDate' => 'required|date',
            'items'     => 'required|array|min:1',
            'items.*.sub_category' => 'required|string|exists:product,sub_category',
            'items.*.description'  => 'required|string',
            'items.*.serial_no'    => 'required|string',
            'items.*.carat'        => 'required|string',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.weight'       => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $sw = $this->generateSPKO();
            $wa = Workallocation::create([
                'Remarks'   => $request->Remarks,
                'Employee'  => $request->Employee,
                'TransDate' => $request->TransDate,
                'Process'   => $request->Process,
                'SW'        => $sw,
            ]);

            $ordinal = 1;
            foreach ($request->items as $item) {
                $product = Product::where('sub_category', $item['sub_category'])->first();
                $product->update([
                    'custom_description' => $item['description'],
                    'custom_serial_no'   => $item['serial_no'],
                    'custom_carat'       => $item['carat'],
                ]);

                Workallocationitem::create([
                    'IDM'     => $wa->ID,
                    'Ordinal' => $ordinal,
                    'Qty'     => $item['qty'],
                    'Weight'  => $item['weight'],
                    'FG'      => $product->Id_product,
                ]);
                $ordinal++;
            }

            DB::commit();
            return redirect()->route('workallocation.index')->with('success', 'SPKO ' . $sw . ' berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal simpan: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $wa = Workallocation::with('items.product')->findOrFail($id);
        $employees = Employee::orderBy('nama')->get();
        $categories = config('categories');
        return view('workallocation.edit', compact('wa', 'employees', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Employee'  => 'required|exists:employee,Id_employee',
            'Process'   => 'required|in:Cor,Brush,Bombing,Slep',
            'TransDate' => 'required|date',
            'items'     => 'required|array|min:1',
            'items.*.sub_category' => 'required|string|exists:product,sub_category',
            'items.*.description'  => 'required|string',
            'items.*.serial_no'    => 'required|string',
            'items.*.carat'        => 'required|string',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.weight'       => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $wa = Workallocation::findOrFail($id);
            $wa->update([
                'Employee'  => $request->Employee,
                'TransDate' => $request->TransDate,
                'Process'   => $request->Process,
            ]);

            Workallocationitem::where('IDM', $wa->ID)->delete();

            $ordinal = 1;
            foreach ($request->items as $item) {
                $product = Product::where('sub_category', $item['sub_category'])->first();
                $product->update([
                    'custom_description' => $item['description'],
                    'custom_serial_no'   => $item['serial_no'],
                    'custom_carat'       => $item['carat'],
                ]);

                Workallocationitem::create([
                    'IDM'     => $wa->ID,
                    'Ordinal' => $ordinal,
                    'Qty'     => $item['qty'],
                    'Weight'  => $item['weight'],
                    'FG'      => $product->Id_product,
                ]);
                $ordinal++;
            }

            DB::commit();
            return redirect()->route('workallocation.index')->with('success', 'SPKO berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal update: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $wa = Workallocation::findOrFail($id);
            Workallocationitem::where('IDM', $wa->ID)->delete();
            $wa->delete();
            DB::commit();
            return redirect()->route('workallocation.index')->with('success', 'SPKO berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal hapus: ' . $e->getMessage()]);
        }
    }

    public function print($id)
    {
        $wa = Workallocation::with(['employee', 'items.product'])->findOrFail($id);
        return view('workallocation.print', compact('wa'));
    }
}

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkAllocationController;
use App\Http\Controllers\WorkCompletionController;
use App\Http\Controllers\ReportController;
use App\Models\Workallocation;
use App\Models\Workallocationitem;
use App\Models\Workcompletionitem;

Route::get('/', function () {
    return redirect()->route('workallocation.index');
});

Route::get('/workallocation/print/{id}', [WorkAllocationController::class, 'print'])->name('workallocation.print');
Route::resource('workallocation', WorkAllocationController::class);

Route::resource('workcompletion', WorkCompletionController::class);

Route::get('/report/daily', [ReportController::class, 'daily'])->name('report.daily');

Route::get('/api/spko-items/{sw}', function($sw) {
    $wa = \App\Models\Workallocation::where('SW', $sw)->first();
    if (!$wa) return response()->json([]);
    
    $items = \App\Models\Workallocationitem::where('IDM', $wa->ID)->with('product')->get();
    
    return response()->json($items->map(function($i) use ($wa) {
        return [
            'ordinal'     => $i->Ordinal,
            'description' => $i->product->custom_description ?? $i->product->description,
            'qty'         => $i->Qty,
            'weight'      => $i->Weight,
            'process'     => $wa->Process,
        ];
    }));
});
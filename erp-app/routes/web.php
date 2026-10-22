<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkAllocationController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('workallocation.index');
});

// Modul SPKO (Create, Update, Delete, Print)
Route::get('/workallocation/print/{id}', [WorkAllocationController::class, 'print'])
     ->name('workallocation.print');
Route::resource('workallocation', WorkAllocationController::class);

// Modul Report — Raw Query
Route::get('/report/daily', [ReportController::class, 'daily'])
     ->name('report.daily');

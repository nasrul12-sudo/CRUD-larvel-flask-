<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Soal No.3 — Raw Query (TANPA Eloquent)
     */
    public function daily()
    {
        $results = DB::select("
            SELECT
                wa.TransDate         AS spko_date,
                wa.SW                AS no_spko,
                wa.Process           AS proses,
                e.nama               AS operator,
                wc.ID                AS nthko_id,
                wc.TransDate         AS nthko_date
            FROM workallocation wa
            LEFT JOIN workcompletion wc ON wc.WorkAllocation = wa.SW
            LEFT JOIN employee e        ON e.Id_employee     = wa.Employee
            ORDER BY wa.TransDate DESC, wa.SW DESC
        ");

        return view('report.daily', compact('results'));
    }
}

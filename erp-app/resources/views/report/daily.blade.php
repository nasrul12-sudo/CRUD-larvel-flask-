@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Report Harian SPKO & NTHKO (Raw Query)</h1>
    <a href="http://127.0.0.1:5000/weight-diff"
       target="_blank"
       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold text-sm">
        📊 Lihat Selisih Berat (Flask) ↗
    </a>
</div>

<table class="w-full bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="p-3 text-left">Tanggal SPKO</th>
            <th class="p-3 text-left">No SPKO</th>
            <th class="p-3 text-left">Proses</th>
            <th class="p-3 text-left">Operator</th>
            <th class="p-3 text-left">No NTHKO</th>
            <th class="p-3 text-left">Tanggal NTHKO</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $row)
        <tr class="border-b hover:bg-gray-50">
            <td class="p-3">{{ $row->spko_date }}</td>
            <td class="p-3 font-semibold text-blue-600">{{ $row->no_spko }}</td>
            <td class="p-3">{{ $row->proses }}</td>
            <td class="p-3">{{ $row->operator }}</td>
            <td class="p-3 font-semibold text-green-700">{{ $row->nthko_sw ?? '-' }}</td>
            <td class="p-3">{{ $row->nthko_date ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="p-6 text-center text-gray-500">Belum ada data transaksi.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection

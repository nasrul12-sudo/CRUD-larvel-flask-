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
            <th class="p-2">Tanggal SPKO</th>
            <th class="p-2">No SPKO</th>
            <th class="p-2">Proses</th>
            <th class="p-2">Operator</th>
            <th class="p-2">No NTHKO</th>
            <th class="p-2">Tanggal NTHKO</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $row)
        <tr class="border-b">
            <td class="p-2">{{ $row->spko_date }}</td>
            <td class="p-2">{{ $row->no_spko }}</td>
            <td class="p-2">{{ $row->proses }}</td>
            <td class="p-2">{{ $row->operator }}</td>
            <td class="p-2">{{ $row->nthko_id ?? '-' }}</td>
            <td class="p-2">{{ $row->nthko_date ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="p-4 text-center">Belum ada data.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
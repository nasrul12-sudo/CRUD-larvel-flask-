@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Report Harian SPKO & NTHKO (Raw Query)</h1>

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
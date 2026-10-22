@extends('layouts.app')

@section('content')
<div class="flex justify-between mb-4">
    <h1 class="text-2xl font-bold">Daftar SPKO</h1>
    <a href="{{ route('workallocation.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded">+ Buat SPKO</a>
</div>

<table class="w-full bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="p-2">No SPKO</th>
            <th class="p-2">Tanggal</th>
            <th class="p-2">Operator</th>
            <th class="p-2">Proses</th>
            <th class="p-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr class="border-b">
            <td class="p-2">{{ $row->SW }}</td>
            <td class="p-2">{{ $row->TransDate }}</td>
            <td class="p-2">{{ $row->employee->nama ?? '-' }}</td>
            <td class="p-2">{{ $row->Process }}</td>
            <td class="p-2 flex gap-2">
                <a href="{{ route('workallocation.edit', $row->ID) }}"
                   class="text-blue-600">Edit</a>
                <a href="{{ route('workallocation.print', $row->ID) }}"
                   target="_blank" class="text-gray-700">Print</a>
                <form action="{{ route('workallocation.destroy', $row->ID) }}"
                      method="POST"
                      onsubmit="return confirm('Hapus SPKO ini?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="p-4 text-center">Belum ada data.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
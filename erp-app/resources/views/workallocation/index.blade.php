@extends('layouts.app')

@section('content')
<div class="flex justify-between mb-4">
    <h1 class="text-2xl font-bold">Daftar SPKO (Rencana Kerja)</h1>
    <a href="{{ route('workallocation.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Buat SPKO</a>
</div>

<div class="space-y-4">
    @forelse($data as $row)
    <div class="bg-white shadow rounded border border-gray-200">
        <div class="bg-gray-50 p-4 border-b flex justify-between items-center">
            <div>
                <span class="font-bold text-lg text-blue-700">{{ $row->SW }}</span>
                <span class="text-gray-500 text-sm ml-4">{{ \Carbon\Carbon::parse($row->TransDate)->format('d M Y') }}</span>
                <span class="text-gray-500 text-sm ml-4">Operator: {{ $row->employee->nama ?? '-' }}</span>
            </div>
            <div class="flex gap-3 text-sm">
                <a href="{{ route('workallocation.edit', $row->ID) }}" class="text-blue-600 font-semibold hover:underline">Edit</a>
                <a href="{{ route('workallocation.print', $row->ID) }}" target="_blank" class="text-gray-700 font-semibold hover:underline">Print</a>
                <form action="{{ route('workallocation.destroy', $row->ID) }}" method="POST" onsubmit="return confirm('Hapus SPKO ini?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 font-semibold hover:underline">Hapus</button>
                </form>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead class="text-gray-500 bg-gray-100 border-b">
                <tr>
                    <th class="p-2 text-left w-10">#</th>
                    <th class="p-2 text-left">Kategori</th>
                    <th class="p-2 text-left">Deskripsi</th>
                    <th class="p-2 text-left">Serial</th>
                    <th class="p-2 text-left">Carat</th>
                    <th class="p-2 text-right">Qty</th>
                    <th class="p-2 text-right">Berat Rencana (gr)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($row->items as $item)
                <tr class="border-b last:border-0">
                    <td class="p-2">{{ $item->Ordinal }}</td>
                    <td class="p-2 font-semibold">{{ $item->product->sub_category ?? '-' }}</td>
                    <td class="p-2">{{ $item->product->custom_description ?? $item->product->description ?? '-' }}</td>
                    <td class="p-2">{{ $item->product->custom_serial_no ?? $item->product->serial_no ?? '-' }}</td>
                    <td class="p-2">{{ $item->product->custom_carat ?? $item->product->carat ?? '-' }}</td>
                    <td class="p-2 text-right font-bold">{{ $item->Qty }}</td>
                    <td class="p-2 text-right text-blue-600 font-bold">{{ number_format($item->Weight, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div class="bg-white p-6 text-center text-gray-500 rounded shadow">Belum ada data SPKO.</div>
    @endforelse
</div>
@endsection

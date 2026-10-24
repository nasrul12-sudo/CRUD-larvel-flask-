@extends('layouts.app')
@section('content')
<div class="flex justify-between mb-4">
    <h1 class="text-2xl font-bold">Daftar NTHKO (Nota Terima Kerja)</h1>
    <a href="{{ route('workcompletion.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Buat NTHKO</a>
</div>

<div class="space-y-4">
    @forelse($data as $row)
    <div class="bg-white shadow rounded border border-gray-200">
        <div class="bg-gray-50 p-4 border-b flex justify-between items-center">
            <div>
                {{-- ⭐ Tampilkan SW (Nomor NTHKO), bukan ID --}}
                <span class="font-bold text-lg text-green-700">{{ $row->SW }}</span>
                <span class="text-gray-500 text-sm ml-4">Ref SPKO: {{ $row->WorkAllocation }}</span>
                <span class="text-gray-500 text-sm ml-4">Proses: {{ $row->Process }}</span>
                <span class="text-gray-500 text-sm ml-4">Operator: {{ $row->employee->nama ?? '-' }}</span>
            </div>
            <form action="{{ route('workcompletion.destroy', $row->ID) }}" method="POST" onsubmit="return confirm('Hapus NTHKO ini?')">
                @csrf @method('DELETE')
                <button class="text-red-600 text-sm font-semibold hover:underline">Hapus</button>
            </form>
        </div>
        <table class="w-full text-sm">
            <thead class="text-gray-500 bg-gray-100 border-b">
                <tr>
                    <th class="p-2 text-left">#</th>
                    <th class="p-2 text-left">Kategori</th>
                    <th class="p-2 text-left">Deskripsi</th>
                    <th class="p-2 text-right">Qty</th>
                    <th class="p-2 text-right">Berat Aktual (gr)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($row->items as $item)
                <tr class="border-b last:border-0">
                    <td class="p-2">{{ $item->Ordinal }}</td>
                    <td class="p-2 font-semibold">{{ $item->product->sub_category ?? '-' }}</td>
                    <td class="p-2">{{ $item->product->custom_description ?? $item->product->description ?? '-' }}</td>
                    <td class="p-2 text-right font-bold text-green-600">{{ $item->Qty }}</td>
                    <td class="p-2 text-right">{{ number_format($item->Weight, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div class="bg-white p-6 text-center text-gray-500 rounded shadow">Belum ada data NTHKO.</div>
    @endforelse
</div>
@endsection

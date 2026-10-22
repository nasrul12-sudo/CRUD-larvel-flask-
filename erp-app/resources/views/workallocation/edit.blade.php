@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit SPKO — {{ $wa->SW }}</h1>

<form action="{{ route('workallocation.update', $wa->ID) }}" method="POST" class="bg-white p-6 rounded shadow">
    @csrf @method('PUT')

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block mb-1">Operator</label>
            <select name="Employee" class="w-full border p-2 rounded" required>
                @foreach($employees as $emp)
                    <option value="{{ $emp->Id_employee }}"
                        {{ $wa->Employee == $emp->Id_employee ? 'selected' : '' }}>
                        {{ $emp->Id_employee }} - {{ $emp->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block mb-1">Tanggal</label>
            <input type="date" name="TransDate"
                   value="{{ $wa->TransDate }}" class="w-full border p-2 rounded" required>
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-2">Qty per Item</h2>
    <table class="w-full bg-gray-50 rounded mb-4">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 text-left">Ordinal</th>
                <th class="p-2 text-left">Produk</th>
                <th class="p-2 text-left w-32">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wa->items as $item)
            <tr class="border-b">
                <td class="p-2">{{ $item->Ordinal }}</td>
                <td class="p-2">
                    {{ $item->FG }} — {{ $item->product->description ?? '-' }}
                </td>
                <td class="p-2">
                    <input type="number" name="qty[{{ $item->Ordinal }}]"
                           value="{{ $item->Qty }}" min="0"
                           class="w-full border p-1 rounded">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex gap-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
        <a href="{{ route('workallocation.index') }}"
           class="bg-gray-300 px-4 py-2 rounded">Batal</a>
    </div>
</form>
@endsection
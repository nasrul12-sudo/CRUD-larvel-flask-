@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Buat SPKO Baru</h1>

<form action="{{ route('workallocation.store') }}" method="POST" class="bg-white p-6 rounded shadow">
    @csrf

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block mb-1">Operator</label>
            <select name="Employee" class="w-full border p-2 rounded" required>
                <option value="">-- Pilih Operator --</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->Id_employee }}"
                        {{ old('Employee') == $emp->Id_employee ? 'selected' : '' }}>
                        {{ $emp->Id_employee }} - {{ $emp->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Proses</label>
            <select name="Process" class="w-full border p-2 rounded" required>
                <option value="">-- Pilih Proses --</option>
                @foreach(['Cor','Brush','Bombing','Sleep'] as $p)
                    <option value="{{ $p }}" {{ old('Process') == $p ? 'selected' : '' }}>
                        {{ $p }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Tanggal</label>
            <input type="date" name="TransDate" value="{{ old('TransDate', date('Y-m-d')) }}"
                   class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label class="block mb-1">Catatan (Opsional)</label>
            <input type="text" name="Remarks" value="{{ old('Remarks') }}"
                   class="w-full border p-2 rounded">
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-2">Pilih Produk & Qty</h2>
    <table class="w-full bg-gray-50 rounded mb-4">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 text-left">Produk</th>
                <th class="p-2 text-left">Carat</th>
                <th class="p-2 text-left w-32">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $prod)
            <tr class="border-b">
                <td class="p-2">
                    <strong>{{ $prod->Id_product }}</strong> — {{ $prod->description }}
                </td>
                <td class="p-2">{{ $prod->carat }}</td>
                <td class="p-2">
                    <input type="number" name="qty[{{ $prod->Id_product }}]"
                           value="{{ old('qty.'.$prod->Id_product, 0) }}"
                           min="0" class="w-full border p-1 rounded">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex gap-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        <a href="{{ route('workallocation.index') }}"
           class="bg-gray-300 px-4 py-2 rounded">Batal</a>
    </div>
</form>
@endsection
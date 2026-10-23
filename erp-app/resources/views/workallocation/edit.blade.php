@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit SPKO — {{ $wa->SW }}</h1>

<form action="{{ route('workallocation.update', $wa->ID) }}" method="POST" id="spko-form" class="bg-white p-6 rounded shadow">
    @csrf @method('PUT')

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block mb-1 font-semibold">Operator</label>
            <select name="Employee" class="w-full border p-2 rounded" required>
                @foreach($employees as $emp)
                    <option value="{{ $emp->Id_employee }}" {{ $wa->Employee == $emp->Id_employee ? 'selected' : '' }}>{{ $emp->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block mb-1 font-semibold">Tanggal</label>
            <input type="date" name="TransDate" value="{{ $wa->TransDate }}" class="w-full border p-2 rounded" required>
        </div>
    </div>

    <div class="flex justify-between items-center mb-2">
        <h2 class="text-lg font-semibold">Item Produk</h2>
        <button type="button" id="add-row-btn" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">+ Tambah Produk</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-gray-50 rounded border" id="product-table">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2 text-left w-10">#</th>
                    <th class="p-2 text-left min-w-140px">Kategori</th>
                    <th class="p-2 text-left min-w-180px">Deskripsi (Manual)</th>
                    <th class="p-2 text-left w-24">Serial (Manual)</th>
                    <th class="p-2 text-left w-20">Carat (Manual)</th>
                    <th class="p-2 text-left w-16">Qty</th>
                    <th class="p-2 text-left w-24">Weight/gr (Rencana)</th>
                    <th class="p-2 text-center w-12">Aksi</th>
                </tr>
            </thead>
            <tbody id="product-body">
                @foreach($wa->items as $item)
                <tr class="border-b product-row">
                    <td class="p-2 row-number text-center font-semibold"></td>
                    <td class="p-2">
                        <select class="cat-select w-full border p-1 rounded">
                            <option value="">-- Pilih --</option>
                            @foreach($categories as $code => $name)
                                <option value="{{ $code }}" {{ $item->product->sub_category == $code ? 'selected' : '' }}>{{ $code }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-2"><input type="text" class="desc-input w-full border p-1 rounded" value="{{ $item->product->custom_description ?? $item->product->description }}"></td>
                    <td class="p-2"><input type="text" class="serial-input w-full border p-1 rounded" value="{{ $item->product->custom_serial_no ?? $item->product->serial_no }}"></td>
                    <td class="p-2"><input type="text" class="carat-input w-full border p-1 rounded" value="{{ $item->product->custom_carat ?? $item->product->carat }}"></td>
                    <td class="p-2"><input type="number" class="qty-input w-full border p-1 rounded" min="1" value="{{ $item->Qty }}"></td>
                    <td class="p-2"><input type="number" step="0.01" class="weight-input w-full border p-1 rounded" min="0" value="{{ $item->Weight }}"></td>
                    <td class="p-2 text-center"><button type="button" class="remove-row text-red-600 font-bold">✕</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update</button>
        <a href="{{ route('workallocation.index') }}" class="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400">Batal</a>
    </div>
</form>

<template id="row-template">
    <tr class="border-b product-row">
        <td class="p-2 row-number text-center font-semibold"></td>
        <td class="p-2">
            <select class="cat-select w-full border p-1 rounded">
                <option value="">-- Pilih --</option>
                @foreach($categories as $code => $name)
                    <option value="{{ $code }}">{{ $code }}</option>
                @endforeach
            </select>
        </td>
        <td class="p-2"><input type="text" class="desc-input w-full border p-1 rounded"></td>
        <td class="p-2"><input type="text" class="serial-input w-full border p-1 rounded"></td>
        <td class="p-2"><input type="text" class="carat-input w-full border p-1 rounded"></td>
        <td class="p-2"><input type="number" class="qty-input w-full border p-1 rounded" min="1" value="1"></td>
        <td class="p-2"><input type="number" step="0.01" class="weight-input w-full border p-1 rounded" min="0" value="0"></td>
        <td class="p-2 text-center"><button type="button" class="remove-row text-red-600 font-bold">✕</button></td>
    </tr>
</template>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const body = document.getElementById('product-body');
    
    function updateRowNumber() {
        body.querySelectorAll('.product-row').forEach((row, i) => {
            row.querySelector('.row-number').textContent = i + 1;
        });
    }

    function addRow() {
        const tpl = document.getElementById('row-template').content.cloneNode(true);
        body.appendChild(tpl);
        updateRowNumber();
    }

    body.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            updateRowNumber();
        }
    });

    document.getElementById('add-row-btn').addEventListener('click', addRow);
    updateRowNumber();

    document.getElementById('spko-form').addEventListener('submit', function() {
        let idx = 0;
        body.querySelectorAll('.product-row').forEach(row => {
            const cat = row.querySelector('.cat-select').value;
            if (!cat) return;

            row.querySelector('.cat-select').setAttribute('name', `items[${idx}][sub_category]`);
            row.querySelector('.desc-input').setAttribute('name', `items[${idx}][description]`);
            row.querySelector('.serial-input').setAttribute('name', `items[${idx}][serial_no]`);
            row.querySelector('.carat-input').setAttribute('name', `items[${idx}][carat]`);
            row.querySelector('.qty-input').setAttribute('name', `items[${idx}][qty]`);
            row.querySelector('.weight-input').setAttribute('name', `items[${idx}][weight]`);
            idx++;
        });
    });
});
</script>
@endsection

@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Buat SPKO Baru (Rencana Kerja)</h1>

<form action="{{ route('workallocation.store') }}" method="POST" id="spko-form" class="bg-white p-6 rounded shadow">
    @csrf

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block mb-1 font-semibold">Operator <span class="text-red-500">*</span></label>
            <select name="Employee" class="w-full border p-2 rounded" required>
                <option value="">-- Pilih Operator --</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->Id_employee }}">{{ $emp->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block mb-1 font-semibold">Proses <span class="text-red-500">*</span></label>
            <select name="Process" class="w-full border p-2 rounded" required>
                <option value="">-- Pilih Proses --</option>
                @foreach(['Cor','Brush','Bombing','Slep'] as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block mb-1 font-semibold">Tanggal <span class="text-red-500">*</span></label>
            <input type="date" name="TransDate" value="{{ date('Y-m-d') }}" class="w-full border p-2 rounded" required>
        </div>
        <div>
            <label class="block mb-1 font-semibold">Catatan</label>
            <input type="text" name="Remarks" class="w-full border p-2 rounded" placeholder="Opsional">
        </div>
    </div>

    <div class="flex justify-between items-center mb-2">
        <h2 class="text-lg font-semibold">Item Produk</h2>
        <button type="button" id="add-row-btn" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
            + Tambah Produk
        </button>
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
            <tbody id="product-body"></tbody>
        </table>
    </div>

    <div class="mt-3 text-sm text-gray-700 flex gap-6">
        <div><strong>Total Qty:</strong> <span id="total-qty">0</span></div>
        <div><strong>Total Weight:</strong> <span id="total-weight">0.00</span> gr</div>
    </div>

    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Simpan SPKO</button>
        <a href="{{ route('workallocation.index') }}" class="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400">Batal</a>
    </div>
</form>

<template id="row-template">
    <tr class="border-b product-row">
        <td class="p-2 row-number text-center font-semibold"></td>
        <td class="p-2">
            <select class="cat-select w-full border p-1 rounded">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $code => $name)
                    <option value="{{ $code }}">{{ $code }}</option>
                @endforeach
            </select>
        </td>
        <td class="p-2"><input type="text" class="desc-input w-full border p-1 rounded" placeholder="Deskripsi"></td>
        <td class="p-2"><input type="text" class="serial-input w-full border p-1 rounded" placeholder="Serial"></td>
        <td class="p-2"><input type="text" class="carat-input w-full border p-1 rounded" placeholder="Carat"></td>
        <td class="p-2"><input type="number" class="qty-input w-full border p-1 rounded" min="1" value="1"></td>
        <td class="p-2"><input type="number" step="0.01" class="weight-input w-full border p-1 rounded" min="0" value="0"></td>
        <td class="p-2 text-center"><button type="button" class="remove-row text-red-600 font-bold hover:text-red-800">✕</button></td>
    </tr>
</template>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const body = document.getElementById('product-body');
    
    function addRow() {
        const tpl = document.getElementById('row-template').content.cloneNode(true);
        body.appendChild(tpl);
        updateRowNumber(); updateTotals();
    }

    body.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            updateRowNumber(); updateTotals();
        }
    });

    body.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('weight-input')) updateTotals();
    });

    function updateRowNumber() {
        body.querySelectorAll('.product-row').forEach((row, i) => {
            row.querySelector('.row-number').textContent = i + 1;
        });
    }

    function updateTotals() {
        let q = 0, w = 0;
        body.querySelectorAll('.product-row').forEach(row => {
            q += parseInt(row.querySelector('.qty-input').value) || 0;
            w += parseFloat(row.querySelector('.weight-input').value) || 0;
        });
        document.getElementById('total-qty').textContent = q;
        document.getElementById('total-weight').textContent = w.toFixed(2);
    }

    document.getElementById('add-row-btn').addEventListener('click', addRow);
    addRow();

    document.getElementById('spko-form').addEventListener('submit', function(e) {
        // ⭐ 1. Bersihkan semua atribut name terlebih dahulu
        body.querySelectorAll('.product-row input, .product-row select').forEach(el => {
            el.removeAttribute('name');
        });

        let valid = true, hasItems = false;
        const usedCats = [];
        
        // ⭐ 2. Beri name baru hanya pada baris yang masih ada
        body.querySelectorAll('.product-row').forEach(row => {
            const cat = row.querySelector('.cat-select').value;
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            
            if (!cat || qty < 1) return; // Abaikan baris yang tidak valid/kosong

            if (usedCats.includes(cat)) {
                alert('Kategori tidak boleh duplikat.'); valid = false; return;
            }
            usedCats.push(cat); hasItems = true;
            const idx = usedCats.length - 1;

            row.querySelector('.cat-select').setAttribute('name', `items[${idx}][sub_category]`);
            row.querySelector('.desc-input').setAttribute('name', `items[${idx}][description]`);
            row.querySelector('.serial-input').setAttribute('name', `items[${idx}][serial_no]`);
            row.querySelector('.carat-input').setAttribute('name', `items[${idx}][carat]`);
            row.querySelector('.qty-input').setAttribute('name', `items[${idx}][qty]`);
            row.querySelector('.weight-input').setAttribute('name', `items[${idx}][weight]`);
        });

        if (!valid || !hasItems) {
            if (!hasItems) alert('Minimal 1 produk dengan Qty > 0');
            e.preventDefault();
        }
    });
});
</script>
@endsection

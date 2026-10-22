@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-bold mb-4">Buat Nota Terima Kerja (NTHKO)</h1>

<form action="{{ route('workcompletion.store') }}" method="POST" class="bg-white p-6 rounded shadow">
    @csrf
    
    <div class="mb-6 bg-blue-50 p-4 border border-blue-200 rounded">
        <label class="block mb-2 font-bold text-blue-800">Pilih SPKO (Surat Perintah Kerja)</label>
        <select name="WorkAllocation" id="spko-select" class="w-full border border-gray-300 p-2 rounded" required>
            <option value="">-- Pilih SPKO --</option>
            @foreach($allocations as $wa)
                <option value="{{ $wa->SW }}">{{ $wa->SW }} - Operator: {{ $wa->employee->nama }} ({{ $wa->TransDate }})</option>
            @endforeach
        </select>
        @if($allocations->isEmpty())
            <p class="text-sm text-red-600 mt-2 font-semibold">⚠️ Semua SPKO sudah memiliki NTHKO. Silakan buat SPKO baru terlebih dahulu.</p>
        @endif
    </div>

    <div id="detail-container" class="hidden">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-lg font-semibold">Input Berat Aktual (Hasil Timbangan)</h2>
            <span class="text-sm text-gray-500 italic">*Qty & Deskripsi dikunci sesuai SPKO.</span>
        </div>
        
        <table class="w-full bg-white rounded border border-gray-200 shadow-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-3 text-left">Ordinal</th>
                    <th class="p-3 text-left">Deskripsi</th>
                    <th class="p-3 text-left">Proses</th> {{-- ⭐ Tambahan Kolom Proses --}}
                    <th class="p-3 text-left">Qty SPKO</th>
                    <th class="p-3 text-left">Berat Rencana (gr)</th>
                    <th class="p-3 text-left text-green-700 bg-green-50">Berat Aktual (gr)</th>
                </tr>
            </thead>
            <tbody id="detail-body"></tbody>
        </table>
        <button type="submit" class="mt-6 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 font-semibold">Simpan NTHKO</button>
    </div>
</form>

<script>
window.onload = function() {
    const selectEl = document.getElementById('spko-select');
    const container = document.getElementById('detail-container');
    const body = document.getElementById('detail-body');

    selectEl.addEventListener('change', function () {
        const sw = this.value;
        if (!sw) { container.classList.add('hidden'); body.innerHTML = ''; return; }

        body.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-gray-500">Memuat data...</td></tr>';
        container.classList.remove('hidden');

        fetch('/api/spko-items/' + sw)
            .then(res => res.json())
            .then(data => {
                body.innerHTML = '';
                if (!data || data.length === 0) {
                    body.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-red-500">Data tidak ditemukan.</td></tr>';
                    return;
                }

                data.forEach(item => {
                    // Pastikan qty dan process terbaca
                    const qty = item.qty || 0;
                    const process = item.process || '-'; // ⭐ Ambil process dari API
                    
                    body.innerHTML += `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 font-semibold">${item.ordinal}</td>
                            <td class="p-3">${item.description}</td>
                            <td class="p-3 font-bold text-gray-800">${process}</td> {{-- ⭐ Tampilkan Proses --}}
                            <td class="p-3 font-bold text-gray-700">${qty}</td>
                            <td class="p-3 font-bold text-blue-600">${parseFloat(item.weight).toFixed(2)}</td>
                            <td class="p-3 bg-green-50">
                                <input type="hidden" name="items[${item.ordinal}][ordinal]" value="${item.ordinal}">
                                <input type="hidden" name="items[${item.ordinal}][qty]" value="${qty}">
                                <input type="number" step="0.01" name="items[${item.ordinal}][weight]" class="w-full border border-green-300 p-2 rounded" value="${item.weight}" required>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(err => {
                body.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-red-500">Error: ' + err.message + '</td></tr>';
            });
    });
};
</script>
@endsection

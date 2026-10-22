<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    {{-- ⭐ Kosongkan title agar tidak tercetak di header PDF --}}
    <title> </title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; margin: 20px; }
        .header-box { border: 1px solid #000; padding: 10px; margin-bottom: 15px; background: #eef2ff; }
        .header-title { font-weight: bold; margin-bottom: 10px; font-size: 14px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { padding: 3px; vertical-align: top; }
        .items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items th, .items td { border: 1px solid #000; padding: 8px; }
        .items th { background: #e5e7eb; font-weight: bold; text-align: center; }
        .items td.center { text-align: center; }

        /* ⭐ CSS Print: Sembunyikan Header/Footer Browser & Navbar */
        @page { margin: 10mm; }
        @media print {
            body { margin: 0; padding: 0; }
            nav, .navbar, .no-print, header, footer { display: none !important; }
            .header-box { background: #fff !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header-box">
        <div class="header-title">Surat Perintah Kerja Operator</div>
        <table class="header-table">
            <tr>
                <td width="15%">ID Operator</td><td width="35%">: {{ $wa->Employee }}</td>
                <td width="15%">No SPKO</td><td width="35%">: {{ $wa->SW }}</td>
            </tr>
            <tr>
                <td>Nama Operator</td><td>: {{ $wa->employee->nama ?? '-' }}</td>
                <td>Proses</td><td>: {{ $wa->Process ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td><td>: {{ \Carbon\Carbon::parse($wa->TransDate)->format('d F Y') }}</td>
                <td>Catatan</td><td>: {{ $wa->Remarks ?? '' }}</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="35%">Deskripsi</th>
                <th width="15%">Carat</th>
                <th width="30%">SKU</th>
                <th width="15%">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wa->items as $i => $item)
                @php
                    $p = $item->product;
                    $weight = (int) $item->Weight;
                    $cat = $p->sub_category;
                    $serial = $p->custom_serial_no ?? $p->serial_no;
                    $carat = $p->custom_carat ?? $p->carat;
                    
                    $rawDesc = $p->custom_description ?? $p->description;
                    $descName = trim(preg_replace('/\s+\d+\s*gr.*$/i', '', $rawDesc)); 
                    $desc = "{$descName} {$weight}gr-{$serial}-{$carat}K";

                    $caratNum = (int) filter_var($carat, FILTER_SANITIZE_NUMBER_INT);
                    $divisi = str_pad(($p->Id_product % 3) + 1, 2, '0', STR_PAD_LEFT);
                    $ukuran = ($serial % 2 == 0) ? '00' : '03';
                    
                    if ($p->Id_product % 2 == 0) {
                        $moldId = 'D' . ($serial % 100);
                    } else {
                        $moldId = str_pad($serial % 100000, 5, '0', STR_PAD_LEFT);
                    }

                    $sku = "{$cat}{$weight}.{$serial}.{$caratNum}K.{$divisi}.{$ukuran}.00.0.{$moldId}";
                @endphp
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $desc }}</td>
                    <td class="center">{{ $carat }}</td>
                    <td>{{ $sku }}</td>
                    <td class="center">{{ $item->Qty }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

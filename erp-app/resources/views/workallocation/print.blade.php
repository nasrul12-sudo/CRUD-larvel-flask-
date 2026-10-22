<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print SPKO {{ $wa->SW }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { border: 1px solid #000; padding: 10px; margin-bottom: 10px; }
        .header table { width: 100%; }
        .items { width: 100%; border-collapse: collapse; }
        .items th, .items td { border: 1px solid #000; padding: 5px; }
        .items th { background: #eee; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <strong>Surat Perintah Kerja</strong>
        <table>
            <tr>
                <td>Operator ID</td><td>: {{ $wa->Employee }}</td>
                <td>No SPKO</td><td>: {{ $wa->SW }}</td>
            </tr>
            <tr>
                <td>Nama Operator</td><td>: {{ $wa->employee->nama ?? '-' }}</td>
                <td>Proses</td><td>: {{ $wa->Process }}</td>
            </tr>
            <tr>
                <td>Tanggal</td><td colspan="3">: {{ $wa->TransDate }}</td>
            </tr>
            <tr>
                <td>Catatan</td><td colspan="3">: {{ $wa->Remarks ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>No.</th>
                <th>Deskripsi</th>
                <th>Carat</th>
                <th>SKU</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wa->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product->description ?? '-' }}</td>
                <td>{{ $item->product->carat ?? '-' }}</td>
                <td>{{ $item->FG }}</td>
                <td>{{ $item->Qty }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
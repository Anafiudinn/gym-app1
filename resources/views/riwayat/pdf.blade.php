<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi Gym</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f2f2f2;
            color: #444;
            font-weight: bold;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .bg-success { background-color: #dcfce7; color: #166534; }
        .bg-danger { background-color: #fee2e2; color: #991b1b; }
        .bg-warning { background-color: #fef9c3; color: #854d0e; }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Riwayat Transaksi Gym</h2>
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Invoice</th>
                <th width="20%">Pelanggan</th>
                <th width="15%">Paket / Tipe</th>
                <th width="15%">Nominal</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($data as $key => $item)
            @php if($item->status == 'dibayar') $total += $item->jumlah_bayar; @endphp
            <tr>
                <td style="text-align: center;">{{ $key + 1 }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td style="font-weight: bold;">{{ $item->kode_invoice }}</td>
                <td>{{ $item->member->nama ?? $item->nama_tamu }}</td>
                <td>
                    {{ $item->paket->nama_paket ?? '-' }} <br>
                    <small style="color: #666;">({{ $item->tipe }})</small>
                </td>
                <td class="text-right">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $item->status == 'dibayar' ? 'bg-success' : ($item->status == 'ditolak' ? 'bg-danger' : 'bg-warning') }}">
                        {{ $item->status }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="5" class="text-right">Total Pendapatan (Lunas):</td>
                <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini dihasilkan secara otomatis oleh Sistem Informasi Manajemen Gym.</p>
    </div>

</body>
</html>
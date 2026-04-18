<!DOCTYPE html>
<html>
<head>
    <title>STRUK_{{ $transaksi->kode_invoice }}_{{ $transaksi->created_at->format('dmY_Hi') }}</title>
    <style>
        @page {
            size: 58mm auto; /* Biar panjangnya ngikutin konten */
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace; /* Font struk klasik */
            width: 48mm; /* Sisakan margin untuk printer thermal */
            font-size: 11px;
            padding: 5px;
            margin: 0 auto;
            color: #000;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .header-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer {
            margin-top: 10px;
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="text-center">
    <span class="header-name">{{ \App\Models\Setting::getValue('nama_gym', 'GYMKU SEMARANG') }}</span><br>
    <span style="font-size: 9px;">{{ \App\Models\Setting::getValue('alamat_gym', 'Jl. Contoh No. 123') }}</span><br>
    <span style="font-size: 9px;">WA: {{ \App\Models\Setting::getValue('no_telp', '-') }}</span>
    <div class="line"></div>
    
    {{ $transaksi->kode_invoice }}<br>
    {{ $transaksi->created_at->format('d/m/Y H:i') }}
    <div class="line"></div>
</div>

<table>
    <tr>
        <td colspan="2">{{ $transaksi->member->nama ?? $transaksi->nama_tamu }}</td>
    </tr>
    <tr>
        <td>{{ $transaksi->paket->nama_paket ?? 'Harian' }}</td>
        <td class="text-right">Rp{{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</td>
    </tr>
    @if($transaksi->tipe == 'membership')
    <tr>
        <td colspan="2" style="font-size: 9px;">(Membership Active)</td>
    </tr>
    @endif
</table>

<div class="line"></div>
<table class="font-bold">
    <tr>
        <td>TOTAL</td>
        <td class="text-right">Rp{{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</td>
    </tr>
</table>
<div class="line"></div>

<div class="text-center footer">
    Terima Kasih!<br>
    Selamat Berlatih gess!<br>
    <span style="font-size: 8px;">Powered by GymPro System</span>
</div>

<script>
    // Langsung print saat halaman terbuka
    window.print();
    
    // Opsional: Tutup jendela otomatis setelah print (bagus untuk popup)
    window.onafterprint = function() {
        window.close();
    };
</script>

</body>
</html>
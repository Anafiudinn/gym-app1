<style>
    @page { size: 58mm 100mm; margin: 0; }
    body { font-family: 'Courier New', Courier, monospace; width: 58mm; font-size: 12px; padding: 10px; }
    .text-center { text-align: center; }
    .line { border-top: 1px dashed #000; margin: 5px 0; }
</style>

<div class="text-center">
    <strong>GYMKU SEMARANG</strong><br>
    Jl. Contoh No. 123<br>
    <div class="line"></div>
    {{ $transaksi->kode_invoice }}<br>
    {{ $transaksi->created_at->format('d/m/Y H:i') }}
    <div class="line"></div>
</div>

<table>
    <tr>
        <td style="width: 100px">{{ $transaksi->paket->nama_paket }}</td>
        <td>: Rp{{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</td>
    </tr>
</table>

<div class="line"></div>
<strong>TOTAL: Rp{{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</strong>
<div class="line"></div>
<div class="text-center">
    Terima Kasih!<br>
    Selamat Berlatih gess!
</div>

<script>window.print();</script>
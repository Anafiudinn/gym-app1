<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Pembayaran - GymFit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen p-4 md:p-8">

    <div class="max-w-2xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-4 border-b pb-2 text-blue-600">Ringkasan Pesanan</h2>
                <div class="space-y-3 text-gray-700">
                    <p class="flex justify-between"><span>Nama:</span> <span class="font-bold">{{ $transaksi->member->nama }}</span></p>
                    <p class="flex justify-between"><span>Paket:</span> <span class="font-bold">{{ $transaksi->paket->nama_paket }}</span></p>
                    <p class="flex justify-between text-lg text-blue-600 font-bold border-t pt-2">
                        <span>Total:</span> <span>Rp{{ number_format($transaksi->jumlah_bayar) }}</span>
                    </p>
                </div>
            </div>

            <div class="bg-blue-600 p-6 rounded-2xl shadow-lg text-white">
                <h3 class="font-bold mb-3"><i class="fas fa-university mr-2"></i> Rekening Pembayaran</h3>
                <div class="bg-blue-700 p-4 rounded-xl space-y-2">
                    <p class="text-sm opacity-80">Bank BCA</p>
                    <p class="text-xl font-mono font-bold tracking-widest">1234567890</p>
                    <p class="text-sm">A.N. GymFit Semarang</p>
                </div>
                <p class="mt-4 text-xs opacity-75">*Silakan transfer sesuai nominal di atas sebelum mengunggah bukti.</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold mb-6 text-gray-800">Konfirmasi Bayar</h2>
            
            <form method="POST" action="/upload/{{ $transaksi->kode_invoice }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase">Nama Pengirim (Rekening)</label>
                    <input type="text" name="nama_rekening" placeholder="Nama di Buku Tabungan" 
                        class="w-full mt-1 p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase">Bank Anda</label>
                    <input type="text" name="nama_bank" placeholder="Misal: BCA, Mandiri, BRI" 
                        class="w-full mt-1 p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase">Foto Bukti Transfer</label>
                    <input type="file" name="bukti" class="w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                </div>

                <button class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 transition duration-200">
                    KIRIM KONFIRMASI
                </button>
            </form>

            <form action="/batal/{{ $transaksi->kode_invoice }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" onclick="return confirm('Yakin ingin membatalkan pendaftaran ini?')" 
                    class="w-full text-gray-400 text-sm font-medium hover:text-red-500 transition">
                    Batalkan Pendaftaran
                </button>
            </form>
        </div>
    </div>

</body>
</html>
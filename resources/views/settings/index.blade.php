@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h2>
        <p class="text-sm text-gray-600">Kelola identitas bisnis dan integrasi API WhatsApp.</p>
    </div>

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-2 mb-4 text-emerald-600">
                    <i class="fa-solid fa-building"></i>
                    <h3 class="font-bold">Identitas Bisnis</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Gym</label>
                        <input type="text" name="nama_gym" value="{{ \App\Models\Setting::getValue('nama_gym') }}"
                            class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Contoh: Ahmad Fitness Center">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat (Muncul di Struk)</label>
                        <textarea name="alamat_gym" rows="3"
                            class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Alamat lengkap gym...">{{ \App\Models\Setting::getValue('alamat_gym') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Telepon Bisnis</label>
                        <input type="text" name="no_telp" value="{{ \App\Models\Setting::getValue('no_telp') }}"
                            class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="0812xxxx">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Link Google Maps (Iframe Src)</label>
                        <input type="text" name="google_maps_url" value="{{ \App\Models\Setting::getValue('google_maps_url') }}"
                            class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500" placeholder="https://www.google.com/maps/embed?pb=...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jam Operasional</label>
                        <textarea name="jam_operasional" rows="3" class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500">{{ \App\Models\Setting::getValue('jam_operasional') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Instagram</label>
                        <input type="text" name="instagram" value="{{ \App\Models\Setting::getValue('instagram') }}"
                            class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="https://www.instagram.com/">
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mt-6">
                <div class="flex items-center gap-2 mb-4 text-emerald-600">
                    <i class="fa-solid fa-credit-card"></i>
                    <h3 class="font-bold">Informasi Pembayaran (Transfer)</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Bank & Atas Nama</label>
                        <input type="text" name="payment_bank" value="{{ \App\Models\Setting::getValue('payment_bank') }}"
                            class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500" placeholder="BCA a/n Ahmad Gym">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Rekening</label>
                        <input type="text" name="payment_rekening" value="{{ \App\Models\Setting::getValue('payment_rekening') }}"
                            class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500" placeholder="1234567890">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-2 mb-4 text-emerald-600">
                    <i class="fa-brands fa-whatsapp"></i>
                    <h3 class="font-bold">Integrasi WhatsApp API</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">API Gateway URL</label>
                        <input type="text" name="wa_api_url" value="{{ \App\Models\Setting::getValue('wa_api_url') }}"
                            class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="https://api.fonnte.com/send">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">API Token / Key</label>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" name="wa_api_key" value="{{ \App\Models\Setting::getValue('wa_api_key') }}"
                                class="w-full mt-1 p-2.5 border rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            <button type="button" @click="show = !show" class="absolute right-3 top-4 text-gray-400">
                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">*Jangan bagikan token ini kepada siapapun.</p>
                    </div>

                    <div class="pt-4 border-t border-gray-50 mt-4">
                        <p class="text-xs text-gray-500 mb-2">Status API:
                            <span class="{{ \App\Models\Setting::getValue('wa_api_key') ? 'text-emerald-500' : 'text-red-400' }} font-bold">
                                {{ \App\Models\Setting::getValue('wa_api_key') ? 'Terpasang' : 'Belum Setting' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg shadow-emerald-200">
                <i class="fa-solid fa-save mr-2"></i> Simpan Semua Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
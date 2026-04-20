// Pop-up penuh
GymAlert.success('Pendaftaran berhasil!')
GymAlert.error('Email sudah terdaftar.')
GymAlert.warning('Slot hampir penuh!')
GymAlert.info('Promo berlaku hingga akhir bulan.')

// Konfirmasi dengan callback
GymAlert.confirm({
    title: 'Konfirmasi Daftar?',
    text: 'Data kamu akan diproses.',
    confirmText: 'Ya, Daftar!',
    onConfirm: () => document.getElementById('form-daftar').submit()
})

// Delete konfirmasi
GymAlert.deleteConfirm(formEl, 'Paket Premium')

// Toast pojok kanan atas (ringkas)
GymAlert.toast('Data tersimpan!', 'success')
GymAlert.toast('Gagal upload!', 'error')

// Loading state (misal saat submit AJAX)
GymAlert.loading('Memproses pendaftaran...')
// lalu tutup dengan:
GymAlert.close()
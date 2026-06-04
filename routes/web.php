<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\KycStatusController;
use App\Http\Controllers\LandingController;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// KYC status (auth required, no account_status check)
Route::middleware('auth')->group(function () {
    Route::get('/kyc-status', [KycStatusController::class, 'show'])->name('kyc.status');
});

// ═══════════════════════════════════════
// ADMIN ROUTES
// ═══════════════════════════════════════
Route::middleware(['auth', 'account.status', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Anggota
    Route::get('/anggota', [\App\Http\Controllers\Admin\AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('/anggota/{user}', [\App\Http\Controllers\Admin\AnggotaController::class, 'show'])->name('anggota.show');
    Route::post('/anggota/{user}/activate', [\App\Http\Controllers\Admin\AnggotaController::class, 'activate'])->name('anggota.activate');
    Route::post('/anggota/{user}/reject', [\App\Http\Controllers\Admin\AnggotaController::class, 'reject'])->name('anggota.reject');
    Route::post('/anggota/{user}/suspend', [\App\Http\Controllers\Admin\AnggotaController::class, 'suspend'])->name('anggota.suspend');
    Route::post('/anggota/{user}/reset-password', [\App\Http\Controllers\Admin\AnggotaController::class, 'resetPassword'])->name('anggota.reset-password');

    // Gadai
    Route::get('/gadai', [\App\Http\Controllers\Admin\GadaiController::class, 'index'])->name('gadai.index');
    Route::get('/gadai/pengajuan/{pengajuan}', [\App\Http\Controllers\Admin\GadaiController::class, 'showPengajuan'])->name('gadai.pengajuan');
    Route::post('/gadai/pengajuan/{pengajuan}/approve', [\App\Http\Controllers\Admin\GadaiController::class, 'approvePengajuan'])->name('gadai.pengajuan.approve');
    Route::post('/gadai/pengajuan/{pengajuan}/reject', [\App\Http\Controllers\Admin\GadaiController::class, 'rejectPengajuan'])->name('gadai.pengajuan.reject');
    Route::get('/gadai/transaksi/{transaksi}', [\App\Http\Controllers\Admin\GadaiController::class, 'showTransaksi'])->name('gadai.transaksi');
    Route::post('/gadai/transaksi/{transaksi}/lelang', [\App\Http\Controllers\Admin\GadaiController::class, 'markMenungguLelang'])->name('gadai.transaksi.lelang');
    Route::post('/gadai/transaksi/{transaksi}/proses-lelang', [\App\Http\Controllers\Admin\GadaiController::class, 'prosesLelang'])->name('gadai.transaksi.proses-lelang');
    Route::get('/gadai/transaksi/{transaksi}/pdf', [\App\Http\Controllers\Admin\GadaiController::class, 'exportPdf'])->name('gadai.transaksi.pdf');

    // Konfirmasi
    Route::get('/konfirmasi', [\App\Http\Controllers\Admin\KonfirmasiController::class, 'index'])->name('konfirmasi.index');
    Route::post('/konfirmasi/pembayaran/{pembayaran}/confirm', [\App\Http\Controllers\Admin\KonfirmasiController::class, 'confirmPembayaranGadai'])->name('konfirmasi.pembayaran.confirm');
    Route::post('/konfirmasi/pembayaran/{pembayaran}/reject', [\App\Http\Controllers\Admin\KonfirmasiController::class, 'rejectPembayaranGadai'])->name('konfirmasi.pembayaran.reject');
    Route::post('/konfirmasi/simpanan/{simpanan}/confirm', [\App\Http\Controllers\Admin\KonfirmasiController::class, 'confirmSimpanan'])->name('konfirmasi.simpanan.confirm');
    Route::post('/konfirmasi/simpanan/{simpanan}/reject', [\App\Http\Controllers\Admin\KonfirmasiController::class, 'rejectSimpanan'])->name('konfirmasi.simpanan.reject');
    Route::post('/konfirmasi/registrasi/{user}/confirm', [\App\Http\Controllers\Admin\KonfirmasiController::class, 'confirmRegistrasi'])->name('konfirmasi.registrasi.confirm');
    Route::post('/konfirmasi/registrasi/{user}/reject', [\App\Http\Controllers\Admin\KonfirmasiController::class, 'rejectRegistrasi'])->name('konfirmasi.registrasi.reject');

    // Simpanan
    Route::get('/simpanan', [\App\Http\Controllers\Admin\SimpananController::class, 'index'])->name('simpanan.index');
    Route::get('/simpanan/export', [\App\Http\Controllers\Admin\SimpananController::class, 'exportExcel'])->name('simpanan.export');

    // Katalog
    Route::get('/katalog', [\App\Http\Controllers\Admin\KatalogController::class, 'index'])->name('katalog.index');
    Route::get('/katalog/create', [\App\Http\Controllers\Admin\KatalogController::class, 'create'])->name('katalog.create');
    Route::post('/katalog', [\App\Http\Controllers\Admin\KatalogController::class, 'store'])->name('katalog.store');
    Route::get('/katalog/{katalog}/edit', [\App\Http\Controllers\Admin\KatalogController::class, 'edit'])->name('katalog.edit');
    Route::put('/katalog/{katalog}', [\App\Http\Controllers\Admin\KatalogController::class, 'update'])->name('katalog.update');
    Route::delete('/katalog/{katalog}', [\App\Http\Controllers\Admin\KatalogController::class, 'destroy'])->name('katalog.destroy');

    // Laporan
    Route::get('/laporan/keuangan', [\App\Http\Controllers\Admin\LaporanController::class, 'keuangan'])->name('laporan.keuangan');
    Route::get('/laporan/gadai', [\App\Http\Controllers\Admin\LaporanController::class, 'gadai'])->name('laporan.gadai');
    Route::get('/laporan/keuangan/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportKeuanganPdf'])->name('laporan.keuangan.pdf');
    Route::get('/laporan/gadai/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportGadaiExcel'])->name('laporan.gadai.excel');

    // SHU
    Route::get('/shu', [\App\Http\Controllers\Admin\ShuController::class, 'index'])->name('shu.index');
    Route::get('/shu/create', [\App\Http\Controllers\Admin\ShuController::class, 'create'])->name('shu.create');
    Route::post('/shu', [\App\Http\Controllers\Admin\ShuController::class, 'store'])->name('shu.store');
    Route::get('/shu/{shu}', [\App\Http\Controllers\Admin\ShuController::class, 'show'])->name('shu.show');
    Route::post('/shu/{shu}/calculate', [\App\Http\Controllers\Admin\ShuController::class, 'calculate'])->name('shu.calculate');
    Route::post('/shu/{shu}/publish', [\App\Http\Controllers\Admin\ShuController::class, 'publish'])->name('shu.publish');
    Route::get('/shu/{shu}/excel', [\App\Http\Controllers\Admin\ShuController::class, 'exportExcel'])->name('shu.excel');
    Route::get('/shu/{shu}/pdf', [\App\Http\Controllers\Admin\ShuController::class, 'exportPdf'])->name('shu.pdf');

    // Biaya
    Route::get('/biaya', [\App\Http\Controllers\Admin\BiayaController::class, 'index'])->name('biaya.index');
    Route::get('/biaya/create', [\App\Http\Controllers\Admin\BiayaController::class, 'create'])->name('biaya.create');
    Route::post('/biaya', [\App\Http\Controllers\Admin\BiayaController::class, 'store'])->name('biaya.store');
    Route::get('/biaya/{biaya}/edit', [\App\Http\Controllers\Admin\BiayaController::class, 'edit'])->name('biaya.edit');
    Route::put('/biaya/{biaya}', [\App\Http\Controllers\Admin\BiayaController::class, 'update'])->name('biaya.update');
    Route::delete('/biaya/{biaya}', [\App\Http\Controllers\Admin\BiayaController::class, 'destroy'])->name('biaya.destroy');

    // Notifikasi
    Route::get('/notifikasi/kirim', [\App\Http\Controllers\Admin\NotifikasiController::class, 'kirim'])->name('notifikasi.kirim');
    Route::post('/notifikasi/send', [\App\Http\Controllers\Admin\NotifikasiController::class, 'send'])->name('notifikasi.send');

    // Pengaturan
    Route::get('/pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'index'])->name('pengaturan');
    Route::put('/pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'update'])->name('pengaturan.update');
});

// ═══════════════════════════════════════
// PENGURUS ROUTES
// ═══════════════════════════════════════
Route::middleware(['auth', 'account.status', 'role:admin,pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Pengurus\DashboardController::class, 'index'])->name('dashboard');

    // Konfirmasi
    Route::get('/konfirmasi', [\App\Http\Controllers\Pengurus\KonfirmasiController::class, 'index'])->name('konfirmasi.index');
    Route::post('/konfirmasi/pengajuan/{pengajuan}/approve', [\App\Http\Controllers\Pengurus\KonfirmasiController::class, 'approvePengajuan'])->name('konfirmasi.pengajuan.approve');
    Route::post('/konfirmasi/pengajuan/{pengajuan}/reject', [\App\Http\Controllers\Pengurus\KonfirmasiController::class, 'rejectPengajuan'])->name('konfirmasi.pengajuan.reject');
    Route::post('/konfirmasi/pembayaran/{pembayaran}/confirm', [\App\Http\Controllers\Pengurus\KonfirmasiController::class, 'confirmPembayaran'])->name('konfirmasi.pembayaran.confirm');
    Route::post('/konfirmasi/pembayaran/{pembayaran}/reject', [\App\Http\Controllers\Pengurus\KonfirmasiController::class, 'rejectPembayaran'])->name('konfirmasi.pembayaran.reject');
    Route::post('/konfirmasi/simpanan/{simpanan}/confirm', [\App\Http\Controllers\Pengurus\KonfirmasiController::class, 'confirmSimpanan'])->name('konfirmasi.simpanan.confirm');
    Route::post('/konfirmasi/simpanan/{simpanan}/reject', [\App\Http\Controllers\Pengurus\KonfirmasiController::class, 'rejectSimpanan'])->name('konfirmasi.simpanan.reject');
    Route::post('/konfirmasi/registrasi/{user}/confirm', [\App\Http\Controllers\Pengurus\KonfirmasiController::class, 'confirmRegistrasi'])->name('konfirmasi.registrasi.confirm');
    Route::post('/konfirmasi/registrasi/{user}/reject', [\App\Http\Controllers\Pengurus\KonfirmasiController::class, 'rejectRegistrasi'])->name('konfirmasi.registrasi.reject');

    // Gadai
    Route::get('/gadai', [\App\Http\Controllers\Pengurus\GadaiController::class, 'index'])->name('gadai.index');
    Route::get('/gadai/pengajuan/{pengajuan}', [\App\Http\Controllers\Pengurus\GadaiController::class, 'showPengajuan'])->name('gadai.pengajuan');
    Route::get('/gadai/transaksi/{transaksi}', [\App\Http\Controllers\Pengurus\GadaiController::class, 'showTransaksi'])->name('gadai.transaksi');

    // Anggota (view-only)
    Route::get('/anggota', [\App\Http\Controllers\Pengurus\AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('/anggota/{user}', [\App\Http\Controllers\Pengurus\AnggotaController::class, 'show'])->name('anggota.show');

    // Biaya
    Route::get('/biaya/tambah', [\App\Http\Controllers\Pengurus\BiayaController::class, 'create'])->name('biaya.create');
    Route::post('/biaya', [\App\Http\Controllers\Pengurus\BiayaController::class, 'store'])->name('biaya.store');

    // Pesan
    Route::get('/pesan', [\App\Http\Controllers\Pengurus\PesanController::class, 'index'])->name('pesan.index');
    Route::get('/pesan/{user}', [\App\Http\Controllers\Pengurus\PesanController::class, 'show'])->name('pesan.show');
    Route::post('/pesan/{user}/reply', [\App\Http\Controllers\Pengurus\PesanController::class, 'reply'])->name('pesan.reply');
});

// ═══════════════════════════════════════
// ANGGOTA ROUTES
// ═══════════════════════════════════════
Route::middleware(['auth', 'account.status', 'role:anggota'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Anggota\DashboardController::class, 'index'])->name('dashboard');

    // Gadai
    Route::get('/gadai', [\App\Http\Controllers\Anggota\GadaiController::class, 'index'])->name('gadai.index');
    Route::get('/gadai/pengajuan', [\App\Http\Controllers\Anggota\GadaiController::class, 'createPengajuan'])->name('gadai.pengajuan.create');
    Route::post('/gadai/pengajuan', [\App\Http\Controllers\Anggota\GadaiController::class, 'storePengajuan'])->name('gadai.pengajuan.store');
    Route::get('/gadai/simulasi', [\App\Http\Controllers\Anggota\GadaiController::class, 'simulasi'])->name('gadai.simulasi');
    Route::get('/gadai/{transaksi}/detail', [\App\Http\Controllers\Anggota\GadaiController::class, 'detail'])->name('gadai.detail');
    Route::get('/gadai/{transaksi}/bayar', [\App\Http\Controllers\Anggota\GadaiController::class, 'bayarForm'])->name('gadai.bayar');
    Route::post('/gadai/{transaksi}/bayar', [\App\Http\Controllers\Anggota\GadaiController::class, 'bayarStore'])->name('gadai.bayar.store');

    // Simpanan
    Route::get('/simpanan', [\App\Http\Controllers\Anggota\SimpananController::class, 'index'])->name('simpanan.index');
    Route::get('/simpanan/bayar', [\App\Http\Controllers\Anggota\SimpananController::class, 'bayarForm'])->name('simpanan.bayar');
    Route::post('/simpanan/bayar', [\App\Http\Controllers\Anggota\SimpananController::class, 'bayarStore'])->name('simpanan.bayar.store');

    // SHU
    Route::get('/shu', [\App\Http\Controllers\Anggota\ShuController::class, 'index'])->name('shu.index');

    // Riwayat
    Route::get('/riwayat', [\App\Http\Controllers\Anggota\RiwayatController::class, 'index'])->name('riwayat');

    // Notifikasi
    Route::get('/notifikasi', [\App\Http\Controllers\Anggota\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/read', [\App\Http\Controllers\Anggota\NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [\App\Http\Controllers\Anggota\NotifikasiController::class, 'markAllRead'])->name('notifikasi.read-all');

    // Pesan
    Route::get('/pesan', [\App\Http\Controllers\Anggota\PesanController::class, 'index'])->name('pesan.index');
    Route::get('/pesan/{user}', [\App\Http\Controllers\Anggota\PesanController::class, 'show'])->name('pesan.show');
    Route::post('/pesan/{user}/send', [\App\Http\Controllers\Anggota\PesanController::class, 'send'])->name('pesan.send');

    // Profil
    Route::get('/profil', [\App\Http\Controllers\Anggota\ProfilController::class, 'index'])->name('profil');
    Route::put('/profil', [\App\Http\Controllers\Anggota\ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [\App\Http\Controllers\Anggota\ProfilController::class, 'updatePassword'])->name('profil.password');
});

require __DIR__.'/auth.php';

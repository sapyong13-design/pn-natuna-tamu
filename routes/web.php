<?php
use App\Http\Controllers\PublicController; use Illuminate\Support\Facades\Route;
Route::get('/', [PublicController::class,'index'])->name('home');
Route::get('/buku-tamu', [PublicController::class,'create'])->name('buku-tamu.create');
Route::get('/cek', [PublicController::class,'cekTamu'])->name('cek');
Route::post('/buku-tamu', [PublicController::class,'store'])->name('buku-tamu.store');
Route::get('/buku-tamu/selesai/{kode_kunjungan}', [PublicController::class,'success'])->name('buku-tamu.selesai');

{{--
    Manual gadai creation popup for pengurus.
    Expects: $anggotaList (Collection), $jenisBarangList (Collection)
    Enclosing Alpine scope must expose: createOpen (bool)
--}}
<div x-show="createOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @click.self="createOpen = false"
     @keydown.escape.window="createOpen = false"
     style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
     x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div @click.stop
         class="bg-white popup-sheet w-full shadow-2xl scrollbar-hide"
         style="max-width:620px; max-height:90vh; overflow-y:auto; border-radius:24px;"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

        {{-- Header --}}
        <div class="relative overflow-hidden rounded-t-3xl px-6 py-5"
             style="background:linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);">
            <div class="absolute inset-0 opacity-[0.05]"
                 style="background-image:radial-gradient(circle,#fff 1px,transparent 1px); background-size:14px 14px;"></div>
            <div class="relative flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background:rgba(255,255,255,0.15);">
                        <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white leading-tight">Buat Gadai Manual</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">Input data gadai secara langsung oleh pengurus</p>
                    </div>
                </div>
                <button type="button" @click="createOpen = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0 transition-colors"
                        style="background:rgba(255,255,255,0.15);"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ $formAction ?? route('pengurus.gadai.store-manual') }}"
              enctype="multipart/form-data"
              x-data="{
                  appRaw: 0, loanRaw: 0,
                  fmt(n) { return n ? new Intl.NumberFormat('id-ID').format(parseInt(n)) : ''; },
                  handleApp(e) { let d=e.target.value.replace(/\D/g,''); this.appRaw=d?parseInt(d):0; e.target.value=this.fmt(this.appRaw); },
                  handleLoan(e) { let d=e.target.value.replace(/\D/g,''); this.loanRaw=d?parseInt(d):0; e.target.value=this.fmt(this.loanRaw); }
              }">
            @csrf

            <div class="px-5 py-5 space-y-4">

                {{-- Anggota + Jenis Barang --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Anggota <span class="text-red-500">*</span></label>
                        <select name="anggota_id" class="form-input" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($anggotaList as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Jenis Barang <span class="text-red-500">*</span></label>
                        <select name="jenis_barang_id" class="form-input" required>
                            <option value="">-- Pilih Jenis Barang --</option>
                            @foreach($jenisBarangList as $j)
                            <option value="{{ $j->id }}">{{ $j->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Brand + Kondisi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Merk / Tipe <span class="text-red-500">*</span></label>
                        <input type="text" name="brand_name" class="form-input" placeholder="Contoh: Samsung Galaxy A54" required>
                    </div>
                    <div>
                        <label class="form-label">Kondisi Barang <span class="text-red-500">*</span></label>
                        <select name="condition" class="form-input" required>
                            <option value="">-- Pilih Kondisi --</option>
                            <option value="Baru">Baru</option>
                            <option value="Sangat Bagus">Sangat Bagus</option>
                            <option value="Bagus">Bagus</option>
                            <option value="Cukup">Cukup</option>
                            <option value="Kurang">Kurang</option>
                        </select>
                    </div>
                </div>

                {{-- Berat/Jumlah + Lokasi Simpan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Berat / Jumlah</label>
                        <input type="text" name="weight_or_quantity" class="form-input" placeholder="Contoh: 1 unit / 10 gram">
                    </div>
                    <div>
                        <label class="form-label">Lokasi Penyimpanan</label>
                        <input type="text" name="warehouse_location" class="form-input" placeholder="Contoh: Rak A-01">
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="form-label">Deskripsi Barang</label>
                    <textarea name="description" class="form-input" rows="2" placeholder="Keterangan tambahan tentang kondisi/kelengkapan barang..."></textarea>
                </div>

                {{-- Nilai Taksiran + Pinjaman --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Nilai Taksiran (Rp) <span class="text-red-500">*</span></label>
                        <div class="flex items-center rounded-xl overflow-hidden" style="border:1px solid #ddebd5;">
                            <span class="px-3 py-2.5 text-xs font-bold border-r flex-shrink-0"
                                  style="background:#eaf3de; border-color:#ddebd5; color:var(--green);">Rp</span>
                            <input type="text" class="flex-1 px-3 py-2.5 text-xs font-semibold outline-none border-0 bg-white"
                                   placeholder="0" x-on:input="handleApp($event)" required>
                            <input type="hidden" name="appraisal_value" :value="appRaw">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Pinjaman Disetujui (Rp) <span class="text-red-500">*</span></label>
                        <div class="flex items-center rounded-xl overflow-hidden" style="border:1px solid #ddebd5;">
                            <span class="px-3 py-2.5 text-xs font-bold border-r flex-shrink-0"
                                  style="background:#eaf3de; border-color:#ddebd5; color:var(--green);">Rp</span>
                            <input type="text" class="flex-1 px-3 py-2.5 text-xs font-semibold outline-none border-0 bg-white"
                                   placeholder="0" x-on:input="handleLoan($event)" required>
                            <input type="hidden" name="loan_amount" :value="loanRaw">
                        </div>
                    </div>
                </div>

                {{-- Tanggal Gadai --}}
                <div>
                    <label class="form-label">Tanggal Mulai Gadai <span class="text-red-500">*</span></label>
                    <input type="date" name="pawn_date" class="form-input" value="{{ date('Y-m-d') }}" required>
                    <p class="text-xs text-mony-muted mt-1">Jatuh tempo otomatis ditetapkan 4 bulan sejak tanggal ini.</p>
                </div>

                {{-- Foto Barang --}}
                <div>
                    <label class="form-label">Foto Barang (Opsional, maks. 6 foto)</label>
                    <input type="file" name="photos[]" multiple accept="image/*"
                           class="form-input text-xs" style="padding:6px;">
                    <p class="text-xs text-mony-muted mt-1">Format: JPG, PNG, WEBP. Maks. 5 MB per file.</p>
                </div>

                {{-- Info bunga --}}
                <div class="rounded-xl px-4 py-3 text-xs flex items-start gap-2.5"
                     style="background:var(--green-light); border:1px solid #c6e4a8; color:var(--green2);">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Bunga <strong>8%/bulan</strong>. Jatuh tempo awal <strong>4 bulan</strong> sejak tanggal gadai, diperpanjang otomatis setiap bunga dikonfirmasi.</span>
                </div>

                @if($errors->any())
                <div class="rounded-xl px-4 py-3 text-xs bg-red-50 border border-red-200 text-red-700">
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

            </div>

            {{-- Footer --}}
            <div class="px-5 pb-5 flex gap-2">
                <button type="button" @click="createOpen = false" class="btn-secondary flex-1 justify-center py-3 text-sm">
                    Batal
                </button>
                <button type="submit" class="btn-success flex-1 justify-center py-3 text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Buat Transaksi
                </button>
            </div>

        </form>
    </div>
</div>
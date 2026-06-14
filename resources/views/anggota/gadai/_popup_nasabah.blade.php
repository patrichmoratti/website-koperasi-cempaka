<div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
    <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
        <p class="text-xs font-semibold text-mony-text">Data Nasabah</p>
    </div>
    @php
        $nik   = $user->nik ?? '';
        $phone = $user->phone ?? '';
        $nikMask  = strlen($nik)  > 6 ? substr($nik,0,6).'XXXXXX'       : ($nik ?: '-');
        $phoneMask= strlen($phone)> 4 ? substr($phone,0,4).'XXXXX'       : ($phone ?: '-');
    @endphp
    @php
        $nasabahRows = [
            ['Nama Nasabah', $user->name],
            ['Nomor KTP', $nikMask],
            ['Alamat Anggota', $user->address ?? '-'],
            ['Nomor Handphone', $phoneMask],
        ];
    @endphp
    @foreach($nasabahRows as $i => [$label, $val])
    <div class="flex justify-between items-start px-4 py-2 text-xs {{ $i < 3 ? 'border-b' : '' }}" style="{{ $i < 3 ? 'border-color:#f0f7ee;' : '' }}">
        <span class="text-mony-muted flex-shrink-0">{{ $label }}</span>
        <span class="font-semibold text-mony-text text-right max-w-44 ml-2">{{ $val }}</span>
    </div>
    @endforeach
</div>
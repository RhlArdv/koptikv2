{{--
    Partial: stok/_badge_stok.blade.php
    Dipakai di stok/index.blade.php per baris menu.

    Props:
    - $stok    : integer stok saat ini
    - $menuId  : id menu (untuk target update JS)
--}}

<div class="badge-stok-container">
    @if($stok == 0)
        <span class="badge-stok inline-flex items-center justify-center min-w-[52px] px-2.5 py-1
                     rounded-full text-xs font-bold bg-red-100 text-red-700">
            Habis
        </span>
    @elseif($stok <= 5)
        <span class="badge-stok inline-flex items-center justify-center min-w-[52px] px-2.5 py-1
                     rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
            {{ $stok }}
        </span>
    @else
        <span class="badge-stok inline-flex items-center justify-center min-w-[52px] px-2.5 py-1
                     rounded-full text-xs font-bold bg-green-100 text-green-700">
            {{ $stok }}
        </span>
    @endif
</div>
@extends('layouts.order-app')

@section('title', 'Pesan Menu')

@section('content')

{{-- HEADER --}}
<header class="order-header">
    <div class="order-header-inner">
        <div class="brand-wrap">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Kopi Titik" class="brand-logo">
            <div class="brand-sep"></div>
            <span class="brand-sub">Pesan dari meja</span>
        </div>
        <svg width="26" height="16" viewBox="0 0 80 48" fill="none" style="opacity:.14;flex-shrink:0;">
            <ellipse cx="40" cy="24" rx="38" ry="22" fill="#6B3F1F"/>
            <path d="M40 2 C36 12 36 36 40 46" stroke="#F8F9FA" stroke-width="3.5" stroke-linecap="round"/>
        </svg>
    </div>
</header>

{{-- BODY --}}
<div class="order-body">

    {{-- Sidebar --}}
    <aside class="order-sidebar">
        <div class="o-card">
            <div class="step-label">
                <div class="step-num">1</div>
                <span class="step-title">Data Kamu</span>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="f-label" for="input-nama">Nama <span class="req">*</span></label>
                    <input class="f-input" type="text" id="input-nama" placeholder="Nama kamu..." maxlength="100" autocomplete="off">
                    <span class="f-error" id="error-nama"></span>
                </div>
                <div class="form-group">
                    <label class="f-label" for="input-meja">No. Meja <span class="req">*</span></label>
                    <input class="f-input" type="text" id="input-meja" placeholder="Contoh: 5" maxlength="20" autocomplete="off">
                    <span class="f-error" id="error-meja"></span>
                </div>
                <div class="form-group full">
                    <label class="f-label" for="input-catatan">Catatan <span class="opt">(opsional)</span></label>
                    <input class="f-input" type="text" id="input-catatan" placeholder="Tidak pakai es, less sugar..." maxlength="300">
                </div>
            </div>
        </div>
        <div class="tip-box">
            💡 <strong>Cara reservasi:</strong> Pilih menu di sebelah kanan, lalu klik <em>Kirim Pesanan</em>. Pembayaran dilakukan di kasir setelah pesanan selesai.
        </div>
    </aside>

    {{-- Menu --}}
    <div>
        <div class="step-label" style="margin-bottom:14px;">
            <div class="step-num">2</div>
            <span class="step-title">Pilih Menu</span>
        </div>

        <div class="cat-tabs">
            <button class="cat-tab active" id="tab-all" onclick="filterKat('all')">Semua</button>
            @foreach($kategoris as $kat)
                <button class="cat-tab" id="tab-{{ $kat->id }}" onclick="filterKat('{{ $kat->id }}')">{{ $kat->nama }}</button>
            @endforeach
        </div>

        @php
            $allMenus = collect([]);
            foreach($kategoris as $kat) {
                foreach($kat->menusAktif as $m) {
                    $m->kategori_nama = $kat->nama;
                    $m->kategori_id   = $kat->id;
                    $allMenus->push($m);
                }
            }
            $allMenus = $allMenus->sortByDesc(fn($m) => $m->stok > 0)->values();
        @endphp

        <div class="menu-grid">
            @foreach($allMenus as $menu)
                @php $habis = $menu->stok <= 0; @endphp
                <div class="m-card {{ $habis ? 'habis' : '' }}" id="card-menu-{{ $menu->id }}" data-kat="{{ $menu->kategori_id }}">
                    <div class="m-img">
                        <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}" loading="lazy" class="{{ $habis ? 'grayscale' : '' }}">
                        @if($habis)<div class="overlay-habis"><span>Habis</span></div>@endif
                        <div class="qty-badge" id="badge-qty-{{ $menu->id }}"></div>
                    </div>
                    <div class="m-body">
                        <span class="m-cat">{{ $menu->kategori_nama }}</span>
                        <p class="m-name">{{ $menu->nama }}</p>
                        @if($menu->deskripsi)<p class="m-desc">{{ $menu->deskripsi }}</p>@endif
                        <p class="m-price">{{ $menu->harga_format }}</p>
                        @if(!$habis)
                            <button class="btn-add" id="btn-tambah-{{ $menu->id }}"
                                    onclick="addToCart({{ $menu->id }}, @js($menu->nama), {{ $menu->harga }}, {{ $menu->stok }})">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 5v14M5 12h14"/>
                                </svg>
                                Tambah
                            </button>
                            <div class="stepper" id="stepper-{{ $menu->id }}">
                                <button class="s-btn" onclick="decCart({{ $menu->id }})">−</button>
                                <span class="s-val" id="qty-display-{{ $menu->id }}">1</span>
                                <button class="s-btn" onclick="incCart({{ $menu->id }}, {{ $menu->stok }})">+</button>
                            </div>
                        @else
                            <button class="btn-sold" disabled>Habis</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- FLOATING CART --}}
<div class="floating-cart" id="floating-cart">
    <button class="fc-btn" onclick="openCart()">
        <div class="fc-left">
            <div class="fc-icon">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="fc-label"><strong id="cart-count">0</strong> item dipilih</span>
        </div>
        <div class="fc-right">
            <span class="fc-total" id="cart-total-float">Rp 0</span>
            <svg width="14" height="14" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </button>
</div>

{{-- MODAL CART --}}
<div class="modal-overlay" id="modal-cart" onclick="if(event.target===this)closeCart()">
    <div class="modal-sheet">
        <div class="m-handle"><div class="m-handle-bar"></div></div>
        <div class="m-head">
            <span class="m-title">Ringkasan Pesanan</span>
            <button class="m-close" onclick="closeCart()">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="m-body">
            <div class="pelanggan-chip">
                <div class="p-chip-item">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <strong id="m-nama">—</strong>
                </div>
                <div class="p-chip-item">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="16" rx="2" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-width="2" d="M8 4v4M16 4v4M3 10h18"/>
                    </svg>
                    Meja <strong id="m-meja">—</strong>
                </div>
            </div>
            <div class="catatan-chip" id="catatan-chip">📝 <span id="m-catatan"></span></div>
            <div id="cart-list"></div>
        </div>
        <div class="m-foot">
            <div class="total-row">
                <span class="total-label">Total Pembayaran</span>
                <span class="total-value" id="cart-total-modal">Rp 0</span>
            </div>
            <div class="err-msg" id="err-submit"></div>
            <button class="btn-submit" id="btn-pesan" onclick="submitOrder()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 2L11 13M22 2L15 22L11 13L2 9L22 2Z"/>
                </svg>
                Kirim Pesanan
            </button>
            <p class="pay-note">Pembayaran di kasir setelah pesanan selesai</p>
        </div>
    </div>
</div>

{{-- MODAL SUKSES --}}
<div class="modal-center" id="modal-sukses">
    <div class="modal-center-box">
        <div class="sukses-icon">
            <svg width="26" height="26" fill="none" stroke="#10B981" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 class="sukses-title">Pesanan Terkirim!</h3>
        <p class="sukses-text">Pesanan kamu sudah diterima dan sedang diproses.</p>
        <p class="sukses-note">Silakan tunggu di meja. Pembayaran di kasir.</p>
        <button class="btn-lagi" onclick="resetOrder()">Pesan Lagi</button>
    </div>
</div>

@endsection

@push('scripts')
    {{-- Expose route URL ke JS --}}
    <script>window.ORDER_STORE_URL = '{{ route('order.store') }}';</script>
    @vite('resources/js/order.js')
@endpush
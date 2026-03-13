const CART_KEY = 'kopititik_cart_v2';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
let cart = [];

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
    try {
        const saved = localStorage.getItem(CART_KEY);
        if (saved) {
            cart = JSON.parse(saved);
            cart.forEach(i => showStepper(i.menu_id, i.qty));
            syncFloat();
        }
    } catch { cart = []; }

    ['input-nama', 'input-meja'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function () {
            this.classList.remove('err');
            const e = document.getElementById('error-' + id.replace('input-', ''));
            if (e) e.classList.remove('show');
        });
    });
});

/* ── Storage ── */
function save() { localStorage.setItem(CART_KEY, JSON.stringify(cart)); }

/* ── Cart actions ── */
window.addToCart = function(id, nama, harga, stok) {
    const ex = cart.find(i => i.menu_id === id);
    if (ex) { if (ex.qty < stok) ex.qty++; }
    else cart.push({ menu_id: id, nama, harga, qty: 1, stok });
    save();
    showStepper(id, cart.find(i => i.menu_id === id).qty);
    syncFloat();
}

window.incCart = function(id, stok) {
    const item = cart.find(i => i.menu_id === id);
    if (!item || item.qty >= stok) return;
    item.qty++; save(); setCardQty(id, item.qty); syncFloat();
}

window.decCart = function(id) {
    const idx = cart.findIndex(i => i.menu_id === id);
    if (idx < 0) return;
    cart[idx].qty--;
    if (cart[idx].qty <= 0) { cart.splice(idx, 1); hideStepper(id); }
    else setCardQty(id, cart[idx].qty);
    save(); syncFloat();
}

/* ── Stepper UI ── */
function showStepper(id, qty) {
    const btn = g('btn-tambah-' + id), st = g('stepper-' + id);
    const b = g('badge-qty-' + id),   v  = g('qty-display-' + id);
    if (btn) btn.style.display = 'none';
    if (st)  st.classList.add('show');
    if (v)   v.textContent = qty;
    if (b)  { b.textContent = qty; b.classList.add('show'); }
}

function hideStepper(id) {
    const btn = g('btn-tambah-' + id), st = g('stepper-' + id), b = g('badge-qty-' + id);
    if (btn) btn.style.display = '';
    if (st)  st.classList.remove('show');
    if (b)   b.classList.remove('show');
}

function setCardQty(id, qty) {
    const v = g('qty-display-' + id), b = g('badge-qty-' + id);
    if (v) v.textContent = qty;
    if (b) b.textContent = qty;
}

/* ── Floating cart sync ── */
function syncFloat() {
    const tq = cart.reduce((s, i) => s + i.qty, 0);
    const th = cart.reduce((s, i) => s + i.harga * i.qty, 0);
    g('cart-count').textContent       = tq;
    g('cart-total-float').textContent = rp(th);
    g('cart-total-modal').textContent = rp(th);
    tq > 0 ? g('floating-cart').classList.add('show') : g('floating-cart').classList.remove('show');
}

/* ── Modal cart ── */
window.openCart = function() {
    g('m-nama').textContent = val('input-nama') || '(belum diisi)';
    g('m-meja').textContent = val('input-meja') || '(belum diisi)';
    const cat = val('input-catatan'), cEl = g('catatan-chip');
    if (cat) { g('m-catatan').textContent = cat; cEl.classList.add('show'); }
    else cEl.classList.remove('show');
    renderCart();
    g('modal-cart').classList.add('show');
}

window.closeCart = function() { g('modal-cart').classList.remove('show'); }

function renderCart() {
    g('cart-list').innerHTML = cart.map(item => `
        <div class="ci-row" id="ci-row-${item.menu_id}">
            <div class="ci-info">
                <div class="ci-name">${esc(item.nama)}</div>
                <div class="ci-price">${rp(item.harga)} / porsi</div>
            </div>
            <div class="ci-stepper">
                <button class="ci-s-btn" onclick="modalDec(${item.menu_id})">−</button>
                <span class="ci-s-val" id="mq-${item.menu_id}">${item.qty}</span>
                <button class="ci-s-btn" onclick="modalInc(${item.menu_id})">+</button>
            </div>
            <div class="ci-subtotal" id="ms-${item.menu_id}">${rp(item.harga * item.qty)}</div>
        </div>
    `).join('');
}

window.modalInc = function(id) {
    const item = cart.find(i => i.menu_id === id);
    if (!item || item.qty >= item.stok) return;
    item.qty++; save();
    g('mq-' + id).textContent = item.qty;
    g('ms-' + id).textContent = rp(item.harga * item.qty);
    setCardQty(id, item.qty); syncFloat();
}

window.modalDec = function(id) {
    const idx = cart.findIndex(i => i.menu_id === id);
    if (idx < 0) return;
    cart[idx].qty--;
    if (cart[idx].qty <= 0) {
        cart.splice(idx, 1); save(); hideStepper(id);
        g('ci-row-' + id)?.remove();
        syncFloat();
        if (cart.length === 0) closeCart();
    } else {
        save();
        g('mq-' + id).textContent = cart[idx].qty;
        g('ms-' + id).textContent = rp(cart[idx].harga * cart[idx].qty);
        setCardQty(id, cart[idx].qty); syncFloat();
    }
}

/* ── Submit ── */
window.submitOrder = function() {
    const nama = val('input-nama'), meja = val('input-meja');
    const catatan = val('input-catatan');
    const errEl = g('err-submit'), btn = g('btn-pesan');
    errEl.classList.remove('show');

    if (!nama)        { showErr(errEl, 'Nama harus diisi.');        closeCart(); focusErr('input-nama', 'error-nama', 'Nama wajib diisi.'); return; }
    if (!meja)        { showErr(errEl, 'Nomor meja harus diisi.');  closeCart(); focusErr('input-meja', 'error-meja', 'Nomor meja wajib diisi.'); return; }
    if (!cart.length) { showErr(errEl, 'Keranjang masih kosong.'); return; }

    btn.disabled = true;
    btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" style="animation:spin 1s linear infinite;flex-shrink:0"><circle cx="12" cy="12" r="10" stroke-opacity=".25" stroke-width="3"/><path d="M12 2a10 10 0 0 1 10 10" stroke-width="3" stroke-linecap="round"/></svg> Mengirim...`;

    fetch(window.ORDER_STORE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({
            nama_pelanggan: nama, nomor_meja: meja, catatan: catatan || null,
            items: cart.map(i => ({ menu_id: i.menu_id, qty: i.qty, catatan_item: null })),
        }),
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) { closeCart(); g('modal-sukses').classList.add('show'); }
            else showErr(errEl, data.message || 'Terjadi kesalahan.');
        })
        .catch(() => showErr(errEl, 'Gagal terhubung ke server.'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 2L11 13M22 2L15 22L11 13L2 9L22 2Z"/></svg> Kirim Pesanan`;
        });
}

/* ── Reset ── */
window.resetOrder = function() {
    cart = [];
    localStorage.removeItem(CART_KEY);
    document.querySelectorAll('[id^="stepper-"]').forEach(s => hideStepper(s.id.replace('stepper-', '')));
    ['input-nama', 'input-meja', 'input-catatan'].forEach(id => { const e = g(id); if (e) e.value = ''; });
    syncFloat();
    g('modal-sukses').classList.remove('show');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* ── Filter kategori ── */
window.filterKat = function(id) {
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
    g('tab-' + id)?.classList.add('active');
    document.querySelectorAll('.m-card').forEach(c => {
        c.style.display = (id === 'all' || c.dataset.kat === String(id)) ? '' : 'none';
    });
}

/* ── Helpers ── */
function rp(n)   { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
function esc(s)  { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
function val(id) { return document.getElementById(id)?.value.trim() ?? ''; }
function g(id)   { return document.getElementById(id); }
function showErr(el, msg) { el.textContent = msg; el.classList.add('show'); }
function focusErr(iId, eId, msg) {
    const i = g(iId), e = g(eId);
    if (i) { i.classList.add('err'); i.focus(); i.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    if (e) { e.textContent = msg; e.classList.add('show'); }
}

@extends('layouts.app')
@section('title','Transaksi Baru')
@section('page-icon','fa-cash-register')
@section('page-title','Transaksi Baru')

@section('content')
<form method="POST" action="{{ route('transaksi.store') }}" id="formTransaksi">
@csrf

<div class="transaksi-layout">

    {{-- ========================================================
         KIRI: Pilih Menu
         ======================================================== --}}
    <div class="grid-card">
        <div class="grid-card-header">
            <div class="grid-card-title">
                <i class="fas fa-utensils"></i>
                <h3>Pilih Menu</h3>
            </div>
        </div>

        @foreach($kategoris as $kat)
            @if($kat->menuAktif->count() > 0)

            <div class="menu-category-label">
                <i class="fas fa-{{ $kat->icon ?? 'tag' }}"></i>
                {{ $kat->nama_kategori }}
            </div>

            <div class="menu-items-grid">
                @foreach($kat->menuAktif as $menu)
                <div class="menu-item-card"
                     id="card-{{ $menu->id_menu }}"
                     onclick="addToCart({{ $menu->id_menu }}, '{{ addslashes($menu->nama_menu) }}', {{ $menu->harga }}, {{ $menu->stok }})">
                    <div class="menu-item-icon">
                        <i class="fas fa-{{ $kat->icon ?? 'coffee' }}"></i>
                    </div>
                    <div class="menu-item-name">{{ $menu->nama_menu }}</div>
                    <div class="menu-item-price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                    <div class="menu-item-stock">Stok: {{ $menu->stok }}</div>
                </div>
                @endforeach
            </div>

            @endif
        @endforeach
    </div>

    {{-- ========================================================
         KANAN: Keranjang
         ======================================================== --}}
    <div class="cart-sticky">
    <div class="grid-card">

        <div class="grid-card-header">
            <div class="grid-card-title">
                <i class="fas fa-shopping-cart"></i>
                <h3>Keranjang</h3>
            </div>
            <button type="button" onclick="clearCart()" class="action-btn action-btn-danger btn-sm">
                Kosongkan
            </button>
        </div>

        {{-- Cart items list --}}
        <div id="cartItems">
            <p id="emptyCart" class="table-empty" style="padding:24px 0;">Keranjang kosong</p>
        </div>

        {{-- Summary --}}
        <div class="cart-summary">
            <div class="cart-summary-row">
                <span class="cart-summary-label">Subtotal</span>
                <span class="cart-summary-value" id="subtotalDisplay">Rp 0</span>
            </div>
            <div class="cart-summary-row">
                <span class="cart-summary-label">Pajak (3%)</span>
                <span class="cart-summary-value" id="pajakDisplay">Rp 0</span>
            </div>
            <div class="cart-total-row">
                <span class="cart-total-label">Total</span>
                <span class="cart-total-value" id="totalDisplay">Rp 0</span>
            </div>
        </div>

        {{-- Customer info --}}
        <div class="form-group">
            <label class="form-label">Nama Customer</label>
            <input type="text" name="nama_customer" class="form-control"
                   placeholder="Kosongkan = Walk-in">
        </div>

        <div class="form-group">
            <label class="form-label">No. Meja</label>
            <input type="number" name="no_meja" class="form-control"
                   placeholder="Kosongkan = Takeaway" min="1" max="99">
        </div>

        <div class="form-group">
            <label class="form-label">Catatan</label>
            <input type="text" name="catatan" class="form-control"
                   placeholder="Less sugar, no ice, dll">
        </div>

        {{-- Eco packaging --}}
        <div class="eco-row" onclick="document.getElementById('eco').click()">
            <input type="checkbox" name="eco_packaging" id="eco" value="1"
                   onclick="event.stopPropagation()">
            <label for="eco">🌿 Eco Packaging (SDGS)</label>
        </div>

        {{-- Hidden inputs for cart items --}}
        <div id="itemsInput"></div>

        {{-- Submit --}}
        <button type="submit" id="btnCheckout"
                class="action-btn action-btn-primary checkout-btn" disabled>
            <i class="fas fa-check-circle"></i> Proses Transaksi
        </button>

    </div>
    </div>

</div>
</form>

@push('scripts')
<script>
let cart   = {};
const stokMap = {};

function formatRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function addToCart(id, nama, harga, stok) {
    stokMap[id] = stok;
    if (!cart[id]) cart[id] = { nama, harga, qty: 0 };

    if (cart[id].qty >= stok) {
        alert('Stok ' + nama + ' tidak cukup!');
        return;
    }

    cart[id].qty++;
    updateCardState(id);
    renderCart();
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;

    if (cart[id].qty <= 0) {
        delete cart[id];
    }

    updateCardState(id);
    renderCart();
}

function clearCart() {
    const ids = Object.keys(cart);
    cart = {};
    ids.forEach(id => updateCardState(id));
    renderCart();
}

/* Toggle .selected class on menu item card */
function updateCardState(id) {
    const card = document.getElementById('card-' + id);
    if (!card) return;
    if (cart[id] && cart[id].qty > 0) {
        card.classList.add('selected');
    } else {
        card.classList.remove('selected');
    }
}

function renderCart() {
    const container  = document.getElementById('cartItems');
    const itemsInput = document.getElementById('itemsInput');
    const btn        = document.getElementById('btnCheckout');

    const keys = Object.keys(cart);

    if (keys.length === 0) {
        container.innerHTML = '<p class="table-empty" style="padding:24px 0;">Keranjang kosong</p>';
        itemsInput.innerHTML = '';
        btn.disabled = true;
        document.getElementById('subtotalDisplay').textContent = 'Rp 0';
        document.getElementById('pajakDisplay').textContent    = 'Rp 0';
        document.getElementById('totalDisplay').textContent    = 'Rp 0';
        return;
    }

    let html     = '';
    let inputs   = '';
    let subtotal = 0;
    let i        = 0;

    keys.forEach(id => {
        const item = cart[id];
        const sub  = item.harga * item.qty;
        subtotal  += sub;

        html += `
        <div class="cart-item">
            <div class="cart-item-info">
                <div class="cart-item-name">${item.nama}</div>
                <div class="cart-item-sub">${formatRp(sub)}</div>
            </div>
            <div class="cart-qty-control">
                <button type="button" class="qty-btn" onclick="changeQty(${id}, -1)">−</button>
                <span class="qty-value">${item.qty}</span>
                <button type="button" class="qty-btn" onclick="changeQty(${id}, 1)">+</button>
            </div>
        </div>`;

        inputs += `<input type="hidden" name="items[${i}][id_menu]" value="${id}">`;
        inputs += `<input type="hidden" name="items[${i}][qty]"     value="${item.qty}">`;
        i++;
    });

    const pajak = Math.round(subtotal * 0.03);
    const total = subtotal + pajak;

    container.innerHTML = html;
    itemsInput.innerHTML = inputs;
    btn.disabled = false;

    document.getElementById('subtotalDisplay').textContent = formatRp(subtotal);
    document.getElementById('pajakDisplay').textContent    = formatRp(pajak);
    document.getElementById('totalDisplay').textContent    = formatRp(total);
}
</script>
@endpush

@endsection
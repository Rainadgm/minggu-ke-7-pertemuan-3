<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Kasir | MiniMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .checkout-confirm-modal .modal-content {
            border: 0;
            border-radius: 0.9rem;
            box-shadow: 0 1.25rem 3rem rgb(15 23 42 / 20%);
        }

        .checkout-confirm-modal .modal-body {
            padding: 1.75rem 1.5rem 1rem;
        }

        .checkout-confirm-icon {
            width: 3rem;
            height: 3rem;
            color: #a83232;
            background: #fae9e3;
        }

        .checkout-confirm-summary {
            border: 1px solid #f0dfe0;
            border-radius: 0.65rem;
            background: #fffafa;
        }

        .checkout-confirm-total {
            color: #8e2929;
        }

        .checkout-confirm-modal .btn-confirm,
        .checkout-success-modal .btn-confirm {
            color: #fff;
            background: #b83c3d;
            border-color: #b83c3d;
        }

        .checkout-confirm-modal .btn-confirm:hover,
        .checkout-success-modal .btn-confirm:hover {
            color: #fff;
            background: #9e3031;
            border-color: #9e3031;
        }

        .checkout-success-modal .modal-content {
            overflow: hidden;
            border: 0;
            border-radius: 0.9rem;
            box-shadow: 0 1.25rem 3rem rgb(15 23 42 / 20%);
        }

        .checkout-success-modal .modal-body {
            padding: 1.75rem 1.5rem 1rem;
        }

        .checkout-success-icon {
            width: 3rem;
            height: 3rem;
            color: #a83232;
            background: #fae9e3;
        }

        .checkout-success-summary {
            border: 1px solid #f0dfe0;
            border-radius: 0.65rem;
            background: #fffafa;
        }

        .checkout-success-total {
            color: #8e2929;
        }

        .checkout-success-modal .modal-footer {
            background: #fffafa;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('kasir.dashboard') }}">MiniMarket</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-light btn-sm" type="submit">Keluar</button>
            </form>
        </div>
    </nav>

    <main class="container py-5">
        <div class="mb-4">
            <p class="text-uppercase text-success fw-bold mb-1">Area Kasir</p>
            <h1 class="h2">Dashboard Kasir</h1>
            <p class="text-secondary mb-0">Selamat datang, {{ Auth::user()->name }}.</p>
        </div>

        <section class="bg-white border rounded-3 p-4" aria-labelledby="account-heading">
            <h2 class="h5" id="account-heading">Informasi akun</h2>
            <dl class="row mb-0">
                <dt class="col-sm-3">Nama</dt>
                <dd class="col-sm-9">{{ Auth::user()->name }}</dd>
                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ Auth::user()->email }}</dd>
                <dt class="col-sm-3">Role</dt>
                <dd class="col-sm-9 mb-0">{{ Auth::user()->role }}</dd>
            </dl>
        </section>

        <section class="mt-4" aria-labelledby="cashier-products-heading">
            <div class="mb-3">
                <h2 class="h5 mb-1" id="cashier-products-heading">Produk tersedia</h2>
                <p class="text-secondary mb-0">Pilih produk untuk memasukkannya ke keranjang.</p>
            </div>
            @if ($products->isEmpty())
                <div class="bg-white border rounded-3 p-4 text-secondary">Belum ada produk aktif untuk dijual.</div>
            @else
                <div class="row g-3">
                    @foreach ($products as $product)
                        <div class="col-sm-6 col-lg-4">
                            <article class="bg-white border rounded-3 p-3 h-100">
                                <div class="d-flex justify-content-between gap-2">
                                    <h3 class="h6 mb-2">{{ $product->name }}</h3>
                                    <span class="badge {{ $product->stock > 0 ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $product->stock > 0 ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </div>
                                <p class="small text-secondary mb-2">{{ $product->category ?: 'Umum' }}</p>
                                <p class="fw-bold mb-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="small text-secondary mb-0">Stok: <span data-product-stock="{{ $product->id }}">{{ $product->stock }}</span></p>
                                    <button
                                        class="btn btn-sm btn-success add-to-cart"
                                        type="button"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-product-price="{{ $product->price }}"
                                        data-product-stock="{{ $product->stock }}"
                                        @disabled($product->stock < 1)
                                    >
                                        {{ $product->stock > 0 ? 'Pilih barang' : 'Stok habis' }}
                                    </button>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="bg-white border rounded-3 p-4 mt-4" aria-labelledby="cart-heading">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1" id="cart-heading">Keranjang belanja</h2>
                    <p class="small text-secondary mb-0"><span id="cart-count">0</span> barang dipilih</p>
                </div>
                <button class="btn btn-sm btn-outline-danger" type="button" id="clear-cart" disabled>Kosongkan</button>
            </div>
            <div id="cart-items" class="d-grid gap-2">
                <p class="text-secondary mb-0" id="empty-cart">Belum ada barang di keranjang.</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                <span>Total</span>
                <span id="cart-total">Rp 0</span>
            </div>
            <div class="row g-3 align-items-end">
                <div class="col-sm-6">
                    <label class="form-label" for="cash-received">Uang diterima</label>
                    <input class="form-control" type="number" id="cash-received" min="0" step="0.01" placeholder="Masukkan pembayaran" disabled>
                    <div class="form-text" id="change-preview">Kembalian: Rp 0</div>
                </div>
                <div class="col-sm-6 d-grid">
                    <button class="btn btn-primary btn-lg" type="button" id="checkout" disabled>Checkout</button>
                </div>
            </div>
            <div class="small mt-3" id="checkout-status" role="status" aria-live="polite"></div>
            <div class="alert alert-success mt-3 mb-0" id="receipt-summary" hidden aria-live="polite"></div>
        </section>

        <section class="bg-white border rounded-3 p-4 mt-4" aria-labelledby="printer-heading">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div>
                    <h2 class="h5 mb-1" id="printer-heading">Printer struk</h2>
                    <p class="text-secondary mb-0" id="selected-printer">Belum ada printer yang dipilih.</p>
                </div>
                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#printerModal">
                    Pilih printer
                </button>
            </div>
        </section>
    </main>

    <div class="modal fade checkout-confirm-modal" id="checkoutConfirmModal" tabindex="-1" aria-labelledby="checkoutConfirmModalLabel" aria-describedby="checkout-confirm-description" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body position-relative text-center">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    <div class="checkout-confirm-icon d-inline-flex align-items-center justify-content-center rounded-4 mb-3" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 3h6v4H9V3Zm-1 11 2 2 5-5" />
                        </svg>
                    </div>
                    <h2 class="h5 fw-bold mb-1" id="checkoutConfirmModalLabel">Konfirmasi Pembayaran</h2>
                    <p class="small text-secondary mb-3" id="checkout-confirm-description">Pastikan jumlah pembayaran sudah sesuai sebelum transaksi disimpan.</p>

                    <div class="checkout-confirm-summary p-3 text-start">
                        <div class="d-flex justify-content-between gap-3 small pb-2 mb-2 border-bottom">
                            <span class="text-secondary">Jumlah item</span>
                            <strong id="confirm-item-count">0 item</strong>
                        </div>
                        <div class="d-flex justify-content-between gap-3 small pb-2 mb-2 border-bottom">
                            <span class="checkout-confirm-total fw-semibold">Total belanja</span>
                            <strong class="checkout-confirm-total" id="confirm-total">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between gap-3 small pb-2 mb-2 border-bottom">
                            <span class="text-secondary">Uang dibayar</span>
                            <strong id="confirm-cash">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between gap-3 small">
                            <span class="text-secondary">Kembalian</span>
                            <strong id="confirm-change">Rp 0</strong>
                        </div>
                    </div>
                    <div class="alert alert-danger small text-start mt-3 mb-0 py-2" id="checkout-confirm-error" role="alert" hidden></div>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Periksa Lagi</button>
                    <button type="button" class="btn btn-confirm px-3" id="confirm-checkout">
                        <span class="confirm-checkout-label">Konfirmasi &amp; Bayar</span>
                        <span class="spinner-border spinner-border-sm ms-1" role="status" aria-hidden="true" hidden></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade checkout-success-modal" id="checkoutSuccessModal" tabindex="-1" aria-labelledby="checkoutSuccessModalLabel" aria-describedby="checkout-success-description" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body position-relative text-center">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    <div class="checkout-success-icon d-inline-flex align-items-center justify-content-center rounded-4 mb-2" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h10v18H7zM9 7h6M9 11h6M9 15h6M9 19h6" />
                        </svg>
                    </div>
                    <h2 class="h5 fw-bold mb-1" id="checkoutSuccessModalLabel">Pembayaran Berhasil</h2>
                    <p class="small text-secondary mb-3" id="checkout-success-description">Transaksi sudah tersimpan. Apakah Anda ingin mencetak struk untuk pelanggan?</p>

                    <div class="checkout-success-summary p-3 text-start">
                        <div class="d-flex justify-content-between align-items-start gap-3 small pb-2 mb-2 border-bottom">
                            <span class="text-secondary">No. Invoice</span>
                            <strong class="text-end text-break" id="success-transaction-code"></strong>
                        </div>
                        <div class="d-flex justify-content-between gap-3 small pb-2 mb-2 border-bottom">
                            <span class="checkout-success-total fw-semibold">Total belanja</span>
                            <strong class="checkout-success-total" id="success-total"></strong>
                        </div>
                        <div class="d-flex justify-content-between gap-3 small">
                            <span class="text-secondary">Kembalian</span>
                            <strong id="success-change"></strong>
                        </div>
                    </div>
                    <div class="alert alert-danger small text-start mt-3 mb-0 py-2" id="checkout-success-error" role="alert" hidden></div>
                </div>
                <div class="modal-footer justify-content-center gap-2 border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Selesai Tanpa Cetak</button>
                    <button type="button" class="btn btn-confirm px-3" id="print-success-receipt">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8V3h10v5M7 17H5a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M7 14h10v7H7z" />
                        </svg>
                        <span class="print-success-label">Cetak Struk</span>
                        <span class="spinner-border spinner-border-sm ms-1" role="status" aria-hidden="true" hidden></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="printerModal" tabindex="-1" aria-labelledby="printerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="printerModalLabel">Pilih printer struk</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary">Pilih printer ESC/POS yang sudah dipasang dan dibagikan di Windows.</p>
                    <label class="form-label" for="printer-select">Printer tersedia</label>
                    <select class="form-select" id="printer-select" disabled>
                        <option value="">Memuat daftar printer...</option>
                    </select>
                    <div class="small mt-3" id="printer-status" role="status" aria-live="polite"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary me-auto" type="button" id="refresh-printers">Muat ulang</button>
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const cart = new Map();
            const cartItems = document.getElementById('cart-items');
            const emptyCart = document.getElementById('empty-cart');
            const cartCount = document.getElementById('cart-count');
            const cartTotal = document.getElementById('cart-total');
            const cashReceived = document.getElementById('cash-received');
            const changePreview = document.getElementById('change-preview');
            const checkoutButton = document.getElementById('checkout');
            const clearCartButton = document.getElementById('clear-cart');
            const checkoutStatus = document.getElementById('checkout-status');
            const receiptSummary = document.getElementById('receipt-summary');
            const savedPrinterKey = 'minimarket.kasir.printer';
            const checkoutConfirmModalElement = document.getElementById('checkoutConfirmModal');
            const checkoutConfirmModal = new bootstrap.Modal(checkoutConfirmModalElement);
            const checkoutSuccessModal = new bootstrap.Modal(document.getElementById('checkoutSuccessModal'));
            const confirmCheckoutButton = document.getElementById('confirm-checkout');
            const confirmCheckoutLabel = confirmCheckoutButton.querySelector('.confirm-checkout-label');
            const confirmCheckoutSpinner = confirmCheckoutButton.querySelector('.spinner-border');
            const confirmCheckoutError = document.getElementById('checkout-confirm-error');
            const successPrintButton = document.getElementById('print-success-receipt');
            const successPrintLabel = successPrintButton.querySelector('.print-success-label');
            const successPrintSpinner = successPrintButton.querySelector('.spinner-border');
            const successPrintError = document.getElementById('checkout-success-error');
            let lastTransactionId = null;
            const rupiah = amount => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }).format(amount);
            const getTotal = () => [...cart.values()].reduce((sum, item) => sum + item.priceCents * item.quantity, 0);

            const printTransactionReceipt = async (statusElement, button) => {
                const savedPrinter = localStorage.getItem(savedPrinterKey);
                if (!savedPrinter) {
                    statusElement.textContent = 'Pilih printer melalui tombol Pilih printer terlebih dahulu.';
                    statusElement.className = 'small mt-2 text-danger';
                    return false;
                }

                button.disabled = true;
                statusElement.textContent = 'Mengirim struk transaksi ke printer...';
                statusElement.className = 'small mt-2 text-secondary';

                try {
                    const printResponse = await fetch(@json(route('kasir.transactions.receipt', ['transaction' => '__TRANSACTION__'])).replace('__TRANSACTION__', String(lastTransactionId)), {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ printer: savedPrinter }),
                    });
                    const printResult = await printResponse.json();
                    if (!printResponse.ok) {
                        throw new Error(printResult.message || printResult.errors?.printer?.[0] || 'Struk transaksi gagal dicetak.');
                    }
                    statusElement.textContent = printResult.message;
                    statusElement.className = 'small mt-2 text-success';
                    return true;
                } catch (error) {
                    statusElement.textContent = error.message || 'Struk transaksi gagal dicetak.';
                    statusElement.className = 'small mt-2 text-danger';
                    return false;
                } finally {
                    button.disabled = false;
                }
            };

            const renderCart = () => {
                cartItems.replaceChildren();
                emptyCart.hidden = cart.size !== 0;

                let count = 0;
                for (const item of cart.values()) {
                    count += item.quantity;
                    const row = document.createElement('div');
                    row.className = 'd-flex flex-wrap justify-content-between align-items-center gap-2 border rounded p-3';

                    const details = document.createElement('div');
                    const name = document.createElement('div');
                    name.className = 'fw-semibold';
                    name.textContent = item.name;
                    const subtotal = document.createElement('div');
                    subtotal.className = 'small text-secondary';
                    subtotal.textContent = `${rupiah(item.priceCents / 100)} × ${item.quantity} = ${rupiah(item.priceCents * item.quantity / 100)}`;
                    details.append(name, subtotal);

                    const controls = document.createElement('div');
                    controls.className = 'd-flex align-items-center gap-2';
                    const decrease = document.createElement('button');
                    decrease.className = 'btn btn-sm btn-outline-secondary';
                    decrease.type = 'button';
                    decrease.textContent = '−';
                    decrease.setAttribute('aria-label', `Kurangi ${item.name}`);
                    decrease.addEventListener('click', () => changeQuantity(item.id, -1));
                    const quantity = document.createElement('span');
                    quantity.className = 'fw-semibold';
                    quantity.textContent = String(item.quantity);
                    const increase = document.createElement('button');
                    increase.className = 'btn btn-sm btn-outline-secondary';
                    increase.type = 'button';
                    increase.textContent = '+';
                    increase.setAttribute('aria-label', `Tambah ${item.name}`);
                    increase.disabled = item.quantity >= item.stock;
                    increase.addEventListener('click', () => changeQuantity(item.id, 1));
                    const remove = document.createElement('button');
                    remove.className = 'btn btn-sm btn-outline-danger';
                    remove.type = 'button';
                    remove.textContent = 'Hapus';
                    remove.addEventListener('click', () => {
                        cart.delete(item.id);
                        renderCart();
                    });
                    controls.append(decrease, quantity, increase, remove);
                    row.append(details, controls);
                    cartItems.append(row);
                }

                const totalCents = getTotal();
                cartCount.textContent = String(count);
                cartTotal.textContent = rupiah(totalCents / 100);
                cashReceived.disabled = cart.size === 0;
                if (cart.size === 0) {
                    cashReceived.value = '';
                }
                clearCartButton.disabled = cart.size === 0;
                checkoutButton.disabled = cart.size === 0 || (Number(cashReceived.value) * 100) < totalCents;
                updateChangePreview();
            };

            const updateChangePreview = () => {
                const total = getTotal();
                const cashCents = Math.round((Number(cashReceived.value) || 0) * 100);
                changePreview.textContent = `Kembalian: ${rupiah(Math.max(0, cashCents - total) / 100)}`;
                checkoutButton.disabled = cart.size === 0 || cashCents < total;
            };

            const changeQuantity = (id, difference) => {
                const item = cart.get(id);
                if (!item) return;
                const quantity = item.quantity + difference;
                if (quantity < 1) {
                    cart.delete(id);
                } else if (quantity <= item.stock) {
                    item.quantity = quantity;
                }
                renderCart();
            };

            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', () => {
                    const id = Number(button.dataset.productId);
                    const item = cart.get(id) || {
                        id,
                        name: button.dataset.productName,
                        priceCents: Math.round(Number(button.dataset.productPrice) * 100),
                        stock: Number(button.dataset.productStock),
                        quantity: 0,
                    };

                    if (item.quantity >= item.stock) {
                        checkoutStatus.textContent = `Stok ${item.name} hanya ${item.stock}.`;
                        checkoutStatus.className = 'small mt-3 text-danger';
                        return;
                    }

                    item.quantity++;
                    cart.set(id, item);
                    checkoutStatus.textContent = '';
                    receiptSummary.hidden = true;
                    renderCart();
                });
            });

            clearCartButton.addEventListener('click', () => {
                cart.clear();
                checkoutStatus.textContent = '';
                renderCart();
            });
            cashReceived.addEventListener('input', updateChangePreview);

            successPrintButton.addEventListener('click', async () => {
                successPrintError.hidden = true;
                successPrintButton.disabled = true;
                successPrintLabel.textContent = 'Mencetak...';
                successPrintSpinner.hidden = false;
                const printed = await printTransactionReceipt(successPrintError, successPrintButton);
                if (printed) {
                    checkoutSuccessModal.hide();
                } else {
                    successPrintError.className = 'alert alert-danger small text-start mt-3 mb-0 py-2';
                    successPrintError.hidden = false;
                }
                successPrintButton.disabled = false;
                successPrintLabel.textContent = 'Cetak Struk';
                successPrintSpinner.hidden = true;
            });

            checkoutButton.addEventListener('click', () => {
                if (cart.size === 0) return;

                const itemCount = [...cart.values()].reduce((count, item) => count + item.quantity, 0);
                const totalCents = getTotal();
                const cashCents = Math.round((Number(cashReceived.value) || 0) * 100);
                document.getElementById('confirm-item-count').textContent = `${itemCount} item`;
                document.getElementById('confirm-total').textContent = rupiah(totalCents / 100);
                document.getElementById('confirm-cash').textContent = rupiah(cashCents / 100);
                document.getElementById('confirm-change').textContent = rupiah(Math.max(0, cashCents - totalCents) / 100);
                confirmCheckoutError.hidden = true;
                checkoutConfirmModal.show();
            });

            confirmCheckoutButton.addEventListener('click', async () => {
                if (cart.size === 0) return;
                confirmCheckoutError.hidden = true;
                confirmCheckoutButton.disabled = true;
                confirmCheckoutLabel.textContent = 'Menyimpan transaksi...';
                confirmCheckoutSpinner.hidden = false;
                checkoutButton.disabled = true;
                checkoutStatus.textContent = 'Menyimpan transaksi dan memperbarui stok...';
                checkoutStatus.className = 'small mt-3 text-secondary';

                try {
                    const response = await fetch(@json(route('kasir.transactions.store')), {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            items: [...cart.values()].map(item => ({
                                product_id: item.id,
                                quantity: item.quantity,
                            })),
                            cash_received: cashReceived.value,
                            printer: localStorage.getItem(savedPrinterKey),
                        }),
                    });
                    const result = await response.json();

                    if (!response.ok) {
                        const validationMessage = Object.values(result.errors || {}).flat()[0];
                        throw new Error(validationMessage || result.message || 'Checkout gagal.');
                    }

                    const sale = result.transaction;
                    const lines = sale.items.map(item =>
                        `${item.name} × ${item.quantity} — ${rupiah(Number(item.line_total))}`
                    );
                    receiptSummary.replaceChildren();
                    const receiptTitle = document.createElement('strong');
                    receiptTitle.textContent = `Transaksi berhasil · ${sale.code}`;
                    const receiptBody = document.createElement('div');
                    receiptBody.className = 'mt-2';
                    receiptBody.textContent = `${lines.join(' | ')} | Total ${rupiah(Number(sale.total))} | Tunai ${rupiah(Number(sale.cash_received))} | Kembalian ${rupiah(Number(sale.change))}`;
                    const receiptStatus = document.createElement('div');
                    receiptStatus.className = `small mt-2 ${sale.receipt.printed ? 'text-success' : 'text-warning-emphasis'}`;
                    receiptStatus.textContent = sale.receipt.message;
                    const printReceiptButton = document.createElement('button');
                    printReceiptButton.className = 'btn btn-sm btn-outline-success mt-3';
                    printReceiptButton.type = 'button';
                    printReceiptButton.textContent = 'Cetak struk transaksi';
                    lastTransactionId = sale.id;
                    printReceiptButton.addEventListener('click', () => printTransactionReceipt(receiptStatus, printReceiptButton));
                    const invoiceLink = document.createElement('a');
                    invoiceLink.className = 'btn btn-sm btn-outline-primary mt-3 ms-2';
                    invoiceLink.href = @json(route('kasir.transactions.invoice', ['transaction' => '__TRANSACTION__'])).replace('__TRANSACTION__', String(sale.id));
                    invoiceLink.textContent = 'Unduh invoice PDF';
                    receiptSummary.append(receiptTitle, receiptBody, receiptStatus, printReceiptButton, invoiceLink);
                    receiptSummary.hidden = false;

                    document.getElementById('success-transaction-code').textContent = sale.code;
                    document.getElementById('success-total').textContent = rupiah(Number(sale.total));
                    document.getElementById('success-change').textContent = rupiah(Number(sale.change));
                    successPrintError.hidden = true;
                    successPrintError.textContent = '';
                    successPrintLabel.textContent = sale.receipt.printed ? 'Cetak Struk Lagi' : 'Cetak Struk';
                    sale.items.forEach(item => {
                        const productButton = document.querySelector(`.add-to-cart[data-product-id="${item.product_id}"]`);
                        if (productButton) {
                            productButton.dataset.productStock = String(item.stock_after);
                            productButton.disabled = item.stock_after < 1;
                            productButton.textContent = item.stock_after > 0 ? 'Pilih barang' : 'Stok habis';
                            document.querySelector(`[data-product-stock="${item.product_id}"]`).textContent = String(item.stock_after);
                        }
                    });

                    cart.clear();
                    cashReceived.value = '';
                    checkoutStatus.textContent = 'Transaksi tersimpan dan stok berhasil diperbarui.';
                    checkoutStatus.className = 'small mt-3 text-success';
                    renderCart();
                    checkoutConfirmModalElement.addEventListener('hidden.bs.modal', () => checkoutSuccessModal.show(), { once: true });
                    checkoutConfirmModal.hide();
                } catch (error) {
                    const message = error.message || 'Checkout gagal.';
                    checkoutStatus.textContent = message;
                    checkoutStatus.className = 'small mt-3 text-danger';
                    confirmCheckoutError.textContent = message;
                    confirmCheckoutError.hidden = false;
                    updateChangePreview();
                } finally {
                    confirmCheckoutButton.disabled = false;
                    confirmCheckoutLabel.textContent = 'Konfirmasi & Bayar';
                    confirmCheckoutSpinner.hidden = true;
                }
            });

            renderCart();

            const printerModal = document.getElementById('printerModal');
            const printerSelect = document.getElementById('printer-select');
            const printerStatus = document.getElementById('printer-status');
            const selectedPrinter = document.getElementById('selected-printer');
            let availablePrinters = [];

            const setStatus = (message, isError = false) => {
                printerStatus.textContent = message;
                printerStatus.className = `small mt-3 ${isError ? 'text-danger' : 'text-secondary'}`;
            };

            const updateSelectedPrinter = () => {
                const printer = availablePrinters.find(item => item.share_name === printerSelect.value);
                selectedPrinter.textContent = printer
                    ? `Printer dipilih: ${printer.name}`
                    : 'Belum ada printer yang dipilih.';

                if (printer) {
                    localStorage.setItem(savedPrinterKey, printer.share_name);
                } else {
                    localStorage.removeItem(savedPrinterKey);
                }
            };

            const loadPrinters = async () => {
                printerSelect.disabled = true;
                printerSelect.replaceChildren(new Option('Memuat daftar printer...', ''));
                setStatus('');

                try {
                    const response = await fetch(@json(route('kasir.printers')), {
                        headers: { Accept: 'application/json' },
                    });
                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(result.message || 'Daftar printer gagal dimuat.');
                    }

                    availablePrinters = result.printers;
                    printerSelect.replaceChildren();

                    if (availablePrinters.length === 0) {
                        printerSelect.add(new Option('Tidak ada printer Windows yang dibagikan', ''));
                        setStatus('Bagikan printer struk di pengaturan Windows, lalu muat ulang.', true);
                        return;
                    }

                    printerSelect.add(new Option('Pilih printer...', ''));
                    availablePrinters.forEach(printer => {
                        printerSelect.add(new Option(printer.name, printer.share_name));
                    });
                    printerSelect.disabled = false;

                    const savedPrinter = localStorage.getItem(savedPrinterKey);
                    if (availablePrinters.some(printer => printer.share_name === savedPrinter)) {
                        printerSelect.value = savedPrinter;
                    }

                    updateSelectedPrinter();
                } catch (error) {
                    printerSelect.replaceChildren(new Option('Daftar printer tidak tersedia', ''));
                    setStatus(error.message || 'Daftar printer gagal dimuat.', true);
                }
            };

            printerModal.addEventListener('show.bs.modal', loadPrinters);
            document.getElementById('refresh-printers').addEventListener('click', loadPrinters);
            printerSelect.addEventListener('change', updateSelectedPrinter);
        })();
    </script>
</body>
</html>
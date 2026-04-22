<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Web</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        (() => {
            let savedTheme = null;

            try {
                savedTheme = localStorage.getItem('kasir-theme');
            } catch (error) {
                savedTheme = null;
            }

            const prefersLight = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
            const theme = savedTheme || (prefersLight ? 'light' : 'dark');

            document.documentElement.dataset.theme = theme;
        })();
    </script>
    <style>
        html[data-theme="light"] {
            color-scheme: light;
        }

        html[data-theme="light"] body {
            background: #f8fafc;
            color: #0f172a;
        }

        html[data-theme="light"] #page-background {
            background: radial-gradient(circle at top, rgba(251, 146, 60, 0.18), transparent 35%), radial-gradient(circle at right, rgba(59, 130, 246, 0.14), transparent 30%), linear-gradient(180deg, #f8fafc 0%, #eef2ff 45%, #e2e8f0 100%);
        }

        html[data-theme="light"] header,
        html[data-theme="light"] #kasir-header-card,
        html[data-theme="light"] #catalog-card,
        html[data-theme="light"] #transactions-card,
        html[data-theme="light"] #cart-sheet,
        html[data-theme="light"] nav.fixed {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(148, 163, 184, 0.35);
            color: #0f172a;
        }

        html[data-theme="light"] header {
            background: rgba(248, 250, 252, 0.96) !important;
        }

        html[data-theme="light"] #kasir-header-card {
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        html[data-theme="light"] #kasir-header-card h1,
        html[data-theme="light"] #kasir-header-card p,
        html[data-theme="light"] #catalog-card h2,
        html[data-theme="light"] #catalog-card p,
        html[data-theme="light"] #transactions-card h2,
        html[data-theme="light"] #transactions-card p,
        html[data-theme="light"] #cart-sheet h2,
        html[data-theme="light"] #cart-sheet p {
            color: #0f172a;
        }

        html[data-theme="light"] #kasir-header-card .inline-flex.items-center.gap-2.rounded-full {
            background: rgba(251, 146, 60, 0.16) !important;
            border-color: rgba(249, 115, 22, 0.22) !important;
            color: #c2410c !important;
        }

        html[data-theme="light"] #kasir-header-card .inline-flex.items-center.gap-2.rounded-full .bg-orange-300 {
            background: #f97316 !important;
        }

        html[data-theme="light"] #kasir-header-card [class*="text-slate-"] ,
        html[data-theme="light"] #catalog-card [class*="text-slate-"] ,
        html[data-theme="light"] #transactions-card [class*="text-slate-"] ,
        html[data-theme="light"] #cart-sheet [class*="text-slate-"] ,
        html[data-theme="light"] #kasir-header-card .text-white:not(button),
        html[data-theme="light"] #catalog-card .text-white:not(button),
        html[data-theme="light"] #transactions-card .text-white:not(button),
        html[data-theme="light"] #cart-sheet .text-white:not(button) {
            color: #0f172a;
        }

        html[data-theme="light"] #catalog-card,
        html[data-theme="light"] #transactions-card,
        html[data-theme="light"] #cart-sheet {
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        html[data-theme="light"] #catalog-card article,
        html[data-theme="light"] #cart-items > div,
        html[data-theme="light"] #cart-summary-card,
        html[data-theme="light"] #transfer-wrapper,
        html[data-theme="light"] #transactions-card .divide-y > div {
            background: rgba(255, 255, 255, 0.92);
            border-color: rgba(148, 163, 184, 0.35);
        }

        html[data-theme="light"] #catalog-card article .bg-slate-900\/80,
        html[data-theme="light"] #cart-items > div .bg-slate-950\/80,
        html[data-theme="light"] #transfer-wrapper .bg-slate-950\/30,
        html[data-theme="light"] #cart-summary-card {
            background: rgba(241, 245, 249, 0.92);
        }

        html[data-theme="light"] #catalog-card .aspect-\[16\/10\],
        html[data-theme="light"] #catalog-card .aspect-\[16\/10\] > div {
            background: #e2e8f0;
        }

        html[data-theme="light"] #catalog-card .aspect-\[16\/10\] .text-slate-500 {
            color: #64748b;
        }

        html[data-theme="light"] #catalog-card .bg-gradient-to-br.from-slate-800.to-slate-900,
        html[data-theme="light"] #catalog-card .bg-slate-900\/80,
        html[data-theme="light"] #catalog-card .bg-slate-800,
        html[data-theme="light"] #catalog-card .bg-slate-900 {
            background: #e2e8f0 !important;
            color: #64748b !important;
        }

        html[data-theme="light"] #search-products,
        html[data-theme="light"] select,
        html[data-theme="light"] #cash-input {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(148, 163, 184, 0.45);
            color: #0f172a;
        }

        html[data-theme="light"] #search-products::placeholder,
        html[data-theme="light"] #cash-input::placeholder {
            color: #94a3b8;
        }

        html[data-theme="light"] #cart-toggle-button {
            box-shadow: 0 16px 35px rgba(249, 115, 22, 0.22);
        }

        html[data-theme="light"] #cart-toggle-button,
        html[data-theme="light"] #theme-toggle-button,
        html[data-theme="light"] #checkout-form button[type="submit"] {
            border-color: rgba(251, 146, 60, 0.22);
        }

        html[data-theme="light"] #cart-toggle-button {
            background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
            color: #fff;
        }

        html[data-theme="light"] #cart-toggle-button:hover {
            background: linear-gradient(135deg, #fdba74 0%, #fb923c 100%);
        }

        html[data-theme="light"] #theme-toggle-button {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(148, 163, 184, 0.4);
            color: #0f172a;
        }

        html[data-theme="light"] #catalog-card article h3,
        html[data-theme="light"] #cart-items h3,
        html[data-theme="light"] #transactions-card .text-white,
        html[data-theme="light"] #transactions-card .text-slate-400,
        html[data-theme="light"] #cart-sheet .text-white,
        html[data-theme="light"] #cart-sheet .text-slate-400,
        html[data-theme="light"] #kasir-header-card .text-white,
        html[data-theme="light"] #kasir-header-card .text-slate-300,
        html[data-theme="light"] #kasir-header-card .text-slate-400 {
            color: #0f172a;
        }

        html[data-theme="light"] #catalog-card article .bg-white\/10,
        html[data-theme="light"] #catalog-card article .bg-slate-900\/80,
        html[data-theme="light"] #cart-items > div .bg-slate-950\/80,
        html[data-theme="light"] #cart-items > div .bg-white\/10,
        html[data-theme="light"] #cart-items > div .border-white\/10,
        html[data-theme="light"] #cart-summary-card,
        html[data-theme="light"] #transfer-wrapper,
        html[data-theme="light"] #payment-method,
        html[data-theme="light"] #cash-input,
        html[data-theme="light"] #search-products {
            box-shadow: none;
        }

        html[data-theme="light"] #category-filters button {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(148, 163, 184, 0.35);
            color: #334155;
        }

        html[data-theme="light"] #category-filters button.bg-\[\#ff7a1a\] {
            background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
            color: #fff;
            border-color: transparent;
        }

        html[data-theme="light"] #product-grid .bg-\[\#ff7a1a\]\/10,
        html[data-theme="light"] #cart-items .text-emerald-200,
        html[data-theme="light"] #transactions-card .text-\[\#ff7a1a\],
        html[data-theme="light"] #cart-summary-card .text-white,
        html[data-theme="light"] #cart-summary-card .text-slate-300 {
            color: #c2410c;
        }

        html[data-theme="light"] #mobile-nav [class*="text-slate-"] {
            color: #475569;
        }

        html[data-theme="light"] nav.fixed > div {
            background: rgba(255, 255, 255, 0.88);
            border-color: rgba(148, 163, 184, 0.3);
            box-shadow: 0 -12px 30px rgba(15, 23, 42, 0.08);
            color: #334155;
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 text-slate-100" data-theme="dark">
    <div class="relative isolate overflow-hidden">
        <div id="page-background" class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(45,212,191,0.18),_transparent_35%),radial-gradient(circle_at_right,_rgba(59,130,246,0.16),_transparent_30%),linear-gradient(180deg,_#020617_0%,_#0f172a_45%,_#111827_100%)]"></div>

        <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur">
            <div class="mx-auto w-full max-w-md px-4 py-4">
                <div id="kasir-header-card" class="relative rounded-[1.75rem] border border-white/10 bg-white/[0.05] px-4 py-4 shadow-2xl shadow-slate-950/20">
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-300/70 to-transparent"></div>
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-2 rounded-full border border-orange-300/20 bg-orange-400/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-orange-100">
                                <span class="h-2 w-2 rounded-full bg-orange-300"></span>
                                Kasir Aktif
                            </div>
                            <h1 class="mt-3 flex items-center gap-2 text-2xl font-semibold tracking-tight text-white">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-300 to-orange-500 text-slate-950 shadow-lg shadow-orange-500/20">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 3h15l-1.5 9h-13z"></path>
                                        <circle cx="9" cy="20" r="1"></circle>
                                        <circle cx="18" cy="20" r="1"></circle>
                                        <path d="M6 3L5 1H2"></path>
                                    </svg>
                                </span>
                                <span>Kasir</span>
                            </h1>
                            <p class="mt-1 truncate text-sm text-slate-300">{{ $outlet->name }}</p>
                            <p class="mt-1 truncate text-xs text-slate-400">{{ auth()->user()->name }}</p>
                        </div>

                        <button id="theme-toggle-button" type="button" class="inline-flex shrink-0 items-center gap-2 rounded-2xl border border-white/10 bg-slate-950/50 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/10">
                            <span id="theme-toggle-icon" class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/10 text-current">
                                <svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                                </svg>
                            </span>
                            <span id="theme-toggle-label">Light</span>
                        </button>
                    </div>

                </div>
            </div>
        </header>

        <main class="mx-auto grid w-full max-w-md min-w-0 gap-6 px-4 py-6 pb-44">
            <section class="min-w-0 space-y-6">
                @if (session('success'))
                    <div class="rounded-3xl border border-emerald-400/30 bg-emerald-500/10 px-5 py-4 text-sm font-medium text-emerald-100">
                        <div>{{ session('success') }}</div>

                        @if (session('receipt'))
                            <div class="mt-4 flex flex-wrap gap-3">
                                <button id="print-receipt-button" type="button" class="inline-flex items-center justify-center rounded-2xl bg-emerald-300 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-200">
                                    Cetak Struk
                                </button>
                                <span class="inline-flex items-center rounded-2xl border border-emerald-300/30 px-3 py-2 text-xs text-emerald-50/90">
                                    Dialog print akan terbuka otomatis jika browser mengizinkan.
                                </span>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($errors->has('checkout'))
                    <div class="rounded-3xl border border-rose-400/30 bg-rose-500/10 px-5 py-4 text-sm font-medium text-rose-100">
                        {{ $errors->first('checkout') }}
                    </div>
                @endif

                <div id="catalog-card" class="rounded-[2rem] border border-white/10 bg-white/5 p-4 shadow-2xl shadow-slate-950/20">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-white">Katalog Produk</h2>
                            <p class="mt-1 text-sm text-slate-400">Tampilan ini difokuskan untuk layar mobile dengan keranjang yang tetap melayang saat halaman discroll.</p>
                        </div>
                        <div class="w-full">
                            <label for="search-products" class="sr-only">Cari produk</label>
                            <input id="search-products" type="search" placeholder="Cari nama produk..." class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white outline-none ring-0 placeholder:text-slate-500 focus:border-orange-300">
                        </div>

                        <div class="w-full min-w-0 overflow-x-auto overflow-y-hidden pb-1 [scrollbar-width:none] [-webkit-overflow-scrolling:touch] [&::-webkit-scrollbar]:hidden">
                            <div id="category-filters" class="flex w-max min-w-full flex-nowrap gap-2 pr-4"></div>
                        </div>
                    </div>

                    <div id="product-grid" class="mt-5 grid grid-cols-2 gap-4"></div>
                    <div id="empty-products" class="mt-5 hidden rounded-3xl border border-dashed border-white/10 px-6 py-12 text-center text-sm text-slate-400">
                        Tidak ada produk yang cocok dengan pencarian.
                    </div>
                </div>

                <div id="transactions-card" class="rounded-[2rem] border border-white/10 bg-white/5 p-4 shadow-2xl shadow-slate-950/20">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-white">Transaksi Terakhir</h2>
                            <p class="mt-1 text-sm text-slate-400">Ringkasan transaksi outlet ini untuk memudahkan pengecekan cepat dari meja kasir.</p>
                        </div>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-3xl border border-white/10 bg-white/5">
                        <div class="hidden grid-cols-[1.2fr_0.7fr_0.7fr_0.7fr] gap-4 bg-white/5 px-5 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 md:grid">
                            <div>Invoice</div>
                            <div>Metode</div>
                            <div>Item</div>
                            <div>Total</div>
                        </div>

                        <div class="divide-y divide-white/10">
                            @forelse ($recentTransactions as $transaction)
                                <div class="px-5 py-4 md:grid md:grid-cols-[minmax(0,1.2fr)_0.7fr_0.7fr_0.7fr] md:items-center md:gap-4">
                                    <div class="min-w-0 flex items-start justify-between gap-4 md:block">
                                        <div class="min-w-0 flex-1">
                                            <div class="block overflow-hidden text-ellipsis whitespace-nowrap text-sm font-semibold leading-5 text-white">{{ $transaction['no_transaksi'] }}</div>
                                            <div class="mt-1 text-xs text-slate-400">{{ $transaction['tanggal'] }}</div>
                                        </div>
                                        <div class="shrink-0 text-right text-sm font-semibold text-[#ff7a1a] md:hidden">
                                            Rp {{ number_format($transaction['total'], 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center gap-2 text-xs md:mt-0 md:block md:text-sm md:text-slate-600">
                                        <span class="rounded-full bg-white/10 px-2.5 py-1 text-slate-300 md:bg-transparent md:px-0 md:py-0">{{ $transaction['metode_pembayaran'] }}</span>
                                        <span class="rounded-full bg-white/10 px-2.5 py-1 text-slate-300 md:hidden">{{ $transaction['jumlah_item'] }} item</span>
                                    </div>

                                    <div class="hidden text-sm text-slate-200 md:block">{{ $transaction['jumlah_item'] }} item</div>
                                    <div class="hidden text-sm font-semibold text-[#ff7a1a] md:block">Rp {{ number_format($transaction['total'], 0, ',', '.') }}</div>
                                </div>
                            @empty
                                <div class="px-5 py-10 text-center text-sm text-slate-400">
                                    Belum ada transaksi untuk outlet ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <aside class="pointer-events-none fixed inset-x-0 bottom-24 z-30 mx-auto w-full max-w-md px-4 pb-4">
                <div class="pointer-events-auto flex justify-center">
                    <button id="cart-toggle-button" type="button" class="inline-flex min-w-[14rem] items-center justify-center gap-2.5 rounded-full bg-[#ff7a1a] px-4.5 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_rgba(249,115,22,0.35)] transition hover:bg-[#ff8c34]">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/15">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="9" cy="20" r="1"></circle>
                                <circle cx="18" cy="20" r="1"></circle>
                                <path d="M3 4h2l2.2 10.2a1 1 0 0 0 1 .8h9.7a1 1 0 0 0 1-.8L21 7H7"></path>
                            </svg>
                        </span>
                        <span id="cart-count" class="hidden rounded-full bg-white/15 px-2 py-1 text-xs font-bold">0</span>
                        <span id="cart-summary">0 item • Rp 0</span>
                    </button>
                </div>
            </aside>

            @include('kasir.partials.mobile-nav', ['active' => 'kasir'])

            <div id="cart-panel" class="pointer-events-none fixed inset-0 z-40 opacity-0 transition duration-200">
                <button id="cart-backdrop" type="button" class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm transition duration-200"></button>

                <div class="absolute inset-0 flex items-end justify-center px-3 py-3 sm:items-center sm:px-4 sm:py-6">
                    <div id="cart-sheet" class="w-full max-w-md translate-y-8 overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/95 shadow-2xl shadow-slate-950/40 backdrop-blur transition duration-200 ease-out max-h-[calc(100dvh-1.5rem)] sm:max-h-[calc(100dvh-3rem)]">
                        <div class="flex max-h-[calc(100dvh-1.5rem)] flex-col p-4 sm:max-h-[calc(100dvh-3rem)]">
                            <div class="mx-auto mb-4 h-1.5 w-14 rounded-full bg-white/15"></div>
                            <div class="flex shrink-0 items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-lg font-semibold text-white">Keranjang</h2>
                                    <p class="mt-1 text-sm text-slate-400">Pilih metode pembayaran dan simpan transaksi saat semua item sudah sesuai.</p>
                                </div>
                                <button id="cart-close-button" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-lg text-white transition hover:bg-white/10">&times;</button>
                            </div>

                            <form id="checkout-form" method="POST" action="{{ route('kasir.store') }}" class="mt-5 flex min-h-0 flex-1 flex-col overflow-hidden">
                            @csrf
                            <div class="min-h-0 flex-1 space-y-5 overflow-y-auto pr-1 pb-4">
                                <div id="cart-items" class="space-y-3"></div>
                                <div id="cart-empty" class="rounded-3xl border border-dashed border-white/10 px-4 py-8 text-center text-sm text-slate-500">
                                    Keranjang masih kosong. Tambahkan produk dari katalog di atas.
                                </div>

                                <div id="cart-summary-card" class="rounded-3xl border border-white/10 bg-white/[0.03] p-4">
                                    <div class="space-y-3 text-sm">
                                        <div class="flex items-center justify-between text-slate-300">
                                            <span>Subtotal</span>
                                            <span id="cart-subtotal">Rp 0</span>
                                        </div>
                                        <div class="flex items-center justify-between text-slate-300">
                                            <span>Total Item</span>
                                            <span id="cart-total-qty">0</span>
                                        </div>
                                        <div class="flex items-center justify-between border-t border-white/10 pt-3 text-base font-semibold text-white">
                                            <span>Total Bayar</span>
                                            <span id="cart-total">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label for="payment-method" class="mb-2 block text-sm font-medium text-slate-300">Metode Pembayaran</label>
                                        <select id="payment-method" name="metode_pembayaran" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none focus:border-emerald-300/60">
                                            @foreach ($paymentMethods as $paymentMethod)
                                                <option value="{{ $paymentMethod->type }}">{{ $paymentMethod->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="transfer-wrapper" class="hidden rounded-3xl border border-orange-300/20 bg-orange-400/10 p-4">
                                        <div class="mb-3 text-sm font-semibold text-white">Informasi Transfer</div>
                                        <div id="transfer-bank-accounts" class="space-y-3"></div>
                                        <p id="transfer-empty" class="hidden text-xs text-orange-100/80">Belum ada rekening transfer yang aktif.</p>
                                    </div>

                                    <div id="cash-wrapper">
                                        <label for="cash-input" class="mb-2 block text-sm font-medium text-slate-300">Nominal Tunai</label>
                                        <input id="cash-input" name="bayar" type="number" min="0" step="0.01" value="0" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none focus:border-emerald-300/60">
                                        <p id="change-indicator" class="mt-2 text-xs text-slate-400">Kembalian akan dihitung otomatis saat nominal tunai cukup.</p>
                                    </div>

                                    <div id="checkout-hidden-items"></div>
                                </div>
                            </div>

                            <div class="shrink-0 border-t border-white/10 pt-4 pb-[env(safe-area-inset-bottom)]">
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300">
                                    Simpan Transaksi
                                </button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const receiptData = @json(session('receipt'));
        const products = @json($products);
        const categoryOptions = @json($categories);
        const bankAccounts = @json($bankAccounts ?? []);
        const themeToggleButton = document.getElementById('theme-toggle-button');
        const themeToggleLabel = document.getElementById('theme-toggle-label');
        const themeToggleIcon = document.getElementById('theme-toggle-icon');
        const currency = new Intl.NumberFormat('id-ID');
        const categories = [
            { id: 'all', nama: 'Semua' },
            ...categoryOptions.map(category => ({
                id: String(category.id),
                nama: category.nama,
            })),
        ];
        const state = {
            query: '',
            cart: {},
            isCartOpen: false,
            activeCategory: 'all',
        };

        const productGrid = document.getElementById('product-grid');
        const emptyProducts = document.getElementById('empty-products');
        const categoryFilters = document.getElementById('category-filters');
        const cartItems = document.getElementById('cart-items');
        const cartEmpty = document.getElementById('cart-empty');
        const cartCount = document.getElementById('cart-count');
        const cartSubtotal = document.getElementById('cart-subtotal');
        const cartTotal = document.getElementById('cart-total');
        const cartTotalQty = document.getElementById('cart-total-qty');
        const cartSummary = document.getElementById('cart-summary');
        const hiddenItems = document.getElementById('checkout-hidden-items');
        const paymentMethod = document.getElementById('payment-method');
        const transferWrapper = document.getElementById('transfer-wrapper');
        const transferBankAccounts = document.getElementById('transfer-bank-accounts');
        const transferEmpty = document.getElementById('transfer-empty');
        const cashWrapper = document.getElementById('cash-wrapper');
        const cashInput = document.getElementById('cash-input');
        const changeIndicator = document.getElementById('change-indicator');
        const checkoutForm = document.getElementById('checkout-form');
        const searchInput = document.getElementById('search-products');
        const cartPanel = document.getElementById('cart-panel');
        const cartSheet = document.getElementById('cart-sheet');
        const cartToggleButton = document.getElementById('cart-toggle-button');
        const cartCloseButton = document.getElementById('cart-close-button');
        const cartBackdrop = document.getElementById('cart-backdrop');
        const printReceiptButton = document.getElementById('print-receipt-button');

        const escapeHtml = value => String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const getTheme = () => document.documentElement.dataset.theme || 'dark';

        const setTheme = theme => {
            const nextTheme = theme === 'light' ? 'light' : 'dark';

            document.documentElement.dataset.theme = nextTheme;
            document.body.dataset.theme = nextTheme;

            try {
                localStorage.setItem('kasir-theme', nextTheme);
            } catch (error) {
                // Ignore storage failures and keep the in-memory theme.
            }

            if (themeToggleLabel) {
                themeToggleLabel.textContent = nextTheme === 'light' ? 'Dark' : 'Light';
            }

            if (themeToggleButton) {
                themeToggleButton.setAttribute('aria-label', nextTheme === 'light' ? 'Ganti ke mode gelap' : 'Ganti ke mode terang');
                themeToggleButton.title = nextTheme === 'light' ? 'Ganti ke mode gelap' : 'Ganti ke mode terang';
            }

            if (themeToggleIcon) {
                themeToggleIcon.innerHTML = nextTheme === 'light'
                    ? `
                        <svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2"></path>
                            <path d="M12 20v2"></path>
                            <path d="m4.93 4.93 1.41 1.41"></path>
                            <path d="m17.66 17.66 1.41 1.41"></path>
                            <path d="M2 12h2"></path>
                            <path d="M20 12h2"></path>
                            <path d="m6.34 17.66-1.41 1.41"></path>
                            <path d="m19.07 4.93-1.41 1.41"></path>
                        </svg>
                    `
                    : `
                        <svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                        </svg>
                    `;
            }
        };

        setTheme(getTheme());

        const formatCurrency = value => `Rp ${currency.format(value || 0)}`;
        const formatReceiptDate = value => {
            if (!value) {
                return '-';
            }

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return new Intl.DateTimeFormat('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            }).format(date);
        };

        const buildReceiptMarkup = receipt => {
            if (!receipt) {
                return '';
            }

            const itemsMarkup = receipt.items.map(item => `
                <tr class="product-row">
                    <td class="product-name-column">
                        <div class="product-name">${escapeHtml(item.nama)}</div>
                        <div class="product-price">${escapeHtml(formatCurrency(item.harga))}</div>
                    </td>
                    <td class="product-qty">x${escapeHtml(item.qty)}</td>
                    <td class="product-subtotal">${escapeHtml(formatCurrency(item.subtotal))}</td>
                </tr>
            `).join('');

            return `
                <!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <title>Struk ${escapeHtml(receipt.transaction_number)}</title>
                    <style>
                        * { box-sizing: border-box; }
                        body { margin: 0; padding: 20px; background: #f1f5f9; color: #0f172a; font-family: Arial, sans-serif; }
                        .receipt-shell { width: 300px; max-width: 100%; margin: 0 auto; }
                        .receipt-card { background: rgba(255,255,255,0.98); border-radius: 24px; padding: 16px; box-shadow: 0 10px 28px rgba(15,23,42,0.08); }
                        .shop-header { text-align: center; margin-bottom: 20px; }
                        .shop-name { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
                        .shop-meta { font-size: 11px; line-height: 1.45; color: #64748b; }
                        .section-block { margin-bottom: 4px; }
                        .section-title { font-size: 13px; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
                        .info-row, .summary-row, .summary-row-last { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
                        .info-row { margin-bottom: 8px; }
                        .info-label, .summary-label { font-size: 11px; color: #64748b; }
                        .info-value { flex: 1; font-size: 11px; font-weight: 700; color: #0f172a; text-align: right; }
                        .divider { border-top: 1px dashed #e2e8f0; margin: 12px 0; }
                        table { width: 100%; border-collapse: collapse; }
                        .product-header-text { font-size: 10px; font-weight: 700; color: #64748b; text-align: right; padding-bottom: 8px; }
                        .product-name-header { text-align: left; }
                        .product-row td { padding: 8px 0; vertical-align: top; }
                        .product-name-column { width: 50%; padding-right: 8px; }
                        .product-name { font-size: 11px; font-weight: 700; color: #0f172a; margin-bottom: 2px; word-break: break-word; }
                        .product-price { font-size: 10px; color: #64748b; }
                        .product-qty { width: 14%; font-size: 10px; font-weight: 700; color: #0f172a; text-align: right; white-space: nowrap; }
                        .product-subtotal { width: 36%; font-size: 10px; font-weight: 800; color: #0f172a; text-align: right; white-space: nowrap; }
                        .summary-row { margin-bottom: 10px; }
                        .summary-row-last { margin-top: 4px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
                        .summary-value { font-size: 11px; font-weight: 700; color: #0f172a; }
                        .summary-highlight-label { font-size: 12px; font-weight: 800; color: #0f766e; }
                        .summary-highlight-value { font-size: 13px; font-weight: 800; color: #0f766e; }
                        .receipt-footer { margin-top: 14px; text-align: center; font-size: 10px; color: #64748b; }
                        @media print {
                            body { padding: 0; background: #fff; }
                            .receipt-shell { width: 100%; }
                            .receipt-card { width: 58mm; max-width: 58mm; border-radius: 0; box-shadow: none; padding: 4mm 3mm; }
                            .shop-name { font-size: 14px; }
                            .shop-meta { font-size: 9px; }
                            .section-title { font-size: 11px; }
                            .info-label, .summary-label, .info-value, .summary-value { font-size: 9px; }
                            .product-header-text, .product-price, .product-qty, .product-subtotal, .receipt-footer { font-size: 8px; }
                            .product-name { font-size: 9px; }
                            .summary-highlight-label, .summary-highlight-value { font-size: 10px; }
                        }
                    </style>
                </head>
                <body>
                    <div class="receipt-shell">
                        <div class="receipt-card">
                            <div class="shop-header">
                                <div class="shop-name">${escapeHtml(receipt.outlet_name)}</div>
                                ${receipt.outlet_address ? `<div class="shop-meta">${escapeHtml(receipt.outlet_address)}</div>` : ''}
                                ${receipt.outlet_phone ? `<div class="shop-meta">Telp: ${escapeHtml(receipt.outlet_phone)}</div>` : ''}
                            </div>

                            <div class="section-block">
                                <div class="section-title">Informasi Pembayaran</div>
                                <div class="info-row">
                                    <div class="info-label">No. Transaksi</div>
                                    <div class="info-value">${escapeHtml(receipt.transaction_number)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Tanggal</div>
                                    <div class="info-value">${escapeHtml(formatReceiptDate(receipt.transaction_date))}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Kasir</div>
                                    <div class="info-value">${escapeHtml(receipt.cashier_name)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Pembayaran</div>
                                    <div class="info-value">${escapeHtml(receipt.payment_method)}</div>
                                </div>
                            </div>

                            <div class="divider"></div>

                            <div class="section-block">
                                <div class="section-title">Detail Produk</div>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="product-header-text product-name-header">Produk</th>
                                            <th class="product-header-text">Qty</th>
                                            <th class="product-header-text">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>${itemsMarkup}</tbody>
                                </table>
                            </div>

                            <div class="divider"></div>

                            <div class="section-block">
                                <div class="section-title">Ringkasan</div>
                                <div class="summary-row">
                                    <div class="summary-label">Total</div>
                                    <div class="summary-value">${escapeHtml(formatCurrency(receipt.total))}</div>
                                </div>
                                <div class="summary-row">
                                    <div class="summary-label">Bayar</div>
                                    <div class="summary-value">${escapeHtml(formatCurrency(receipt.bayar))}</div>
                                </div>
                                <div class="summary-row-last">
                                    <div class="summary-highlight-label">Kembalian</div>
                                    <div class="summary-highlight-value">${escapeHtml(formatCurrency(receipt.kembalian))}</div>
                                </div>
                            </div>

                            <div class="receipt-footer">Terima kasih</div>
                        </div>
                    </div>
                </body>
                </html>
            `;
        };

        const printReceipt = () => {
            if (!receiptData) {
                return;
            }

            const popup = window.open('', '_blank', 'width=420,height=720');

            if (!popup) {
                window.alert('Popup print diblokir browser. Gunakan tombol Cetak Struk lalu izinkan popup.');
                return;
            }

            popup.document.open();
            popup.document.write(buildReceiptMarkup(receiptData));
            popup.document.close();
            popup.focus();

            popup.addEventListener('load', () => {
                popup.print();
            });
        };

        const setCartPanelOpen = isOpen => {
            state.isCartOpen = isOpen;
            cartPanel.classList.toggle('pointer-events-none', !isOpen);
            cartPanel.classList.toggle('opacity-0', !isOpen);
            cartSheet.classList.toggle('translate-y-8', !isOpen);
            document.body.classList.toggle('overflow-hidden', isOpen);
        };

        const currentCartItems = () => Object.values(state.cart);

        const renderCategories = () => {
            categoryFilters.innerHTML = categories.map(category => {
                const isActive = state.activeCategory === category.id;
                const tone = isActive
                    ? 'bg-[#ff7a1a] text-white border-orange-400/50'
                    : 'bg-white/5 text-slate-300 border-white/10';

                return `
                    <button
                        type="button"
                        data-category-id="${escapeHtml(category.id)}"
                        class="shrink-0 snap-start whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition ${tone}"
                    >
                        ${escapeHtml(category.nama)}
                    </button>
                `;
            }).join('');
        };

        const renderProducts = () => {
            const query = state.query.trim().toLowerCase();
            const filteredProducts = products.filter(product => {
                const matchesQuery = product.nama.toLowerCase().includes(query);
                const activeCategory = categories.find(category => category.id === state.activeCategory);
                const productCategory = (product.kategori || '').toLowerCase();
                const selectedCategory = (activeCategory?.nama || '').toLowerCase();
                const matchesCategory = state.activeCategory === 'all' || productCategory === selectedCategory;

                return matchesQuery && matchesCategory;
            });

            emptyProducts.classList.toggle('hidden', filteredProducts.length > 0);
            productGrid.innerHTML = filteredProducts.map(product => {
                const inCartQty = state.cart[product.id]?.qty || 0;
                const isOutOfStock = product.stok <= 0;
                const cardTone = isOutOfStock
                    ? 'border-white/5 bg-white/[0.03] opacity-60'
                    : 'border-white/10 bg-white/[0.05] hover:border-orange-300/40 hover:bg-white/[0.08] hover:shadow-lg hover:shadow-slate-950/20';

                return `
                    <article class="group overflow-hidden rounded-[1.4rem] border ${cardTone} transition">
                        <div class="aspect-[16/10] overflow-hidden bg-slate-900/80">
                            ${product.image_url
                                ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.nama)}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">`
                                : `<div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 text-sm font-medium text-slate-500">Tanpa Gambar</div>`}
                        </div>
                        <div class="space-y-3 p-3.5">
                            <div class="space-y-2">
                                <h3 class="min-h-[2.5rem] line-clamp-2 text-[14px] font-semibold leading-tight text-white">${escapeHtml(product.nama)}</h3>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <span class="inline-flex min-w-0 items-center justify-center rounded-full bg-[#ff7a1a]/10 px-2.5 py-1 text-[13px] font-semibold text-[#ff9a4d]">${formatCurrency(product.harga)}</span>
                                    <span class="inline-flex min-w-0 items-center justify-center rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-medium text-slate-200 whitespace-nowrap">Stok ${product.stok}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-3 pt-1">
                                <button type="button" data-action="decrease" data-id="${product.id}" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-slate-900/80 text-lg text-white transition hover:border-white/20 hover:bg-slate-800">-</button>
                                <div class="min-w-[3rem] text-center text-sm font-semibold text-white">${inCartQty}</div>
                                <button type="button" data-action="increase" data-id="${product.id}" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#ff7a1a] text-lg font-semibold text-white transition hover:bg-[#ff8c34] disabled:cursor-not-allowed disabled:bg-slate-300" ${isOutOfStock ? 'disabled' : ''}>+</button>
                            </div>
                        </div>
                    </article>
                `;
            }).join('');
        };

        const renderCart = () => {
            const items = currentCartItems();
            const totalQty = items.reduce((sum, item) => sum + item.qty, 0);
            const subtotal = items.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            const selectedPaymentMethod = paymentMethod.value || '';
            const normalizedPaymentMethod = selectedPaymentMethod.toLowerCase();
            const isCash = normalizedPaymentMethod === 'tunai';
            const isTransfer = normalizedPaymentMethod.includes('transfer');
            const bayar = Number(cashInput.value || 0);

            cartCount.textContent = String(totalQty);
            cartTotalQty.textContent = String(totalQty);
            cartSubtotal.textContent = formatCurrency(subtotal);
            cartTotal.textContent = formatCurrency(subtotal);
            cartSummary.textContent = `${totalQty} item • ${formatCurrency(subtotal)}`;

            cartEmpty.classList.toggle('hidden', items.length > 0);
            cartItems.innerHTML = items.map(item => `
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-white">${escapeHtml(item.nama)}</h3>
                            <p class="mt-1 text-xs text-slate-400">${formatCurrency(item.harga)} per item</p>
                        </div>
                        <button type="button" data-action="remove" data-id="${item.id}" class="text-xs font-medium text-rose-200 transition hover:text-rose-100">Hapus</button>
                    </div>
                    <div class="mt-4 flex items-center justify-between gap-3">
                        <div class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-slate-950/80 p-1">
                            <button type="button" data-action="decrease" data-id="${item.id}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-base text-white transition hover:bg-white/10">-</button>
                            <span class="min-w-[2rem] text-center text-sm font-semibold text-white">${item.qty}</span>
                            <button type="button" data-action="increase" data-id="${item.id}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-base text-white transition hover:bg-white/10">+</button>
                        </div>
                        <div class="text-sm font-semibold text-emerald-200">${formatCurrency(item.harga * item.qty)}</div>
                    </div>
                </div>
            `).join('');

            hiddenItems.innerHTML = items.map((item, index) => `
                <input type="hidden" name="items[${index}][produk_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
            `).join('');

            transferWrapper.classList.toggle('hidden', !isTransfer);
            transferBankAccounts.innerHTML = isTransfer
                ? bankAccounts.map(account => `
                    <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-3">
                        <div class="text-sm font-semibold text-white">${escapeHtml(account.bank_name)}</div>
                        <div class="mt-2 text-[11px] uppercase tracking-[0.2em] text-orange-100/70">No. Rekening</div>
                        <div class="mt-1 text-sm font-medium text-orange-50">${escapeHtml(account.account_number)}</div>
                        <div class="mt-3 text-[11px] uppercase tracking-[0.2em] text-orange-100/70">Atas Nama</div>
                        <div class="mt-1 text-sm font-medium text-orange-50">${escapeHtml(account.account_holder)}</div>
                        ${account.note ? `<div class="mt-3 text-xs text-orange-100/70">${escapeHtml(account.note)}</div>` : ''}
                    </div>
                `).join('')
                : '';
            transferEmpty.classList.toggle('hidden', !isTransfer || bankAccounts.length > 0);

            cashWrapper.classList.toggle('hidden', !isCash);

            if (!isCash) {
                cashInput.value = subtotal > 0 ? String(subtotal) : '0';
                changeIndicator.textContent = 'Pembayaran non-tunai akan otomatis mengikuti total transaksi.';
                return;
            }

            if (bayar >= subtotal) {
                changeIndicator.textContent = `Kembalian: ${formatCurrency(bayar - subtotal)}`;
            } else {
                changeIndicator.textContent = 'Nominal tunai masih kurang dari total transaksi.';
            }
        };

        const updateItem = (productId, nextQty) => {
            const product = products.find(item => item.id === Number(productId));

            if (!product) {
                return;
            }

            if (nextQty <= 0) {
                delete state.cart[product.id];
            } else {
                state.cart[product.id] = {
                    id: product.id,
                    nama: product.nama,
                    harga: Number(product.harga),
                    stok: Number(product.stok),
                    qty: Math.min(nextQty, Number(product.stok)),
                };
            }

            renderProducts();
            renderCart();
        };

        document.addEventListener('click', event => {
            const categoryTrigger = event.target.closest('[data-category-id]');

            if (categoryTrigger) {
                state.activeCategory = categoryTrigger.dataset.categoryId || 'all';
                renderCategories();
                renderProducts();
                return;
            }

            const trigger = event.target.closest('[data-action]');

            if (!trigger) {
                return;
            }

            const { action, id } = trigger.dataset;
            const currentQty = state.cart[id]?.qty || 0;

            if (action === 'increase') {
                updateItem(id, currentQty + 1);
            }

            if (action === 'decrease') {
                updateItem(id, currentQty - 1);
            }

            if (action === 'remove') {
                updateItem(id, 0);
            }
        });

        searchInput.addEventListener('input', event => {
            state.query = event.target.value || '';
            renderProducts();
        });

        paymentMethod.addEventListener('change', renderCart);
        cashInput.addEventListener('input', renderCart);
        if (themeToggleButton) {
            themeToggleButton.addEventListener('click', () => {
                setTheme(getTheme() === 'light' ? 'dark' : 'light');
            });
        }
        cartToggleButton.addEventListener('click', () => setCartPanelOpen(true));
        cartCloseButton.addEventListener('click', () => setCartPanelOpen(false));
        cartBackdrop.addEventListener('click', () => setCartPanelOpen(false));
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && state.isCartOpen) {
                setCartPanelOpen(false);
            }
        });

        checkoutForm.addEventListener('submit', event => {
            const items = currentCartItems();
            const total = items.reduce((sum, item) => sum + (item.harga * item.qty), 0);

            if (items.length === 0) {
                event.preventDefault();
                window.alert('Keranjang masih kosong. Tambahkan produk terlebih dahulu.');
                return;
            }

            if (paymentMethod.value === 'Tunai' && Number(cashInput.value || 0) < total) {
                event.preventDefault();
                window.alert('Nominal tunai masih kurang dari total transaksi.');
            }
        });

        if (printReceiptButton) {
            printReceiptButton.addEventListener('click', printReceipt);
        }

        renderCategories();
        renderProducts();
        renderCart();

        if (receiptData) {
            window.setTimeout(printReceipt, 300);
        }
    </script>
</body>
</html>

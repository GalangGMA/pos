<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('kasir.partials.theme-head')
    @include('kasir.partials.theme-style')
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 text-slate-100">
    <div class="relative isolate overflow-hidden">
        <div id="page-background" class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(45,212,191,0.18),_transparent_35%),radial-gradient(circle_at_right,_rgba(59,130,246,0.16),_transparent_30%),linear-gradient(180deg,_#020617_0%,_#0f172a_45%,_#111827_100%)]"></div>

        <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur">
            <div class="mx-auto flex w-full max-w-md items-center justify-between gap-3 px-4 py-5">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-300/80">Kasir Mobile</p>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-white">Beranda</h1>
                    <p class="mt-1 truncate text-sm text-slate-300">{{ $outlet->name }}</p>
                </div>

                <div class="flex items-center gap-2">
                    @include('kasir.partials.theme-toggle')
                    <form method="POST" action="{{ route('kasir.logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 text-sm font-semibold text-slate-200 transition hover:bg-white/10">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-md space-y-6 px-4 py-6 pb-28">
            <section class="space-y-3">
                <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                    <p class="text-sm text-slate-300">Pendapatan Hari Ini</p>
                    <h2 class="mt-3 text-2xl font-semibold text-white">Rp {{ number_format($todaySummary['omzet_harian'], 0, ',', '.') }}</h2>
                    <p class="mt-2 text-xs text-slate-400">Omzet masuk di outlet aktif.</p>
                </div>
                <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm text-slate-300">Transaksi Hari Ini</p>
                            <h2 class="mt-3 text-2xl font-semibold text-white">{{ number_format($todaySummary['total_transaksi']) }}</h2>
                            <p class="mt-2 text-xs text-slate-400">Jumlah transaksi yang tercatat hari ini.</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-3 py-2 text-right text-[11px] text-emerald-100">
                            <div class="font-semibold">{{ now()->translatedFormat('d M Y') }}</div>
                            <div class="text-emerald-200/80">Aktif</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-2 gap-3">
                <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Produk Siap</div>
                    <div class="mt-3 text-2xl font-semibold text-white">{{ number_format($availableProducts) }}</div>
                </div>
                <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Stok Tipis</div>
                    <div class="mt-3 text-2xl font-semibold text-orange-300">{{ number_format($lowStockProducts) }}</div>
                </div>
                <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Stok Habis</div>
                    <div class="mt-3 text-2xl font-semibold text-rose-300">{{ number_format($emptyStockProducts) }}</div>
                </div>
                <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Outlet</div>
                    <div class="mt-3 truncate text-lg font-semibold text-white">{{ $outlet->code }}</div>
                </div>
            </section>

            <section class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Akses Cepat</h2>
                    <p class="text-sm text-slate-400">Pilih menu utama</p>
                </div>

                <div class="grid gap-3">
                    <a href="{{ route('kasir.index') }}" class="flex items-center justify-between rounded-[1.75rem] border border-orange-300/20 bg-orange-400/10 p-4 transition hover:bg-orange-400/15">
                        <div>
                            <div class="text-base font-semibold text-white">Buka Kasir</div>
                            <div class="mt-1 text-sm text-slate-300">Mulai transaksi dan kelola keranjang belanja.</div>
                        </div>
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-[#ff7a1a] text-white">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="9" cy="20" r="1"></circle>
                                <circle cx="18" cy="20" r="1"></circle>
                                <path d="M3 4h2l2.2 10.2a1 1 0 0 0 1 .8h9.7a1 1 0 0 0 1-.8L21 7H7"></path>
                            </svg>
                        </span>
                    </a>

                    <a href="{{ route('kasir.products') }}" class="flex items-center justify-between rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4 transition hover:bg-white/[0.08]">
                        <div>
                            <div class="text-base font-semibold text-white">Lihat Produk</div>
                            <div class="mt-1 text-sm text-slate-300">Cek katalog dan stok produk per outlet.</div>
                        </div>
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-slate-100">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 3 4 7v10l8 4 8-4V7l-8-4Z"></path>
                                <path d="m4 7 8 4 8-4"></path>
                                <path d="M12 11v10"></path>
                            </svg>
                        </span>
                    </a>
                </div>
            </section>

            <section class="rounded-[2rem] border border-white/10 bg-white/[0.05] p-4 shadow-xl shadow-slate-950/10">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Transaksi Terbaru</h2>
                        <p class="mt-1 text-sm text-slate-400">Aktivitas terakhir outlet ini.</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($recentTransactions as $transaction)
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/[0.04] p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-white">{{ $transaction['no_transaksi'] }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $transaction['tanggal'] }}</div>
                                </div>
                                <div class="text-right text-sm font-semibold text-[#ff7a1a]">Rp {{ number_format($transaction['total'], 0, ',', '.') }}</div>
                            </div>
                            <div class="mt-3 flex items-center gap-2 text-xs text-slate-300">
                                <span class="rounded-full bg-white/10 px-2.5 py-1">{{ $transaction['metode_pembayaran'] }}</span>
                                <span class="rounded-full bg-white/10 px-2.5 py-1">{{ $transaction['jumlah_item'] }} item</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-white/10 px-4 py-10 text-center text-sm text-slate-400">
                            Belum ada transaksi untuk outlet ini.
                        </div>
                    @endforelse
                </div>
            </section>
        </main>

        @include('kasir.partials.mobile-nav', ['active' => 'home'])
    </div>
    @include('kasir.partials.theme-script')
</body>
</html>

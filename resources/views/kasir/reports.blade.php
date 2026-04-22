<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kasir</title>
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
                    <h1 class="text-2xl font-semibold tracking-tight text-white">Laporan</h1>
                    <p class="mt-1 truncate text-sm text-slate-300">{{ $outlet->name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @include('kasir.partials.theme-toggle')
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-right text-xs text-slate-300">
                        <div class="font-semibold text-white">{{ now()->translatedFormat('d M Y') }}</div>
                        <div>Hari ini</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-md space-y-6 px-4 py-6 pb-28">
            <section class="rounded-[2rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                <p class="text-sm text-slate-300">Omzet hari ini</p>
                <h2 class="mt-2 text-3xl font-semibold text-white">Rp {{ number_format($todaySummary['omzet_harian'], 0, ',', '.') }}</h2>
                <p class="mt-2 text-sm text-slate-400">{{ $todaySummary['total_transaksi'] }} transaksi tercatat untuk outlet ini.</p>
            </section>

            <section class="grid grid-cols-2 gap-3">
                <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Tunai</div>
                    <div class="mt-3 text-2xl font-semibold text-white">{{ number_format($cashTransactions) }}</div>
                </div>
                <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Non Tunai</div>
                    <div class="mt-3 text-2xl font-semibold text-white">{{ number_format($nonCashTransactions) }}</div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-white/10 bg-white/[0.05] p-4 shadow-xl shadow-slate-950/10">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Transaksi Terkini</h2>
                        <p class="mt-1 text-sm text-slate-400">10 transaksi terakhir untuk pengecekan cepat.</p>
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

        @include('kasir.partials.mobile-nav', ['active' => 'reports'])
    </div>
    @include('kasir.partials.theme-script')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lainnya Kasir</title>
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
                    <h1 class="text-2xl font-semibold tracking-tight text-white">Lainnya</h1>
                    <p class="mt-1 truncate text-sm text-slate-300">Menu tambahan kasir</p>
                </div>
                @include('kasir.partials.theme-toggle')
            </div>
        </header>

        <main class="mx-auto w-full max-w-md space-y-6 px-4 py-6 pb-28">
            <section class="rounded-[2rem] border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                <p class="text-sm text-slate-300">Akun kasir</p>
                <h2 class="mt-2 text-2xl font-semibold text-white">{{ $user->name }}</h2>
                <div class="mt-4 space-y-2 text-sm text-slate-300">
                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/[0.04] px-4 py-3">
                        <span>Role</span>
                        <span class="font-semibold uppercase text-white">{{ $user->role }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/[0.04] px-4 py-3">
                        <span>Outlet</span>
                        <span class="font-semibold text-white">{{ $outlet->name }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/[0.04] px-4 py-3">
                        <span>Kode Outlet</span>
                        <span class="font-semibold text-white">{{ $outlet->code }}</span>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-2 gap-3">
                <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Produk</div>
                    <div class="mt-3 text-2xl font-semibold text-white">{{ number_format($productCount) }}</div>
                </div>
                <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Kategori</div>
                    <div class="mt-3 text-2xl font-semibold text-white">{{ number_format($categoryCount) }}</div>
                </div>
            </section>

            <section class="space-y-3">
                <a href="{{ route('kasir.products') }}" class="flex items-center justify-between rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4 transition hover:bg-white/[0.08]">
                    <div>
                        <div class="text-base font-semibold text-white">Kelola Tampilan Produk</div>
                        <div class="mt-1 text-sm text-slate-300">Lihat katalog produk outlet ini.</div>
                    </div>
                    <span class="text-slate-400">›</span>
                </a>

                <a href="{{ route('kasir.reports') }}" class="flex items-center justify-between rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-4 transition hover:bg-white/[0.08]">
                    <div>
                        <div class="text-base font-semibold text-white">Ringkasan Laporan</div>
                        <div class="mt-1 text-sm text-slate-300">Cek omzet dan transaksi terbaru.</div>
                    </div>
                    <span class="text-slate-400">›</span>
                </a>

                <a id="more-printer-card" href="{{ route('kasir.printer-bluetooth') }}" class="flex items-center justify-between rounded-[1.75rem] border border-cyan-400/20 bg-cyan-500/10 p-4 transition hover:bg-cyan-500/15">
                    <div>
                        <div class="text-base font-semibold text-white">Printer Bluetooth</div>
                        <div class="mt-1 text-sm text-cyan-50/80">Hubungkan printer BLE dan lakukan test print dari browser.</div>
                    </div>
                    <span class="text-cyan-100">›</span>
                </a>

                <form method="POST" action="{{ route('kasir.logout') }}">
                    @csrf
                    <button id="more-logout-button" type="submit" class="flex w-full items-center justify-between rounded-[1.75rem] border border-rose-400/20 bg-rose-500/10 p-4 text-left transition hover:bg-rose-500/15">
                        <div>
                            <div class="text-base font-semibold text-rose-100">Keluar dari Akun</div>
                            <div class="mt-1 text-sm text-rose-200/80">Akhiri sesi kasir di perangkat ini.</div>
                        </div>
                        <span class="text-rose-200">›</span>
                    </button>
                </form>
            </section>
        </main>

        @include('kasir.partials.mobile-nav', ['active' => 'more'])
    </div>
    @include('kasir.partials.theme-script')
</body>
</html>

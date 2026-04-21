<nav class="fixed inset-x-0 bottom-0 z-20 mx-auto w-full max-w-md px-4 pb-[max(0.75rem,env(safe-area-inset-bottom))] pt-3 text-slate-300">
    <div class="grid grid-cols-5 items-end gap-2 rounded-[2rem] border border-white/10 bg-white/[0.06] px-4 py-3 text-[11px] font-medium shadow-[0_-12px_30px_rgba(2,6,23,0.35)] backdrop-blur-xl">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1.5 text-center transition hover:text-slate-200 {{ ($active ?? null) === 'home' ? 'text-[#ff7a1a]' : '' }}">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 10.5 12 3l9 7.5"></path>
                <path d="M5 9.5V21h14V9.5"></path>
            </svg>
            <span>Beranda</span>
        </a>
        <a href="{{ route('kasir.products') }}" class="flex flex-col items-center gap-1.5 text-center transition hover:text-slate-200 {{ ($active ?? null) === 'products' ? 'text-[#ff7a1a]' : '' }}">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full {{ ($active ?? null) === 'products' ? 'bg-white/10 text-[#ff7a1a]' : 'text-current' }}">
                <svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3 4 7v10l8 4 8-4V7l-8-4Z"></path>
                    <path d="m4 7 8 4 8-4"></path>
                    <path d="M12 11v10"></path>
                </svg>
            </span>
            <span>Produk</span>
        </a>
        <a href="{{ route('kasir.index') }}" class="-mt-8 flex flex-col items-center gap-2 text-center {{ ($active ?? null) === 'kasir' ? 'text-slate-100' : 'text-slate-400' }}">
            <span class="inline-flex h-14 w-14 items-center justify-center rounded-full {{ ($active ?? null) === 'kasir' ? 'bg-[#ff7a1a] text-white shadow-[0_12px_24px_rgba(249,115,22,0.35)] ring-4 ring-slate-950/70' : 'bg-white/10 text-slate-300 ring-4 ring-slate-950/70' }}">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="9" cy="20" r="1"></circle>
                    <circle cx="18" cy="20" r="1"></circle>
                    <path d="M3 4h2l2.2 10.2a1 1 0 0 0 1 .8h9.7a1 1 0 0 0 1-.8L21 7H7"></path>
                </svg>
            </span>
            <span class="font-semibold {{ ($active ?? null) === 'kasir' ? 'text-[#ff7a1a]' : '' }}">Kasir</span>
        </a>
        <a href="{{ route('kasir.reports') }}" class="flex flex-col items-center gap-1.5 text-center transition hover:text-slate-200 {{ ($active ?? null) === 'reports' ? 'text-[#ff7a1a]' : '' }}">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 21V10"></path>
                <path d="M12 21V3"></path>
                <path d="M19 21v-6"></path>
            </svg>
            <span>Laporan</span>
        </a>
        <a href="{{ route('kasir.more') }}" class="flex flex-col items-center gap-1.5 text-center transition hover:text-slate-200 {{ ($active ?? null) === 'more' ? 'text-[#ff7a1a]' : '' }}">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8.92 4.6H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.36.58.56 1.25.6 1.93V11a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09c-.68.04-1.35.24-1.91.6Z"></path>
            </svg>
            <span>Lainnya</span>
        </a>
    </div>
</nav>

<style>
    html[data-theme="light"] { color-scheme: light; }
    html[data-theme="light"] body { background: #f8fafc; color: #0f172a; }
    html[data-theme="light"] #page-background { background: radial-gradient(circle at top, rgba(251, 146, 60, 0.18), transparent 35%), radial-gradient(circle at right, rgba(59, 130, 246, 0.14), transparent 30%), linear-gradient(180deg, #f8fafc 0%, #eef2ff 45%, #e2e8f0 100%); }
    html[data-theme="light"] header, html[data-theme="light"] #kasir-header-card, html[data-theme="light"] #catalog-card, html[data-theme="light"] #transactions-card, html[data-theme="light"] #cart-sheet, html[data-theme="light"] nav.fixed, html[data-theme="light"] section, html[data-theme="light"] .light-card { background: rgba(255,255,255,0.92); border-color: rgba(148,163,184,0.35); color: #0f172a; }
    html[data-theme="light"] header { background: rgba(248,250,252,0.96) !important; }
    html[data-theme="light"] #kasir-header-card h1, html[data-theme="light"] #kasir-header-card p, html[data-theme="light"] #catalog-card h2, html[data-theme="light"] #catalog-card p, html[data-theme="light"] #transactions-card h2, html[data-theme="light"] #transactions-card p, html[data-theme="light"] #cart-sheet h2, html[data-theme="light"] #cart-sheet p, html[data-theme="light"] .light-card h2, html[data-theme="light"] .light-card p { color: #0f172a; }
    html[data-theme="light"] .text-white, html[data-theme="light"] .text-slate-100, html[data-theme="light"] .text-slate-200, html[data-theme="light"] .text-slate-300, html[data-theme="light"] .text-slate-400 { color: #0f172a !important; }
    html[data-theme="light"] .bg-slate-950, html[data-theme="light"] .bg-slate-900, html[data-theme="light"] .bg-slate-800, html[data-theme="light"] .bg-slate-900\/70, html[data-theme="light"] .bg-slate-950\/80, html[data-theme="light"] .bg-slate-950\/70, html[data-theme="light"] .bg-white\/5, html[data-theme="light"] .bg-white\/4, html[data-theme="light"] .bg-white\/6 { background-color: rgba(241,245,249,0.92) !important; }
    html[data-theme="light"] .border-white\/10, html[data-theme="light"] .border-white\/5 { border-color: rgba(148,163,184,0.35) !important; }
    html[data-theme="light"] #catalog-card [class*="bg-slate-900"],
    html[data-theme="light"] #catalog-card [class*="from-slate-800"],
    html[data-theme="light"] #catalog-card [class*="to-slate-900"],
    html[data-theme="light"] #product-list [class*="bg-slate-900"],
    html[data-theme="light"] #product-list [class*="from-slate-800"],
    html[data-theme="light"] #product-list [class*="to-slate-900"],
    html[data-theme="light"] #product-grid [class*="bg-slate-900"],
    html[data-theme="light"] #product-grid [class*="from-slate-800"],
    html[data-theme="light"] #product-grid [class*="to-slate-900"] { background: #e2e8f0 !important; color: #64748b !important; }
    html[data-theme="light"] #catalog-card [class*="justify-center"],
    html[data-theme="light"] #product-list [class*="justify-center"],
    html[data-theme="light"] #product-grid [class*="justify-center"] { background: #e2e8f0 !important; }
    html[data-theme="light"] .bg-white\/10 { background-color: rgba(255,255,255,0.85) !important; }
    html[data-theme="light"] .text-emerald-200, html[data-theme="light"] .text-orange-300, html[data-theme="light"] .text-rose-300, html[data-theme="light"] .text-[#ff7a1a] { color: #c2410c !important; }
    html[data-theme="light"] .bg-[#ff7a1a], html[data-theme="light"] .bg-[#ff7a1a]\/10 { background: linear-gradient(135deg, #fb923c 0%, #f97316 100%) !important; color: #fff !important; }
    html[data-theme="light"] nav.fixed > div { background: rgba(255,255,255,0.88); border-color: rgba(148,163,184,0.3); box-shadow: 0 -12px 30px rgba(15,23,42,0.08); color: #334155; }
    html[data-theme="light"] #mobile-nav a:hover {
        color: #c2410c !important;
    }
    html[data-theme="light"] #mobile-nav a:hover span,
    html[data-theme="light"] #mobile-nav a:hover svg {
        color: inherit !important;
    }
    html[data-theme="light"] #mobile-nav a.text-[#ff7a1a]:hover { color: #c2410c !important; }
    html[data-theme="light"] #theme-toggle-button { background: rgba(255,255,255,0.9); border-color: rgba(148,163,184,0.4); color: #0f172a; }
    html[data-theme="light"] #cart-toggle-button { box-shadow: 0 16px 35px rgba(249,115,22,0.22); background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); color: #fff; }
    html[data-theme="light"] #cart-toggle-button:hover { background: linear-gradient(135deg, #fdba74 0%, #fb923c 100%); }
    html[data-theme="light"] #printer-page #printer-support-card,
    html[data-theme="light"] #printer-page #printer-config-card,
    html[data-theme="light"] #printer-page #printer-test-card {
        background: rgba(255,255,255,0.95) !important;
        border-color: rgba(148,163,184,0.35) !important;
        color: #0f172a !important;
    }
    html[data-theme="light"] #printer-page #printer-support-card p,
    html[data-theme="light"] #printer-page #printer-support-card h2,
    html[data-theme="light"] #printer-page #printer-config-card p,
    html[data-theme="light"] #printer-page #printer-config-card h2,
    html[data-theme="light"] #printer-page #printer-config-card label,
    html[data-theme="light"] #printer-page #printer-test-card p,
    html[data-theme="light"] #printer-page #printer-test-card h2,
    html[data-theme="light"] #printer-page #printer-test-card label,
    html[data-theme="light"] #printer-page #support-text,
    html[data-theme="light"] #printer-page #device-name,
    html[data-theme="light"] #printer-page #device-status,
    html[data-theme="light"] #printer-page #message-box {
        color: #0f172a !important;
    }
    html[data-theme="light"] #printer-page #support-badge,
    html[data-theme="light"] #printer-page #clear-config-button,
    html[data-theme="light"] #printer-page #disconnect-button,
    html[data-theme="light"] #printer-page #connect-button,
    html[data-theme="light"] #printer-page #test-print-button {
        color: #0f172a !important;
    }
    html[data-theme="light"] #printer-page .text-cyan-300,
    html[data-theme="light"] #printer-page .text-cyan-100,
    html[data-theme="light"] #printer-page .text-cyan-100\/90,
    html[data-theme="light"] #printer-page .text-cyan-50\/80,
    html[data-theme="light"] #printer-page .text-slate-200,
    html[data-theme="light"] #printer-page .text-slate-300,
    html[data-theme="light"] #printer-page .text-slate-400 {
        color: #334155 !important;
    }
    html[data-theme="light"] #more-printer-card,
    html[data-theme="light"] #more-logout-button {
        background: rgba(255,255,255,0.95) !important;
        border-color: rgba(148,163,184,0.35) !important;
        color: #0f172a !important;
    }
    html[data-theme="light"] #more-printer-card div,
    html[data-theme="light"] #more-logout-button div,
    html[data-theme="light"] #more-printer-card span,
    html[data-theme="light"] #more-logout-button span {
        color: #0f172a !important;
    }
    html[data-theme="light"] #more-printer-card:hover,
    html[data-theme="light"] #more-logout-button:hover {
        background: rgba(248,250,252,0.98) !important;
    }
    html[data-theme="light"] #more-printer-card span.text-cyan-100,
    html[data-theme="light"] #more-logout-button span.text-rose-200 {
        color: #64748b !important;
    }
    html[data-theme="light"] input, html[data-theme="light"] select, html[data-theme="light"] textarea { background: rgba(255,255,255,0.95) !important; border-color: rgba(148,163,184,0.45) !important; color: #0f172a !important; }
    html[data-theme="light"] input::placeholder { color: #94a3b8; }
    html[data-theme="light"] .light-surface { background: rgba(255,255,255,0.92) !important; border-color: rgba(148,163,184,0.35) !important; }
    html[data-theme="light"] .light-surface-soft { background: rgba(241,245,249,0.92) !important; }
</style>

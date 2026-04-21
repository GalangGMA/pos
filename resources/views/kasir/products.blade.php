<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 text-slate-100">
    <div class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(45,212,191,0.18),_transparent_35%),radial-gradient(circle_at_right,_rgba(59,130,246,0.16),_transparent_30%),linear-gradient(180deg,_#020617_0%,_#0f172a_45%,_#111827_100%)]"></div>

        <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur">
            <div class="mx-auto flex w-full max-w-md items-center justify-between gap-3 px-4 py-5">
                <div class="min-w-0">
                    <h1 class="text-2xl font-semibold tracking-tight text-white">Produk</h1>
                    <p class="mt-1 truncate text-sm text-slate-300">{{ $outlet->name }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-right text-xs text-slate-300">
                    <div id="product-count" class="font-semibold text-white">{{ $products->count() }}</div>
                    <div>Produk</div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-md min-w-0 px-4 py-6 pb-28">
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-4 shadow-2xl shadow-slate-950/20">
                <div class="space-y-4">
                    <div class="w-full">
                        <label for="search-products" class="sr-only">Cari produk</label>
                        <input id="search-products" type="search" placeholder="Cari produk..." class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white outline-none ring-0 placeholder:text-slate-500 focus:border-orange-300">
                    </div>

                    <div class="w-full min-w-0 overflow-x-auto overflow-y-hidden pb-1 [scrollbar-width:none] [-webkit-overflow-scrolling:touch] [&::-webkit-scrollbar]:hidden">
                        <div id="category-filters" class="flex w-max min-w-full flex-nowrap gap-2 pr-4"></div>
                    </div>
                </div>

                <div id="product-results-label" class="mt-4 text-sm text-slate-400">{{ $products->count() }} produk ditemukan</div>

                <div id="product-list" class="mt-4 space-y-4"></div>
                <div id="empty-products" class="mt-4 hidden rounded-3xl border border-dashed border-white/10 px-6 py-12 text-center text-sm text-slate-400">
                    Tidak ada produk yang cocok dengan pencarian.
                </div>
            </section>
        </main>

        @include('kasir.partials.mobile-nav', ['active' => 'products'])
    </div>

    <script>
        const products = @json($products);
        const categoryOptions = @json($categories);
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
            activeCategory: 'all',
        };

        const searchInput = document.getElementById('search-products');
        const categoryFilters = document.getElementById('category-filters');
        const productList = document.getElementById('product-list');
        const emptyProducts = document.getElementById('empty-products');
        const productCount = document.getElementById('product-count');
        const productResultsLabel = document.getElementById('product-results-label');

        const escapeHtml = value => String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const formatCurrency = value => `Rp ${currency.format(value || 0)}`;

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

        const filteredProducts = () => {
            const query = state.query.trim().toLowerCase();

            return products.filter(product => {
                const matchesQuery = product.nama.toLowerCase().includes(query);
                const activeCategory = categories.find(category => category.id === state.activeCategory);
                const productCategory = (product.kategori || '').toLowerCase();
                const selectedCategory = (activeCategory?.nama || '').toLowerCase();
                const matchesCategory = state.activeCategory === 'all' || productCategory === selectedCategory;

                return matchesQuery && matchesCategory;
            });
        };

        const renderProducts = () => {
            const items = filteredProducts();

            productCount.textContent = String(items.length);
            productResultsLabel.textContent = `${items.length} produk ditemukan`;
            emptyProducts.classList.toggle('hidden', items.length > 0);

            productList.innerHTML = items.map(product => `
                <article class="overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/[0.05] p-3 shadow-lg shadow-slate-950/10">
                    <div class="flex items-center gap-3">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-slate-900/80">
                            ${product.image_url
                                ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.nama)}" class="h-full w-full object-cover">`
                                : `<div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 text-xs font-medium text-slate-500">IMG</div>`}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="truncate text-base font-semibold text-white">${escapeHtml(product.nama)}</h3>
                                    <p class="mt-1 text-xs text-slate-400">${escapeHtml(product.kategori || 'Lainnya')}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-white/10 px-2.5 py-1 text-xs text-slate-300">Stok ${product.stok}</span>
                            </div>
                            <div class="mt-3 text-lg font-semibold text-[#ff7a1a]">${formatCurrency(product.harga)}</div>
                        </div>
                    </div>
                </article>
            `).join('');
        };

        document.addEventListener('click', event => {
            const categoryTrigger = event.target.closest('[data-category-id]');

            if (!categoryTrigger) {
                return;
            }

            state.activeCategory = categoryTrigger.dataset.categoryId || 'all';
            renderCategories();
            renderProducts();
        });

        searchInput.addEventListener('input', event => {
            state.query = event.target.value || '';
            renderProducts();
        });

        renderCategories();
        renderProducts();
    </script>
</body>
</html>

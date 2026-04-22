<script>
    (() => {
        const button = document.getElementById('theme-toggle-button');
        const label = document.getElementById('theme-toggle-label');
        const icon = document.getElementById('theme-toggle-icon');

        const getTheme = () => document.documentElement.dataset.theme || 'dark';

        const setTheme = theme => {
            const nextTheme = theme === 'light' ? 'light' : 'dark';

            document.documentElement.dataset.theme = nextTheme;
            document.body.dataset.theme = nextTheme;

            try {
                localStorage.setItem('kasir-theme', nextTheme);
            } catch (error) {
                // ignore storage failures
            }

            if (label) {
                label.textContent = nextTheme === 'light' ? 'Dark' : 'Light';
            }

            if (button) {
                button.setAttribute('aria-label', nextTheme === 'light' ? 'Ganti ke mode gelap' : 'Ganti ke mode terang');
                button.title = nextTheme === 'light' ? 'Ganti ke mode gelap' : 'Ganti ke mode terang';
            }

            if (icon) {
                icon.innerHTML = nextTheme === 'light'
                    ? '<svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>'
                    : '<svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>';
            }
        };

        if (button) {
            setTheme(getTheme());
            button.addEventListener('click', () => setTheme(getTheme() === 'light' ? 'dark' : 'light'));
        }
    })();
</script>

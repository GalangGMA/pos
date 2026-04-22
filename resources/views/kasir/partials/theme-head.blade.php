<script>
    (() => {
        let savedTheme = null;

        try {
            savedTheme = localStorage.getItem('kasir-theme');
        } catch (error) {
            savedTheme = null;
        }

        const prefersLight = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
        document.documentElement.dataset.theme = savedTheme || (prefersLight ? 'light' : 'dark');
    })();
</script>

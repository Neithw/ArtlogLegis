export default () => ({
    darkMode:
        document.documentElement.classList.contains('dark'),

    sidebarOpen: false,

    sidebarCollapsed:
        localStorage.getItem('sidebar-collapsed') === 'true',

    applyTheme() {
        document.documentElement.classList.toggle(
            'dark',
            this.darkMode
        );
    },

    applySidebar() {
        document.documentElement.classList.toggle(
            'sidebar-collapsed',
            this.sidebarCollapsed
        );
    },

    toggleTheme() {
        this.darkMode = !this.darkMode;

        this.applyTheme();

        localStorage.setItem(
            'theme',
            this.darkMode ? 'dark' : 'light'
        );
    },

    toggleSidebar() {
        if (window.innerWidth >= 1024) {
            this.sidebarCollapsed = !this.sidebarCollapsed;

            this.applySidebar();

            localStorage.setItem(
                'sidebar-collapsed',
                String(this.sidebarCollapsed)
            );

            return;
        }

        this.sidebarOpen = !this.sidebarOpen;
    },
});
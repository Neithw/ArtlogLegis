export default (abertoInicial = false) => ({
    aberto: Boolean(abertoInicial),

    alternar() {
        const layoutStore = this.$store.layout;

        if (
            window.innerWidth >= 1024
            && layoutStore.sidebarCollapsed
        ) {
            layoutStore.toggleSidebar();
            this.aberto = true;

            return;
        }

        this.aberto = !this.aberto;
    },
});
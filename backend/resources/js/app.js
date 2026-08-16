import {
    Livewire,
    Alpine,
} from '../../vendor/livewire/livewire/dist/livewire.esm';

Alpine.data('formularioUsuario', ({
    pacotes = {},
    papelInicial = '',
    permissoesIniciais = [],
    permissoesDisponiveis = []
}) => ({
    pacotes,

    papelId: String(papelInicial ?? ''),

    permissoesSelecionadas: Array.isArray(permissoesIniciais)
        ? permissoesIniciais.map(Number)
        : [],

    permissoesDisponiveis: Array.isArray(permissoesDisponiveis)
        ? permissoesDisponiveis.map(Number)
        : [],

    personalizacaoAberta: false,

    init() {
        this.personalizacaoAberta = this.possuiPersonalizacao;
    },

    get permissoesDoPacoteAtual() {
        return (this.pacotes[String(this.papelId)] ?? [])
            .map(Number);
    },

    get possuiPersonalizacao() {
        if (!this.papelId) {
            return this.permissoesSelecionadas.length > 0;
        }

        return JSON.stringify(
            this.normalizarIds(this.permissoesSelecionadas)
        ) !== JSON.stringify(
            this.normalizarIds(this.permissoesDoPacoteAtual)
        );
    },

    normalizarIds(ids) {
        return [...new Set(ids.map(Number))]
            .sort((a, b) => a - b);
    },

    selecionarPapel(roleId) {
        this.papelId = String(roleId);
        this.aplicarPacote(roleId);
    },

    aplicarPacote(roleId) {
        const permissoesDoPacote =
            this.pacotes[String(roleId)] ?? [];

        this.permissoesSelecionadas =
            permissoesDoPacote.map(Number);
    },

    selecionarTodasPermissoes() {
        this.permissoesSelecionadas = [
            ...this.permissoesDisponiveis
        ];

        this.personalizacaoAberta = true;
    },

    limparPermissoes() {
        this.permissoesSelecionadas = [];
        this.personalizacaoAberta = true;
    }
}));

Alpine.data('formularioVereador', (configuracao = {}) => ({
    camaraId: String(configuracao.camaraId ?? ''),
    userId: String(configuracao.userId ?? ''),
    usuarios: Array.isArray(configuracao.usuarios)
        ? configuracao.usuarios
        : [],

    get usuariosFiltrados() {
        if (!this.camaraId) {
            return [];
        }

        const camaraIdSelecionada = Number(this.camaraId);

        return this.usuarios.filter(
            (usuario) =>
                Number(usuario.camara_id) === camaraIdSelecionada
        );
    },

    alterarCamara() {
        this.userId = '';
    }
}));

Alpine.data('formularioMandato', (configuracao = {}) => ({
    vereadorId: String(configuracao.vereadorId ?? ''),
    legislaturaId: String(configuracao.legislaturaId ?? ''),

    vereadores: Array.isArray(configuracao.vereadores)
        ? configuracao.vereadores
        : [],

    legislaturas: Array.isArray(configuracao.legislaturas)
        ? configuracao.legislaturas
        : [],

    get vereadorSelecionado() {
        if (!this.vereadorId) {
            return null;
        }

        return this.vereadores.find(
            (vereador) => Number(vereador.id) === Number(this.vereadorId)
        ) ?? null;
    },

    get legislaturasFiltradas() {
        if (!this.vereadorSelecionado) {
            return [];
        }

        const camaraId = Number(this.vereadorSelecionado.camara_id);

        return this.legislaturas.filter(
            (legislatura) => Number(legislatura.camara_id) === camaraId
        );
    },

    alterarVereador() {
        this.legislaturaId = '';
    }
}));

Alpine.store('layout', {
    darkMode: document.documentElement.classList.contains('dark'),

    sidebarOpen: false,

    sidebarCollapsed:
        localStorage.getItem('sidebar-collapsed') === 'true',

    sidebarLabelsVisible:
        localStorage.getItem('sidebar-collapsed') !== 'true',

    sidebarLabelTimer: null,

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
            clearTimeout(this.sidebarLabelTimer);

            if (this.sidebarCollapsed) {
                this.sidebarCollapsed = false;
                this.applySidebar();

                localStorage.setItem('sidebar-collapsed', 'false');

                this.sidebarLabelTimer = setTimeout(() => {
                    if (!this.sidebarCollapsed) {
                        this.sidebarLabelsVisible = true;
                    }
                }, 100);
            } else {
                this.sidebarLabelsVisible = false;
                this.sidebarCollapsed = true;
                this.applySidebar();

                localStorage.setItem('sidebar-collapsed', 'true');
            }

            return;
        }

        this.sidebarOpen = !this.sidebarOpen;
    },
});

document.addEventListener('livewire:navigated', () => {
    const layout = Alpine.store('layout')

    layout.applyTheme();
    layout.applySidebar();
});

Livewire.start();

requestAnimationFrame(() => {
    document.documentElement.classList.add('layout-ready');
});
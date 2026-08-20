export default ({
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
});
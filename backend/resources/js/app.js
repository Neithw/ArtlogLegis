import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('formularioUsuario', ({
    pacotes = {},
    papelInicial = '',
    permissoesIniciais = []
}) => ({
    pacotes,

    papelId: String(papelInicial ?? ''),

    permissoesSelecionadas: permissoesIniciais.map(Number),

    aplicarPacote(roleId) {
        const permissoesDoPacote = this.pacotes[String(roleId)] ?? [];

        this.permissoesSelecionadas = permissoesDoPacote.map(Number);
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

Alpine.start();
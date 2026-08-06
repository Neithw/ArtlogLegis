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

Alpine.start();
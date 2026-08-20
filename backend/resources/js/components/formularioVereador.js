export default (configuracao = {}) => ({
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
});
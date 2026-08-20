export default (configuracao = {}) => ({
    camaraSelecionada: String(
        configuracao.camaraSelecionada ?? ''
    ),

    legislaturaSelecionada: String(
        configuracao.legislaturaSelecionada ?? ''
    ),

    legislaturas: Array.isArray(configuracao.legislaturas)
        ? configuracao.legislaturas
        : [],

    get legislaturasFiltradas() {
        if (!this.camaraSelecionada) {
            return [];
        }

        return this.legislaturas.filter(
            (legislatura) =>
                String(legislatura.camara_id)
                === this.camaraSelecionada
        );
    },

    alterarCamara() {
        this.legislaturaSelecionada = '';
    },
});
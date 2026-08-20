export default (configuracao = {}) => ({
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
});
export default (configuracao = {}) => ({
    camaraId: String(configuracao.camaraId ?? ''),

    legislaturaId: String(
        configuracao.legislaturaId ?? ''
    ),

    tipoProposicaoId: String(
        configuracao.tipoProposicaoId ?? ''
    ),

    autorMandatoId: String(
        configuracao.autorMandatoId ?? ''
    ),

    legislaturas: Array.isArray(configuracao.legislaturas)
        ? configuracao.legislaturas
        : [],

    tiposProposicao: Array.isArray(configuracao.tiposProposicao)
        ? configuracao.tiposProposicao
        : [],

    mandatos: Array.isArray(configuracao.mandatos)
        ? configuracao.mandatos
        : [],

    get legislaturasFiltradas() {
        return this.legislaturas.filter(
            (legislatura) =>
                String(legislatura.camara_id)
                === this.camaraId
        );
    },

    get tiposProposicaoFiltrados() {
        return this.tiposProposicao.filter(
            (tipo) =>
                String(tipo.camara_id)
                === this.camaraId
        );
    },

    get mandatosFiltrados() {
        return this.mandatos.filter(
            (mandato) =>
                String(mandato.camara_id)
                === this.camaraId
                && String(mandato.legislatura_id)
                === this.legislaturaId
                && (
                    mandato.elegivel
                    || String(mandato.id)
                    === this.autorMandatoId
                )
        );
    },

    get autorMandatoSelecionado() {
        return this.mandatos.find(
            (mandato) =>
                String(mandato.id)
                === this.autorMandatoId
        ) ?? null;
    },

    get autorMandatoIndisponivel() {
        return Boolean(
            this.autorMandatoSelecionado
            && !this.autorMandatoSelecionado.elegivel
        );
    },

    alterarCamara() {
        this.legislaturaId = '';
        this.tipoProposicaoId = '';
        this.autorMandatoId = '';
    },

    alterarLegislatura() {
        this.autorMandatoId = '';
    },
});
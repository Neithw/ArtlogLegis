export default (valoresIniciais = []) => {
    const valores = Array.isArray(valoresIniciais)
        ? valoresIniciais
            .map((valor) => String(valor).trim())
            .filter(Boolean)
        : [];

    return {
        proximoId: valores.length + 1,
        novaPalavra: '',

        palavrasChave: valores.map((valor, indice) => ({
            id: indice + 1,
            valor,
        })),

        adicionarPalavraChave() {
            const valor = this.novaPalavra.trim();

            if (!valor) {
                return;
            }

            const repetida = this.palavrasChave.some(
                (palavra) =>
                    palavra.valor.toLowerCase()
                    === valor.toLowerCase()
            );

            if (!repetida) {
                this.palavrasChave.push({
                    id: this.proximoId++,
                    valor,
                });
            }

            this.novaPalavra = '';
        },

        removerPalavraChave(id) {
            this.palavrasChave = this.palavrasChave.filter(
                (palavra) => palavra.id !== id
            );
        },
    };
};
const ROTULOS_PADRAO = {
    favoravel: 'Favorável',
    contrario: 'Contrário',
    abstencao: 'Abstenção',
};

function normalizarEscolha(escolha, rotulos) {
    if (
        typeof escolha !== 'string'
        || !Object.prototype.hasOwnProperty.call(
            rotulos,
            escolha
        )
    ) {
        return null;
    }

    return escolha;
}

export default function votacaoPlenario(configuracao = {}) {
    const rotulos = {
        ...ROTULOS_PADRAO,
        ...(configuracao.rotulos ?? {}),
    };

    const votoAtual = normalizarEscolha(
        configuracao.votoAtual,
        rotulos
    );

    const escolhaInicial = normalizarEscolha(
        configuracao.escolhaInicial,
        rotulos
    );

    return {
        rotulos,
        votoAtual,

        escolhaPendente:
            escolhaInicial === votoAtual
                ? null
                : escolhaInicial,

        enviando: false,

        selecionar(escolha) {
            if (this.enviando) {
                return;
            }

            const escolhaNormalizada = normalizarEscolha(
                escolha,
                this.rotulos
            );

            if (escolhaNormalizada === null) {
                return;
            }

            if (escolhaNormalizada === this.votoAtual) {
                this.escolhaPendente = null;
                return;
            }

            if (escolhaNormalizada === this.escolhaPendente) {
                return;
            }

            this.escolhaPendente = escolhaNormalizada;
            this.focarConfirmacao();
        },

        cancelar() {
            if (this.enviando) {
                return;
            }

            this.escolhaPendente = null;
        },

        ehVotoAtual(escolha) {
            return this.votoAtual === escolha;
        },

        ehNovaEscolha(escolha) {
            return this.escolhaPendente === escolha;
        },

        estaSelecionado(escolha) {
            if (this.escolhaPendente !== null) {
                return this.escolhaPendente === escolha;
            }

            return this.votoAtual === escolha;
        },

        rotulo(escolha) {
            return this.rotulos[escolha] ?? '';
        },

        get podeConfirmar() {
            return !this.enviando
                && this.escolhaPendente !== null
                && this.escolhaPendente !== this.votoAtual;
        },

        aoFinalizarExpansao(evento) {
            if (
                evento.propertyName !== 'height'
                || this.escolhaPendente === null
            ) {
                return;
            }

            this.focarConfirmacao();
        },

        focarConfirmacao() {
            const confirmacao = this.$refs.confirmacao;

            if (!confirmacao) {
                return;
            }

            const movimentoReduzido = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches;

            const margemInferior = 24;

            const limiteVisivel =
                window.innerHeight - margemInferior;

            const posicao =
                confirmacao.getBoundingClientRect();

            const deslocamento = Math.max(
                posicao.bottom - limiteVisivel,
                0
            );

            if (deslocamento > 1) {
                window.scrollBy({
                    top: deslocamento,

                    behavior: movimentoReduzido
                        ? 'auto'
                        : 'smooth',
                });
            }

            this.$refs.tituloConfirmacao?.focus({
                preventScroll: true,
            });
        },

        confirmar(evento) {
            if (!this.podeConfirmar) {
                evento.preventDefault();
                return;
            }

            this.enviando = true;
        },
    };
}
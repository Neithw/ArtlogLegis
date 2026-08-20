export default () => ({
    processando: false,
    erro: '',
    mensagem: '',
    temporizadorMensagem: null,

    async mover(event) {
        if (this.processando) {
            return;
        }

        const formulario = event.currentTarget;
        const itemAtual = formulario.closest('[data-item-pauta]');
        const direcao = new FormData(formulario).get('direcao');

        const itemVizinho = direcao === 'acima'
            ? itemAtual.previousElementSibling
            : itemAtual.nextElementSibling;

        if (!itemVizinho?.matches('[data-item-pauta]')) {
            return;
        }

        const posicoesAnteriores = new Map(
            [...this.$refs.lista.querySelectorAll('[data-item-pauta]')]
                .map((item) => [
                    item,
                    item.getBoundingClientRect()
                ])
        );

        this.processando = true;
        this.erro = '';
        this.mensagem = '';

        try {
            const resposta = await fetch(formulario.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: new FormData(formulario)
            });

            const dados = await resposta.json()
                .catch(() => ({}));

            if (!resposta.ok) {
                throw new Error(
                    this.obterMensagemErro(dados)
                );
            }

            if (Array.isArray(dados.ordem)) {
                this.aplicarOrdem(dados.ordem);
            } else {
                this.trocarItens(
                    itemAtual,
                    itemVizinho,
                    direcao
                );
            }

            this.atualizarControles();
            this.animarMovimento(posicoesAnteriores);

            this.mensagem =
                dados.message ?? 'Ordem da pauta atualizada.';

            clearTimeout(this.temporizadorMensagem);

            this.temporizadorMensagem = setTimeout(() => {
                this.mensagem = '';
            }, 3000);
        } catch (erro) {
            this.erro = erro instanceof Error
                ? erro.message
                : 'Não foi possível atualizar a ordem da pauta.';
        } finally {
            this.processando = false;
        }
    },

    aplicarOrdem(ordem) {
        const itensPorId = new Map(
            [...this.$refs.lista.querySelectorAll('[data-item-pauta]')]
                .map((item) => [
                    Number(item.dataset.itemId),
                    item
                ])
        );

        ordem.forEach((itemId) => {
            const item = itensPorId.get(Number(itemId));

            if (item) {
                this.$refs.lista.appendChild(item);
            }
        });
    },

    trocarItens(itemAtual, itemVizinho, direcao) {
        if (direcao === 'acima') {
            this.$refs.lista.insertBefore(
                itemAtual,
                itemVizinho
            );

            return;
        }

        this.$refs.lista.insertBefore(
            itemVizinho,
            itemAtual
        );
    },

    atualizarControles() {
        const itens = [
            ...this.$refs.lista.querySelectorAll('[data-item-pauta]')
        ];

        itens.forEach((item, indice) => {
            const ordem = indice + 1;

            const numero = item.querySelector(
                '[data-numero-ordem]'
            );

            if (numero) {
                numero.textContent = ordem;
            }

            const moverAcima = item.querySelector(
                '[data-mover="acima"]'
            );

            const moverAbaixo = item.querySelector(
                '[data-mover="abaixo"]'
            );

            moverAcima?.classList.toggle(
                'hidden',
                indice === 0
            );

            moverAbaixo?.classList.toggle(
                'hidden',
                indice === itens.length - 1
            );

            moverAcima
                ?.querySelector('button')
                ?.setAttribute(
                    'aria-label',
                    `Mover item ${ordem} para cima`
                );

            moverAbaixo
                ?.querySelector('button')
                ?.setAttribute(
                    'aria-label',
                    `Mover item ${ordem} para baixo`
                );
        });
    },

    animarMovimento(posicoesAnteriores) {
        const itens = this.$refs.lista.querySelectorAll(
            '[data-item-pauta]'
        );

        itens.forEach((item) => {
            const posicaoAnterior =
                posicoesAnteriores.get(item);

            if (!posicaoAnterior) {
                return;
            }

            const posicaoAtual =
                item.getBoundingClientRect();

            const deslocamentoY =
                posicaoAnterior.top - posicaoAtual.top;

            if (
                deslocamentoY === 0
                || typeof item.animate !== 'function'
            ) {
                return;
            }

            item.animate(
                [
                    {
                        transform:
                            `translateY(${deslocamentoY}px)`
                    },
                    {
                        transform: 'translateY(0)'
                    }
                ],
                {
                    duration: 220,
                    easing: 'ease-out'
                }
            );
        });
    },

    obterMensagemErro(dados) {
        if (dados?.errors) {
            const mensagens = Object.values(dados.errors)
                .flat();

            if (mensagens.length > 0) {
                return mensagens[0];
            }
        }

        return dados?.message
            ?? 'Não foi possível atualizar a ordem da pauta.';
    }
});
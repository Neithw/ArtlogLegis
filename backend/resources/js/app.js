import {
    Livewire,
    Alpine,
} from '../../vendor/livewire/livewire/dist/livewire.esm';

import presencasSessao from './components/presencasSessao';

Alpine.data('formularioUsuario', ({
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

Alpine.data('pautaOrdenavel', () => ({
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
}));

Alpine.data('presencasSessao', presencasSessao);

Alpine.store('layout', {
    darkMode: document.documentElement.classList.contains('dark'),

    sidebarOpen: false,

    sidebarCollapsed:
        localStorage.getItem('sidebar-collapsed') === 'true',

    sidebarLabelsVisible:
        localStorage.getItem('sidebar-collapsed') !== 'true',

    sidebarLabelTimer: null,

    applyTheme() {
        document.documentElement.classList.toggle(
            'dark',
            this.darkMode
        );
    },

    applySidebar() {
        document.documentElement.classList.toggle(
            'sidebar-collapsed',
            this.sidebarCollapsed
        );
    },

    toggleTheme() {
        this.darkMode = !this.darkMode;

        this.applyTheme();

        localStorage.setItem(
            'theme',
            this.darkMode ? 'dark' : 'light'
        );
    },

    toggleSidebar() {
        if (window.innerWidth >= 1024) {
            clearTimeout(this.sidebarLabelTimer);

            if (this.sidebarCollapsed) {
                this.sidebarCollapsed = false;
                this.applySidebar();

                localStorage.setItem('sidebar-collapsed', 'false');

                this.sidebarLabelTimer = setTimeout(() => {
                    if (!this.sidebarCollapsed) {
                        this.sidebarLabelsVisible = true;
                    }
                }, 100);
            } else {
                this.sidebarLabelsVisible = false;
                this.sidebarCollapsed = true;
                this.applySidebar();

                localStorage.setItem('sidebar-collapsed', 'true');
            }

            return;
        }

        this.sidebarOpen = !this.sidebarOpen;
    },
});

document.addEventListener('livewire:navigated', () => {
    const layout = Alpine.store('layout')

    layout.applyTheme();
    layout.applySidebar();
});

Livewire.start();

requestAnimationFrame(() => {
    document.documentElement.classList.add('layout-ready');
});
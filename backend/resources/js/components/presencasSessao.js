function normalizarTotais(totais = {}) {
    return {
        mandatos: Number(totais.mandatos ?? 0),
        presentes: Number(totais.presentes ?? 0),
        ausentes: Number(totais.ausentes ?? 0),
        justificadas: Number(totais.justificadas ?? 0),

        naoRegistradas: Number(
            totais.naoRegistradas
            ?? totais.nao_registradas
            ?? 0
        ),
    };
}

export default function presencasSessao(configuracao = {}) {
    const registros = Object.fromEntries(
        Object.entries(configuracao.registros ?? {})
            .map(([mandatoId, registro]) => [
                String(mandatoId),
                {
                    situacao: registro.situacao ?? '',
                    rotulo: registro.rotulo ?? 'Não registrada',
                    observacao: registro.observacao ?? '',

                    registradoPor:
                        registro.registradoPor ?? null,

                    atualizadoPor:
                        registro.atualizadoPor ?? null,

                    situacaoFormulario:
                        registro.situacao ?? '',

                    observacaoFormulario:
                        registro.observacao ?? '',

                    salvando: false,
                    erro: '',
                },
            ])
    );

    return {
        registros,

        totais: normalizarTotais(
            configuracao.totais
        ),

        mensagem: '',
        temporizadorMensagem: null,

        registro(mandatoId) {
            return this.registros[String(mandatoId)];
        },

        validar(registro) {
            if (!registro.situacaoFormulario) {
                return 'Selecione a situação da presença.';
            }

            if (
                registro.situacaoFormulario === 'justificada'
                && !registro.observacaoFormulario.trim()
            ) {
                return 'Informe a justificativa da ausência.';
            }

            return '';
        },

        async salvar(event, mandatoId) {
            const registro = this.registro(mandatoId);

            if (!registro || registro.salvando) {
                return;
            }

            const formulario = event.currentTarget;

            registro.erro = '';

            const mensagemValidacao = this.validar(registro);

            if (mensagemValidacao) {
                registro.erro = mensagemValidacao;

                const nomeCampo =
                    registro.situacaoFormulario === 'justificada'
                        ? 'observacao'
                        : 'situacao';

                formulario.elements
                    .namedItem(nomeCampo)
                    ?.focus();

                return;
            }

            registro.salvando = true;

            try {
                const resposta = await fetch(formulario.action, {
                    method: 'POST',

                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },

                    credentials: 'same-origin',
                    body: new FormData(formulario),
                });

                const dados = await resposta.json()
                    .catch(() => ({}));

                if (!resposta.ok) {
                    throw new Error(
                        this.obterMensagemErro(dados)
                    );
                }

                if (!dados.presenca) {
                    throw new Error(
                        'O servidor retornou uma resposta inválida.'
                    );
                }

                registro.situacao =
                    dados.presenca.situacao;

                registro.rotulo =
                    dados.presenca.rotulo;

                registro.observacao =
                    dados.presenca.observacao ?? '';

                registro.registradoPor =
                    dados.presenca.registrado_por;

                registro.atualizadoPor =
                    dados.presenca.atualizado_por;

                registro.situacaoFormulario =
                    registro.situacao;

                registro.observacaoFormulario =
                    registro.observacao;

                if (dados.totais) {
                    this.totais = normalizarTotais(
                        dados.totais
                    );
                }

                this.exibirMensagem(
                    dados.message
                    ?? 'Presença salva com sucesso.'
                );
            } catch (erro) {
                registro.erro = erro instanceof Error
                    ? erro.message
                    : 'Não foi possível salvar a presença.';
            } finally {
                registro.salvando = false;
            }
        },

        exibirMensagem(mensagem) {
            this.mensagem = mensagem;

            clearTimeout(this.temporizadorMensagem);

            this.temporizadorMensagem = setTimeout(() => {
                this.mensagem = '';
            }, 3000);
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
                ?? 'Não foi possível salvar a presença.';
        },
    };
}
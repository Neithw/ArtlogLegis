import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('formularioUsuario', ({
    pacotes,
    papelInicial = '',
    permissoesIniciais = []
}) => ({
    pacotes,

    papelId: String(papelInicial),

    permissoesSelecionadas: permissoesIniciais.map(String),

    aplicarPacote(novoPapelId) {
        this.papelId = String(novoPapelId);

        this.permissoesSelecionadas = [
            ...(this.pacotes[this.papelId] ?? [])
        ];
    }

}));

Alpine.start();
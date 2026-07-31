import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('formularioUsuario', ({
    pacotes = {},
    papelInicial = '',
    permissoesIniciais = []
}) => ({
    pacotes,

    papelId: String(papelInicial ?? ''),

    permissoesSelecionadas: permissoesIniciais.map(Number),

    aplicarPacote(roleId) {
        const permissoesDoPacote = this.pacotes[String(roleId)] ?? [];

        this.permissoesSelecionadas = permissoesDoPacote.map(Number);
    }

}));

Alpine.start();
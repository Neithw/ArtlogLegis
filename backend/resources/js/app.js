import {
    Livewire,
    Alpine,
} from '../../vendor/livewire/livewire/dist/livewire.esm';

import formularioUsuario from './components/formularioUsuario';
import formularioVereador from './components/formularioVereador';
import formularioMandato from './components/formularioMandato';
import formularioProposicao from './components/formularioProposicao';
import formularioSessao from './components/formularioSessao';
import campoPalavrasChave from './components/campoPalavrasChave';
import pautaOrdenavel from './components/pautaOrdenavel';
import presencasSessao from './components/presencasSessao';
import votacaoPlenario from './components/votacaoPlenario';

import grupoSidebar from './components/grupoSidebar';
import criarLayoutStore from './stores/layout';

Alpine.data('formularioUsuario', formularioUsuario);
Alpine.data('formularioVereador', formularioVereador);
Alpine.data('formularioMandato', formularioMandato);
Alpine.data('formularioProposicao', formularioProposicao);
Alpine.data('formularioSessao', formularioSessao);
Alpine.data('campoPalavrasChave', campoPalavrasChave);
Alpine.data('pautaOrdenavel', pautaOrdenavel);
Alpine.data('presencasSessao', presencasSessao);
Alpine.data('votacaoPlenario', votacaoPlenario);

Alpine.data('grupoSidebar', grupoSidebar);
Alpine.store('layout', criarLayoutStore());

const sincronizarLayout = () => {
    const layoutStore = Alpine.store('layout');

    layoutStore.applyTheme();
    layoutStore.applySidebar();
};

document.addEventListener('livewire:navigating', (event) => {
    event.detail.onSwap(sincronizarLayout);
});

document.addEventListener(
    'livewire:navigated',
    sincronizarLayout
);

Livewire.start();
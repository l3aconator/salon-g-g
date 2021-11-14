import MicroModal from 'micromodal';

import { onDocumentReady } from './utils';

// Components
import Menu from './components/menu';
import Header from './components/header';
import Toggler from './components/toggler';
import CartHandler from './components/cart-handler';
import Carousel from './components/carousel';
import Modal from './components/modal';
import Form from './components/form';
import ProductGallery from './components/product-gallery';
import VideoModal from './components/videomodal';

export const modals = {};

onDocumentReady(() => {
  new Menu();
  new Toggler();
  new CartHandler();
  new Header();

  document.querySelectorAll('.js-carousel').forEach(el => new Carousel(el, { loop: false }));
  document.querySelectorAll('.js-modal').forEach(el => {
    const m = new Modal(el);

    if (el.id) {
      modals[el.id] = m;
    }
  });
  document.querySelectorAll('.wpcf7').forEach(el => new Form(el));
  document.querySelectorAll('.js-product-gallery').forEach(el => new ProductGallery(el));
  document.querySelectorAll('.js-video-modal[data-video]').forEach(el => new VideoModal(el));
  MicroModal.init();

  document.querySelector('.js-buttonSwapSignup').addEventListener('click', () => {
    MicroModal.close('login-modal');
    MicroModal.show('signup-modal');
  });

  document.querySelector('.js-buttonSwapLogin').addEventListener('click', () => {
    MicroModal.close('signup-modal');
    MicroModal.show('login-modal');
  });
});

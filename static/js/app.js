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

function runQty(woocommerceTrigger) {
  const cart = document.querySelector('[name="update_cart"]');
  woocommerceTrigger.querySelectorAll('.qty').forEach(el => {
    el.addEventListener('change', () => cart.click());

    if (
      document.querySelectorAll(`#${el.getAttribute('id')}-decrease_qty`).length !== 0 ||
      document.querySelectorAll(`#${el.getAttribute('id')}-increase_qty`).length !== 0
    ) {
      return;
    }
    const minusElement = document.createElement('button');
    minusElement.textContent = '-';
    minusElement.setAttribute('id', `${el.getAttribute('id')}-decrease_qty`);
    minusElement.setAttribute('class', `${el.getAttribute('id')} decrease_qty`);
    minusElement.setAttribute('type', 'button');
    el.parentNode.insertBefore(minusElement, el);
    const plusElement = document.createElement('button');
    plusElement.textContent = '+';
    plusElement.setAttribute('id', `${el.getAttribute('id')}-increase_qty`);
    plusElement.setAttribute('class', `${el.getAttribute('id')} increase_qty`);
    plusElement.setAttribute('type', 'button');
    el.parentNode.appendChild(plusElement, el);

    minusElement.addEventListener('click', me => {
      const id = document.querySelector(`#${me.target.id.split('-')['0']}`);
      id.setAttribute('value', parseInt(id.value) === 0 ? 0 : id.value--);
      cart.removeAttribute('disabled');
      cart.click();
    });

    plusElement.addEventListener('click', me => {
      const id = document.querySelector(`#${me.target.id.split('-')['0']}`);
      id.setAttribute('value', parseInt(id.value) === 0 ? 0 : id.value++);
      cart.removeAttribute('disabled');
      cart.click();
    });
  });
}

onDocumentReady(() => {
  MicroModal.init();
  const woocommerceTrigger = document.querySelector('.wp-block-editor-content');
  const signupTrigger = document.querySelector('.js-buttonSwapSignup');
  const loginTrigger = document.querySelector('.js-buttonSwapSignup');

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

  if (signupTrigger) {
    signupTrigger.addEventListener('click', () => {
      MicroModal.close('login-modal');
      MicroModal.show('signup-modal');
    });
  }

  if (loginTrigger) {
    loginTrigger.addEventListener('click', () => {
      MicroModal.close('signup-modal');
      MicroModal.show('login-modal');
    });
  }

  // Update cart on qty change vs. user having to click button
  if (woocommerceTrigger) {
    const cartBtn = document.querySelector('.actions');

    if (cartBtn) {
      cartBtn.style.display = 'none';
    }

    woocommerceTrigger.addEventListener('mouseover', () => {
      const cartBtn = document.querySelector('.actions');
      cartBtn.style.display = 'none';
      runQty(woocommerceTrigger);
    });
  }
});

class Modal {
  constructor(el) {
    this.modal = el;
    this.modalContent = el.querySelector('.modal__content');
    this.responseOutput = el.querySelector('.wpcf7-response-output');
    this.form = el.querySelector('.wpcf7-form');
    this.active = false;

    this.settings = {
      fixedWidth: false,
    };

    if (el.dataset.fixedWidth) {
      this.settings.fixedWidth = true;
    }

    this.setupTriggers();
  }

  setupTriggers() {
    document
      .querySelectorAll('.js-modal-trigger')
      .forEach(el => el.addEventListener('click', this.triggerModal.bind(this)));
  }

  triggerModal() {
    this.modal.classList.toggle('modal--visible');
    this.active = !this.active;

    if (this.active) {
      if (!this.settings.fixedWidth) {
        this.setWidth();
      }
    } else if (this.modalContent) {
      if (this.onCloseFn) {
        this.onCloseFn();
      }

      setTimeout(() => {
        this.unsetWidth();
        this.resetForm();
      }, 500);
    }
  }

  onClose(fn) {
    this.onCloseFn = fn;
  }

  setWidth() {
    if (!this.modalContent) {
      return;
    }

    const width = this.modalContent.offsetWidth;
    this.modalContent.style.width = `${width}px`;
  }

  unsetWidth() {
    if (!this.modalContent) {
      return;
    }

    this.modalContent.style.width = null;
  }

  resetForm() {
    if (this.responseOutput) {
      this.responseOutput.innerText = '';
      this.responseOutput.style = null;
    }

    if (this.form) {
      this.form.classList.add('init');
      this.form.classList.remove('invalid');
      this.form.classList.remove('sent');

      this.form.querySelectorAll('input.wpcf7-not-valid').forEach(el => {
        el.classList.remove('wpcf7-not-valid');
        el.removeAttribute('aria-invalid');
      });
    }

    document.querySelectorAll('.wpcf7-not-valid-tip').forEach(el => el.remove());
  }
}

export default Modal;

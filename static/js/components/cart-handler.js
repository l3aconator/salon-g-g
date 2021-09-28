class CartHandler {
  constructor() {
    this.flag = document.querySelector('.cart-flag__track');
    this.frame =
      document.querySelector('.main-frame') || document.querySelector('.home .single--page');

    if (!this.frame) {
      return;
    }

    this.setupListeners();
  }

  setupListeners() {
    window.jQuery(document.body).on('added_to_cart', this.handleAddToCart.bind(this));
  }

  handleAddToCart(event, { markup }) {
    if (!markup) {
      return;
    }

    this.frame.classList.add('inserting-new-flag');

    const frag = document.createRange().createContextualFragment(markup);
    const newFlag = frag.firstChild;

    // If there's already a flag, remove it.
    if (this.flag) {
      this.flag.remove();
    }

    this.frame.prepend(newFlag);
    this.flag = newFlag;

    setTimeout(() => this.frame.classList.remove('inserting-new-flag'), 10);
  }
}

export default CartHandler;

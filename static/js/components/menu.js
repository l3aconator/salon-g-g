class Menu {
  constructor() {
    this.open = false;
    this.triggers = document.querySelectorAll('.js-toggle-menu');
    this.body = document.body;

    this.init();
  }

  init() {
    this.triggers.forEach(el =>
      el.addEventListener('click', () => {
        this.body.classList.toggle('menu--open');
        this.open = !this.open;

        if (!this.open) {
          document
            .querySelectorAll('.item--expanded')
            .forEach(el => el.classList.remove('item--expanded'));
        }
      }),
    );
  }
}

export default Menu;

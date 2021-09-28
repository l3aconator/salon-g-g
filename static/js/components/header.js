class Header {
  constructor() {
    this.header = document.querySelector('#site-masthead');

    if (!this.header) {
      return;
    }

    this.setupObserver();
  }

  setupObserver() {
    const options = {
      root: null,
      rootMargin: '0px',
      threshold: [0, 0.1],
    };

    this.observer = new IntersectionObserver(this.handleIntersection.bind(this), options);
    this.observer.observe(document.querySelector('.header-intersection'));
  }

  handleIntersection(entries) {
    entries.forEach(entry => {
      this.fixed = !entry.intersectionRatio > 0;
      if (this.fixed) {
        this.header.classList.add('site-header--fixed');
      } else {
        this.header.classList.remove('site-header--fixed');
      }
    });
  }
}

export default Header;

const ACTIVE_BUTTON_CLASS = 'product-gallery__thumb--active';
const ACTIVE_IMG_CLASS = 'product-gallery__img--active';

class ProductGallery {
  constructor(el) {
    this.gallery = el;
    this.activeImage = el.querySelector('.js-initial-img');
    this.thumbs = el.querySelectorAll('button[data-src]');

    if (!this.thumbs || !this.activeImage) {
      return;
    }

    this.availableImages = {
      [this.activeImage.getAttribute('src')]: this.activeImage,
    };

    this.viewport = this.activeImage.parentElement;

    this.setupListeners();
  }

  setupListeners() {
    this.thumbs.forEach(el => el.addEventListener('click', this.handleThumbClick.bind(this)));
  }

  handleThumbClick(event) {
    const { src, alt } = event.currentTarget.dataset;

    Array.from(event.currentTarget.parentElement.parentElement.children).forEach(li => {
      const button = li.firstElementChild;
      if (button) {
        if (button === event.currentTarget) {
          button.classList.add(ACTIVE_BUTTON_CLASS);
        } else {
          button.classList.remove(ACTIVE_BUTTON_CLASS);
        }
      }
    });

    if (this.availableImages[src]) {
      this.handleTransition(this.availableImages[src]);
    } else {
      const img = new Image();

      img.onload = () => {
        this.viewport.appendChild(img);
        setTimeout(() => this.handleTransition(img), 10);
      };

      img.src = src;
      img.alt = alt;
      img.classList.add('product-gallery__img');
      this.availableImages[src] = img;
    }
  }

  handleTransition(newImage) {
    this.activeImage.classList.remove(ACTIVE_IMG_CLASS);
    newImage.classList.add(ACTIVE_IMG_CLASS);
    this.activeImage = newImage;
  }
}

export default ProductGallery;

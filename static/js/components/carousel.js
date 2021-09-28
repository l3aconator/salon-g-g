const MQ_SM = 600;

class Carousel {
  constructor(el, args = {}) {
    this.el = el;

    const defaultOptions = {
      loop: false,
      size: 'full',
    };

    const dataOptions = this.getDataOptions();

    this.options = { ...defaultOptions, ...args, ...dataOptions };

    this.track = this.el.querySelector('[data-track]');
    this.controls = this.el.querySelectorAll('button[data-control]');
    this.slides = this.el.querySelectorAll('[data-slide]');
    this.slideCount = this.slides.length;

    // If we've got a looper, account for the clone slides.
    if (this.options.loop) {
      this.slideCount -= 4;
    }

    if (this.options.size === 'half') {
      if (this.smallScreen()) {
        this.track.style.transform = `translate3d(-200%, 0, 0)`;
      } else {
        this.slideCount -= 1;
      }
    } else if (this.options.size === 'quarter') {
      if (this.smallScreen()) {
        this.track.style.transform = `translate3d(-100%, 0, 0)`;
        this.slideCount -= 1;
      } else {
        this.slideCount -= 3;
      }
    }

    if (!this.track) {
      console.warn('Carousels need a track element with the `[data-track]` attribute.');
      return;
    }

    if (this.slideCount < 2) {
      console.warn('Carousels need at least two slides.');
      return;
    }

    // State
    this.currentSlide = 0;
    this.controlsActive = true;

    setTimeout(this.size.bind(this), 1000);
    this.setupListeners();
  }

  size() {
    const firstImage = this.track.querySelector('.slide__image img');

    if (!firstImage) {
      return;
    }

    const height = firstImage.offsetHeight;

    if (height) {
      this.controls.forEach(btn => {
        btn.style.height = `${height}px`;
        btn.classList.add('sized');
      });
    }
  }

  /**
   * Set up options passed to the carousel DOM node.
   *
   * @return object
   */
  getDataOptions() {
    const { dataset } = this.el;
    const dataOptions = {};

    Object.keys(dataset)
      .filter(optname => optname.indexOf('carousel') === 0)
      .forEach(optname => {
        const optkey = optname.substr(8).toLowerCase();
        let optvalue = dataset[optname];

        if (optvalue === 'true') {
          optvalue = true;
        } else if (optvalue === 'false') {
          optvalue = false;
        }

        dataOptions[optkey] = optvalue;
      });

    return dataOptions;
  }

  /**
   * Init event listeners.
   *
   * @return void
   */
  setupListeners() {
    this.controls.forEach(btn => btn.addEventListener('click', this.handleControls.bind(this)));
    this.track.addEventListener('transitionend', this.handleTransitionEnd.bind(this));
  }

  /**
   * Handle a click on one of the directional controls.
   *
   * @param {object} event `click` event.
   */
  handleControls(event) {
    if (!this.controlsActive) {
      return;
    }

    const dir = event.currentTarget.dataset.control;

    if (dir === 'next') {
      this.goto(this.currentSlide + 1);
    } else {
      this.goto(this.currentSlide - 1);
    }
  }

  /**
   * Handle when the slide transition ends.
   *
   * @param {object} event `transitionend` event.
   *
   * @return void
   */
  handleTransitionEnd(event) {
    // Only do stuff when the track has finished animating.
    if (event.target !== this.track) {
      return;
    }

    // If we're not looping, no need to manage the carousel state.
    if (!this.options.loop) {
      return;
    }

    // Reactivate the controls.
    this.controlsActive = true;

    const isSmallScreen = this.smallScreen();

    const realSlideCount = this.slideCount + 1;

    if (
      (this.options.size === 'half' && !isSmallScreen) ||
      (this.options.size === 'quarter' && isSmallScreen)
    ) {
      if (this.currentSlide < -1) {
        this.resetClone(realSlideCount);
        this.currentSlide = realSlideCount - 2;
      } else if (this.currentSlide > realSlideCount - 2) {
        this.resetClone(1);
        this.currentSlide = -1;
      }
    } else if (this.options.size === 'quarter') {
      if (this.currentSlide < -1) {
        this.resetClone(realSlideCount - 1);
        this.currentSlide = realSlideCount - 3;
      } else if (this.currentSlide > realSlideCount - 3) {
        this.resetClone(1);
        this.currentSlide = -1;
      }
    } else {
      if (this.getCurrentPosition() < 2) {
        // Go to the last slide.
        this.resetClone(this.slideCount + 1);
        this.currentSlide = this.slideCount - 1;
      } else if (this.getCurrentPosition() >= this.slideCount + 2) {
        // Go to the first slide.
        this.resetClone(2);
        this.currentSlide = 0;
      }
    }
  }

  /**
   * Moves the track to the reset spot once we've hit a clone.
   *
   * @param {int} position New position for the track.
   *
   * @return void
   */
  resetClone(position) {
    this.track.style.transition = 'none';
    this.track.style.transform = `translate3d(${this.newTrackPosition(position)}%, 0, 0)`;
    setTimeout(() => this.track.style.removeProperty('transition'), 0);
  }

  /**
   * Move to a specified slide.
   *
   * @param {int} slide The slide to move to.
   *
   * @return Carousel
   */
  goto(slide) {
    // If we're not looping, don't scroll past the carousel edges.
    if (!this.options.loop && (slide < 0 || slide >= this.slideCount)) {
      return;
    }

    // Deactivate the controls if we're looping.
    if (this.options.loop) {
      this.controlsActive = false;
    }

    // Update the current slide.
    this.currentSlide = slide;

    // Move the track.
    this.track.style.transform = `translate3d(${this.newTrackPosition()}%, 0, 0)`;

    return this;
  }

  /**
   * Get the value to move the slider.
   *
   * @param {int} position The slide position to move to.
   *
   * @return int
   */
  newTrackPosition(position = null) {
    const currentSlide = position || this.getCurrentPosition();
    let constant = -100;

    if (this.options.size === 'half' && !this.smallScreen()) {
      constant = -50;
    }

    if (this.options.size === 'quarter') {
      if (this.smallScreen()) {
        constant = -50;
      } else {
        return (currentSlide + 1) * -25;
      }
    }

    return currentSlide * constant;
  }

  /**
   * Utility method for getting the right slide number for the current context.
   *
   * @return int
   */
  getCurrentPosition() {
    // If we're looping, offset the current slide by two to account for the cloned
    // slide items.
    if (this.options.loop) {
      return this.currentSlide + 2;
    }

    return this.currentSlide;
  }

  /**
   * Media query for small screens.
   *
   * @return boolean
   */
  smallScreen() {
    return window.innerWidth < MQ_SM;
  }
}

export default Carousel;

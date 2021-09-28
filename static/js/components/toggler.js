/**
 * A general class that allows you to declaratively set a class toggle on a button or other
 * clickable element.
 */
class Toggler {
  constructor() {
    this.body = document.body;
    this.toggleElements = document.querySelectorAll('.js-toggle-class');
    this.boundOnClick = this.onClick.bind(this);

    this.bindEvents();
  }

  bindEvents() {
    this.toggleElements.forEach(el => el.addEventListener('click', this.boundOnClick));
  }

  onClick(e) {
    const src = e.target.closest('.js-toggle-class');
    const { tElemPos, tSelector, tClass } = src.dataset;

    // Since modifying classes on the body is common, check for that selector first.
    if (tSelector === 'body') {
      this.toggle(this.body, tClass);
      return;
    }

    // Depending on the position of the element, determine the element that should have its class
    // toggled and toggle it.
    switch (tElemPos) {
      case 'child':
        this.toggle(src.querySelectorAll(tSelector), tClass);
        break;
      case 'parent':
        this.toggle(src.closest(tSelector), tClass);
        break;
      default:
        this.toggle(src, tClass);
        break;
    }
  }

  /**
   * Toggle a class on an element or a NodeList of elements.
   *
   * @param {Element|NodeList} el Can be a singular element, or a NodeList of elements.
   * @param {String} tclass The class to apply to the elements.
   *
   * @return void
   */
  toggle(el, tclass) {
    // Since el can be a NodeList, check if it is and, if so, iterate to toggle the class on all
    // elements in the NodeList. Otherwise, el should just be a singular element so we can toggle
    // the class directly on it.
    if (el.length > 1) {
      el.forEach(childEl => childEl.classList.toggle(tclass));
      return;
    }

    el.classList.toggle(tclass);
  }
}

export default Toggler;

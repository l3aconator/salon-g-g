import { onDocumentReady } from '../utils';

onDocumentReady(() => {
  const filters = document.querySelectorAll('.control__sort');

  if (filters) {
    filters.forEach(filter => {
      const params = window.location.search.replace('?', '');
      const hoverControl = filter.querySelector('.control__active');
      const selectControl = filter.querySelector('.control__select option:first-child');

      const activeItem = Array.from(filter.querySelectorAll('[data-filter]')).filter(
        el => el.dataset.filter === params,
      );

      if (hoverControl && activeItem.length !== 0) {
        hoverControl.innerHTML = `Sort by ${activeItem[0].innerHTML}`;
      }

      if (selectControl && activeItem.length !== 0) {
        selectControl.innerHTML = `Sort by ${activeItem[0].innerHTML}`;
      }
    });
  }
});

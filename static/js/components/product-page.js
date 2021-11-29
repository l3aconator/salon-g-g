import { onDocumentReady } from '../utils';

function findVariantLabelToAppend(selector) {
  const splitAttribute = selector.dataset.attribute_name.split('attribute_')[1];

  return splitAttribute || undefined;
}

onDocumentReady(() => {
  const attachmentButton = document.querySelector('.wcpoa_attachmentbtn');
  const showMore = document.querySelector('#product__showMore');
  const dimensionsToggle = document.querySelector('.product__dimensionsHeading');
  const variationSelectorAll = document.querySelectorAll('.tawcvs-swatches');

  // Plugin doesn't allow customizing the text
  // so we have to monkey patch through JS
  if (attachmentButton) {
    attachmentButton.innerHTML = 'Download Specification Sheet';
  }

  // Handle the expansion / collapse of the truncation on the product page
  if (showMore) {
    showMore.addEventListener('click', el => {
      const contentElMain = document.querySelector('.product__contentShort');
      const contentEl = document.querySelector('.product__contentShort div');

      if (el.target.dataset.open === 'true') {
        contentEl.innerHTML = contentElMain.dataset.contentShort;
        el.target.setAttribute('data-open', false);
        el.target.innerHTML = 'Read more +';
      } else {
        contentEl.innerHTML = contentElMain.dataset.content;
        el.target.setAttribute('data-open', true);
        el.target.innerHTML = 'Read less -';
      }
    });
  }

  // If a product has dimension
  // handle the expansion / collapse of the toggle
  if (dimensionsToggle) {
    dimensionsToggle.addEventListener('click', () => {
      const button = document.querySelector('.product__dimensionsToggle');
      if (button.classList.contains('open')) {
        button.innerHTML = '+';
      } else {
        button.innerHTML = '-';
      }

      document.querySelector('.product__dimensionsContent').classList.toggle('open');
      button.classList.toggle('open');
    });
  }

  // Handle the addition
  // of the selected variant pretty name next
  // to the variant field label
  // this JS assumes you are using the variant plugin
  // that converts the swatches from dropdowns
  if (variationSelectorAll) {
    variationSelectorAll.forEach(el => {
      el &&
        el.querySelectorAll('.swatch').forEach(me => {
          me &&
            me.addEventListener('click', ({ target }) => {
              const swatch = target;
              const classlist = swatch.classList;
              const attributeParent = swatch.parentNode.parentNode;
              const type = findVariantLabelToAppend(attributeParent);
              const label = document.querySelector(`label[for="${type}"]`);
              const handleString = ` | <span class="product__selectedVariant">${swatch.dataset.value}</span>`;

              if (classlist.contains('disabled')) {
                return;
              }

              if (classlist.contains('selected')) {
                label.innerHTML = label.innerHTML.replace(handleString, '');
              } else {
                label.innerHTML = `${label.innerHTML.split('|')[0]}${handleString}`;
              }
            });
        });
    });
  }
});

const quoteModal = document.querySelector('.quote__modal');
const quoteModalFields = document.querySelectorAll('.quote__modalFields');
const modalTrigger = document.querySelector('[data-micromodal-trigger="quote-modal"]');

if (modalTrigger) {
  modalTrigger.addEventListener('click', () => {
    const variants = document.querySelectorAll('.product__selectedVariant');
    const variantSelections = document.querySelector('input[name="VariantSelections"]');

    if (variants.length !== 0) {
      variantSelections.value = Array.from(variants)
        .map(el => el.innerHTML)
        .join(' ');
    }
  });
}

if (quoteModal) {
  if (quoteModalFields) {
    quoteModalFields.forEach(el => {
      if (el) {
        document.querySelector(`input[name="${el.name.split('_')[1]}"]`).value = el.value;
      }
    });
  }

  document.addEventListener(
    'wpcf7mailsent',
    () => {
      // awful hack but we have a race condition
      setTimeout(() => {
        document.querySelector('.wpcf7-response-output').innerHTML =
          'Thank you for your request, we will be in touch with a quote shortly.';
      }, 75);
    },
    false,
  );
}

const quoteModal = document.querySelector('.quote__modal');
const quoteModalFields = document.querySelectorAll('.quote__modalFields');
const modalTrigger = document.querySelector('[data-micromodal-trigger="quote-modal"]');

if (modalTrigger) {
  modalTrigger.addEventListener('click', () => {
    document.querySelector('input[name="VariantSelections"]').value = document.querySelector(
      'input[name="variation_id"]',
    ).value;
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
}

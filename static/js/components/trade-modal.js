import MicroModal from 'micromodal';

const urlSearchParams = new URLSearchParams(window.location.search);
const params = Object.keys(Object.fromEntries(urlSearchParams.entries()));
const signupSwitchEl = document.querySelector('#signup-modal #switch');
const keyName = 'showTradeAccountModal';

function callback(mutationsList) {
  mutationsList.forEach(mutation => {
    if (mutation.attributeName === 'class') {
      if (mutation.target.classList.contains('sent')) {
        MicroModal.close('trade-modal-main');
        localStorage.removeItem(keyName);
      }
    }
  });
}

const mutationObserver = new MutationObserver(callback);

// sign up modal trigger trade application
if (signupSwitchEl) {
  signupSwitchEl.addEventListener('click', el => {
    localStorage.setItem('showTradeAccountModal', el.target.checked);
  });
}

document.querySelector('#trade-modal-main .modal__close').addEventListener('click', () => {
  localStorage.removeItem(keyName);
});
if (params.includes('trade-modal')) {
  MicroModal.show('trade-modal-main');
}

if (localStorage.getItem(keyName) === 'true') {
  MicroModal.show('trade-modal-main');
}

mutationObserver.observe(document.querySelector('#trade-modal-main .wpcf7-form'), {
  attributes: true,
});

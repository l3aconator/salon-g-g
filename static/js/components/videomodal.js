import { modals } from '../app';

class VideoModal {
  constructor(el) {
    this.modal = modals[`modal-video-${el.dataset.video}`];
    this.player = null;

    if (!this.modal) {
      return;
    }

    this.modal.onClose(() => {
      if (this.player) {
        this.player.pause();
        this.player.setCurrentTime(0);
      }
    });

    el.addEventListener('click', this.handleClick.bind(this));
  }

  loadVimeo() {
    if (window.Vimeo) {
      return Promise.resolve(window.Vimeo);
    }

    return new Promise(resolve => {
      const script = document.createElement('script');
      script.src = 'https://player.vimeo.com/api/player.js';
      script.onload = () => resolve(window.Vimeo);
      document.body.appendChild(script);
    });
  }

  handleClick(event) {
    if (this.player) {
      this.player.play();
      return;
    }

    const { video } = event.currentTarget.dataset;
    const wrapper = this.modal.modalContent.querySelector('.modal__video-wrapper');

    this.loadVimeo().then(vimeo => {
      this.player = new vimeo.Player(wrapper || null, { id: video });
      this.player.play();
    });
  }
}

export default VideoModal;

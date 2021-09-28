class Form {
  constructor(el) {
    this.form = el;

    this.setupListeners();
  }

  setupListeners() {
    this.form.addEventListener('submit', this.handleSubmit.bind(this));
    this.form.addEventListener('wpcf7submit', this.handleSubmitted.bind(this));
  }

  handleSubmit() {
    this.form.classList.add('submitting');
  }

  handleSubmitted() {
    this.form.classList.remove('submitting');
  }
}

export default Form;

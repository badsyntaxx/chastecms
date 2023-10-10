class FancyForm {
    constructor() {console.log('fancy form started');}

    fancyfy(settings) {
        this.settings = this.extend({
            form: 'form',
            exclude: [],
            buttons: []
        }, settings);

        this.styleForm();
        this.handleEvents();
        this.blurPassword();
    }

    styleForm() {
        const {form, include, exclude, buttons} = this.settings;
        const inputs = document.querySelectorAll(`${form} input, ${form} select, ${form} textarea, ${form} button`);
        document.querySelector(form).classList.add('ff-form');

        inputs.forEach(v => {
            switch (v.type) {
                case 'checkbox':
                    v.insertAdjacentHTML('afterEnd', '<span class="ff-checkbox"></span>');
                    v.style.opacity = 0;
                break;
                case 'radio':
                    v.insertAdjacentHTML('afterEnd', '<span class="ff-radio"></span>');
                    v.style.opacity = 0;
                break;  
                case 'select-one':
                    v.classList.add('ff-select');
                    v.parentElement.querySelector('label').classList.add('ff-label');
                break;
                case 'textarea':
                    v.classList.add('ff-textarea');
                    v.parentElement.querySelector('label').classList.add('ff-label');
                break;
                case 'time':
                    v.parentElement.querySelector('label').classList.add('ff-label');
                    v.classList.add('ff-input');
                    v.classList.add('ff-time');
                    v.type = 'text';
                break;
                case 'date':
                    v.parentElement.querySelector('label').classList.add('ff-label');
                    v.classList.add('ff-input');
                    v.classList.add('ff-date');
                    v.type = 'text';
                break;
                case 'range':
                    v.parentElement.querySelector('label').classList.add('ff-label');
                    v.parentElement.querySelector('label').classList.add('ff-label-shrink');
                    v.classList.add('ff-input');
                break;
                case 'button':
                case 'submit':
                    v.parentElement.querySelector('button').classList.add('ff-button');
                break
                default:
                    v.classList.add('ff-input');
                    if (v.type != 'color') v.parentElement.querySelector('label').classList.add('ff-label');
                break;
            }
        });

        exclude.forEach(e => {
            inputs.forEach(v => {
                if (v.classList.contains(e.replace('.', '')) || v.id == e.replace('#', '')) {
                    switch (v.type) {
                        case 'checkbox':
                            v.nextElementSibling.remove();
                            v.style.opacity = 1;
                        break;
                        case 'radio':
                            v.nextElementSibling.remove();
                            v.style.opacity = 1;
                        break;  
                        case 'select-one':
                            v.classList.remove('ff-select');
                            v.parentElement.querySelector('label').classList.remove('ff-label');
                        break;
                        case 'textarea':
                            v.classList.remove('ff-textarea');
                            v.parentElement.querySelector('label').classList.remove('ff-label');
                        break;
                        case 'time':
                            v.parentElement.querySelector('label').classList.remove('ff-label');
                            v.classList.remove('ff-input');
                            v.classList.remove('ff-time');
                            v.type = 'text';
                        break;
                        case 'date':
                            v.parentElement.querySelector('label').classList.remove('ff-label');
                            v.classList.remove('ff-input');
                            v.classList.remove('ff-date');
                            v.type = 'text';
                        break;
                        case 'range':
                            v.parentElement.querySelector('label').classList.remove('ff-label');
                            v.parentElement.querySelector('label').classList.remove('ff-label-shrink');
                            v.classList.remove('ff-input');
                        break;
                        case 'button':
                        case 'submit':
                            v.parentElement.querySelector('button').classList.remove('ff-button');
                        break
                        default:
                            v.classList.remove('ff-input');
                            if (v.type != 'color') v.parentElement.querySelector('label').classList.remove('ff-label');
                        break;
                    }
                }
            });
        });

        buttons.forEach(v => {
            document.querySelectorAll(v).forEach(b => b.classList.add('ff-button'));
        });

        return this.checkFields();
    }

    checkFields() {
        const labels = document.querySelectorAll(`${this.settings.form} .ff-label`);
        const inputs = document.querySelectorAll(`${this.settings.form} input`);

        labels.forEach(v => {
            if (v.nextElementSibling && v.nextElementSibling.value != '') v.parentElement.querySelector('.ff-label').classList.add('ff-label-shrink');
        });
        inputs.forEach(v => {
            if (v.type == 'checkbox' && v.checked == true) v.nextSibling.classList.add('checked');
            else if (v.type == 'checkbox' && v.checked == false) v.nextSibling.classList.remove('checked');
        });
    }

    handleEvents() {
        const inputs = document.querySelectorAll(`${this.settings.form} input, ${this.settings.form} select, ${this.settings.form} textarea`);
        const labels = document.querySelectorAll('.ff-label');

        labels.forEach(v => {
            v.addEventListener('mouseover', (e) => {
                if (e.target.tagName === 'LABEL') e.target.style.cursor = 'text';
            });
            v.addEventListener('click', (e) => {
                if (e.target.tagName === 'LABEL') {
                    if (e.target.nextElementSibling) e.target.nextElementSibling.focus();
                    if (e.target.parentElement.querySelector('input')) e.target.parentElement.querySelector('input').click();
                }
            });
        });

        inputs.forEach(v => {
            v.addEventListener('focus', () => {
                const label = v.parentElement.querySelector('.ff-label');
                if (v.type == 'select') v.parentElement.querySelector('.ff-label').classList.add('ff-label-shrink');
                if (v.type != 'checkbox' && v.type != 'radio' && v.type != 'color' && v.type != 'file') {
                    if (label) label.classList.add('ff-label-shrink');
                }
                if (v.type == 'textarea') v.parentElement.querySelector('label').classList.add('ff-label-shrink');
                if (v.classList.contains('ff-date')) v.type = 'date';
                if (v.classList.contains('ff-time'))  v.type = 'time';
            });

            v.addEventListener('blur', () => {
                const label = v.parentElement.querySelector('.ff-label');
                if (!v.value && label) v.parentElement.querySelector('.ff-label').classList.remove('ff-label-shrink');
                if (v.name == 'date' || v.name == 'time') v.type = 'text';
            });

            if (v.nextElementSibling && v.nextElementSibling.classList.contains('ff-radio')) {
                v.nextElementSibling.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    if (v.type == 'radio' && v.checked == false) {
                        document.querySelectorAll('.ff-radio').forEach(r => {
                            r.classList.remove('checked');
                            r.checked = false;
                        });
                        v.checked = true;
                        v.nextElementSibling.classList.add('checked');
                    } else if (v.type == 'radio' && v.checked == true) {
                        v.checked = false;
                        v.nextElementSibling.classList.remove('checked');
                    }   
                    
                });
            }

            if (v.nextElementSibling && v.nextElementSibling.classList.contains('ff-checkbox')) {
                v.nextElementSibling.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    if (v.type == 'checkbox' && v.checked == false) {
                        v.checked = true;
                        v.nextElementSibling.classList.add('checked');
                    } else if (v.type == 'checkbox' && v.checked == true) {
                        v.checked = false;
                        v.nextElementSibling.classList.remove('checked');
                    }
                });
            }

            v.addEventListener('keyup', () => this.checkFields());

            v.addEventListener('change', () => {
                this.checkFields();
                /* if (v.type == 'file') {
                    let filename = v.value.split('\\'); WTF IS THIS
                } */
            });            
        }); 
    }

    blurPassword() {
        setTimeout(() => {
            let input = document.querySelector('input[type="password"]');
            if (input && input.value) input.focus().blur();
        }, 300);
    }

    extend(defaults, settings) {
        for (let prop in settings) {
            if (typeof settings[prop] === 'object') {
                defaults[prop] = this.extend(defaults[prop], settings[prop]);
            } else {
                defaults[prop] = settings[prop];
            }
        }
        return defaults;
    }
}

const fancyform = new FancyForm();
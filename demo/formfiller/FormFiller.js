class FormFiller {

    constructor(settings) {
        this.settings = this.extend({
            form: 'form',
            url: 'formfiller.php',
            json: {},
            ignore: []
        }, settings);

        this.data = {};
        this.leftovers = null;

        return this.get().then(data => {
            return this.populate(data);
        }).catch(error => {
            return error;
        });
    }
  
    async get() {
        const parent = this;
        const response = await fetch(this.settings.url);
        let data = null;

        if (response.ok) data = await response.json();
        else return response;

        if (data) return Object.assign({}, data, parent.settings.json); // Combine url data object with json data object
        else return parent.settings.json;   
    }

    populate(data) {
        const parent = this;
        const original_data = {...data};
        const inputs = document.querySelectorAll(this.settings.form + ' input, ' + this.settings.form + ' textarea, ' + this.settings.form + ' select');
        const ignore = this.settings.ignore;
        let unfilled = [];

        // Is the data property empty?
        if (Object.keys(original_data).length === 0) {
            let response = { 
                status: 'Error',
                message: 'Form Filler did not recieve any form data. Be sure to provide the config with json and/or a url',
                target: parent.settings.form
            }
            return response;
        }
        
        // Remove ignored properties from data object
        ignore.forEach(function(v) {
            delete data[v];
        });

        // Fill in the form
        inputs.forEach(function(v) {
            const input = v;
            for (let key in data) {
                if (key == input.name) {
                    switch (input.type) {
                        case 'checkbox':
                            if (data[key] === true) input.checked = true;
                        break;
                        case 'radio':
                            let radio = document.querySelectorAll(parent.settings.form + ' input[name="' + key + '"]');
                            radio.forEach(function(v) { 
                                if (v.value == data[key]) v.checked = true;
                            });
                        break;  
                        case 'select-one':
                        case 'textarea':
                        case 'text':
                        case 'tel':
                        case 'date':
                        case 'time':
                        case 'email':
                        case 'hidden':
                        case 'password': 
                        case 'color':
                            input.value = data[key];
                        break;
                    }
                    delete data[key];
                }
            }
        });

        let num = 0;
        // Create the unfilled array
        inputs.forEach(function(v) {
            switch (v.type) {
                case 'radio': 
                    if (v.checked === false) ++num
                    if (num === 3) unfilled.push(v.name);
                break;
                case 'checkbox': if (v.checked === false) unfilled.push(v.name); break;
                default: if (!v.value) unfilled.push(v.name); break;
            }
        });

        let response = {
            status: 'Success',
            message: 'Form Filler completed successfuly',
            target: parent.settings.form,
            leftovers: data,
            unfilled: [... new Set(unfilled)]
        }
        
        return response;
    }

    /**
     * Extend
     * 
     * Take this classes default parameters and overwrite and add to them with
     * settings from the user.
     * @param {object} defaults - JSON object of default settings
     * @param {object} settings - JSON object of custom settings
     * @return {object}
     */
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
import './bootstrap';

// Autocompletion component
window.setupAutocomplete = function(inputId, optionsUrl, valueField = 'id', displayField = 'name') {
    return {
        search: '',
        results: [],
        selectedId: '',
        selectedLabel: '',
        showResults: false,
        isLoading: false,
        valueField: valueField,
        displayField: displayField,

        init() {
            this.$watch('search', (value) => {
                if (value.length > 1) {
                    this.getResults();
                } else {
                    this.results = [];
                }
            });

            // Si une valeur est déjà sélectionnée, récupérer son label
            if (this.selectedId) {
                this.getSelectedLabel();
            }
        },

        getResults() {
            this.isLoading = true;

            fetch(`${optionsUrl}?q=${encodeURIComponent(this.search)}`)
                .then(response => response.json())
                .then(data => {
                    this.results = data;
                    this.isLoading = false;
                    this.showResults = true;
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche:', error);
                    this.isLoading = false;
                });
        },

        selectOption(option) {
            this.selectedId = option[this.valueField];
            this.selectedLabel = option[this.displayField];
            this.search = option[this.displayField];
            this.showResults = false;

            // Mettre à jour le champ caché
            document.getElementById(inputId).value = option[this.valueField];

            // Déclencher un événement pour indiquer qu'une option a été sélectionnée
            this.$dispatch('option-selected', { id: option[this.valueField], value: option[this.displayField] });
        },

        getSelectedLabel() {
            fetch(`${optionsUrl}?id=${encodeURIComponent(this.selectedId)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        this.selectedLabel = data[0][this.displayField];
                        this.search = data[0][this.displayField];
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération du libellé:', error);
                });
        },

        clear() {
            this.search = '';
            this.selectedId = '';
            this.selectedLabel = '';
            document.getElementById(inputId).value = '';
        }
    };
};

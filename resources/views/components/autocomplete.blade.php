@props([
    'name',
    'label',
    'placeholder',
    'route',
    'value' => null,
    'required' => false,
    'forReservation' => false
])

<div>
    <label for="{{ $name }}_search" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <div class="relative">
        <input
            type="text"
            id="{{ $name }}_search"
            placeholder="{{ $placeholder }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            {{ $required ? 'required' : '' }}
        >
        <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}">
        <div id="{{ $name }}_results" class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-md py-1 text-sm hidden"></div>
    </div>
    @error($name)
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ne pas initialiser plusieurs fois
            if (window.autocompleteInitialized_{{ $name }}) return;
            window.autocompleteInitialized_{{ $name }} = true;

            // Pour ce champ
            let timeout_{{ $name }};

            document.getElementById('{{ $name }}_search').addEventListener('input', function() {
                clearTimeout(timeout_{{ $name }});
                const query = this.value;

                if (query.length < 2) {
                    document.getElementById('{{ $name }}_results').style.display = 'none';
                    document.getElementById('{{ $name }}_results').innerHTML = '';
                    return;
                }

                timeout_{{ $name }} = setTimeout(function() {
                    // Ajouter le paramètre for_reservation si nécessaire
                    let url = '{{ $route }}?q=' + encodeURIComponent(query);
                    @if($forReservation)
                        url += '&for_reservation=1';
                    @endif

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            const resultsContainer = document.getElementById('{{ $name }}_results');
                            resultsContainer.innerHTML = '';

                            if (data.length === 0) {
                                resultsContainer.innerHTML = '<div class="px-4 py-2 text-gray-500">Aucun résultat trouvé</div>';
                            } else {
                                data.forEach(function(item) {
                                    const div = document.createElement('div');
                                    div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer {{ $name }}-result';
                                    div.dataset.id = item.id;
                                    div.dataset.name = item.name;
                                    div.textContent = item.name;
                                    div.addEventListener('click', function() {
                                        document.getElementById('{{ $name }}').value = this.dataset.id;
                                        document.getElementById('{{ $name }}_search').value = this.dataset.name;
                                        resultsContainer.style.display = 'none';
                                    });
                                    resultsContainer.appendChild(div);
                                });
                            }

                            resultsContainer.style.display = 'block';
                        });
                }, 300);
            });

            // Fermer les résultats quand on clique ailleurs
            document.addEventListener('click', function(e) {
                const searchElem = document.getElementById('{{ $name }}_search');
                const resultsElem = document.getElementById('{{ $name }}_results');

                if (!searchElem.contains(e.target) && !resultsElem.contains(e.target)) {
                    resultsElem.style.display = 'none';
                }
            });

            // Charger la valeur initiale si elle existe
            if (document.getElementById('{{ $name }}').value) {
                fetch('{{ $route }}?id=' + document.getElementById('{{ $name }}').value)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('{{ $name }}_search').value = data.name;
                        }
                    });
            }
        });
    </script>
</div>

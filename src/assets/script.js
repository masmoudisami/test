document.addEventListener('DOMContentLoaded', function() {
    const addLineBtn = document.getElementById('addLine');
    const linesTable = document.querySelector('#linesTable tbody');
    const calcInputs = document.querySelectorAll('.calc-input, .qty, .price');
    const clientSearch = document.getElementById('clientSearch');
    const searchResults = document.getElementById('searchResults');
    const clientId = document.getElementById('clientId');
    const selectedClientBox = document.getElementById('selectedClientBox');
    const selectedClientName = document.getElementById('selectedClientName');
    const selectedClientModel = document.getElementById('selectedClientModel');

    if (clientSearch) {
        let debounceTimer;
        
        clientSearch.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            debounceTimer = setTimeout(function() {
                fetch('index.php?route=search_clients&q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(error => console.error('Erreur:', error));
            }, 300);
        });

        function displaySearchResults(clients) {
            searchResults.innerHTML = '';
            
            if (clients.length === 0) {
                searchResults.style.display = 'none';
                return;
            }
            
            clients.forEach(client => {
                const item = document.createElement('div');
                item.className = 'search-result-item';
                item.innerHTML = '<strong>' + escapeHtml(client.name) + '</strong>' +
                    '<small>' + (client.car_model ? 'Modèle: ' + escapeHtml(client.car_model) : '') + 
                    (client.phone ? ' | Tél: ' + escapeHtml(client.phone) : '') + '</small>';
                
                item.addEventListener('click', function() {
                    selectClient(client);
                });
                
                searchResults.appendChild(item);
            });
            
            searchResults.style.display = 'block';
        }

        function selectClient(client) {
            clientId.value = client.id;
            clientSearch.value = client.name;
            selectedClientName.textContent = client.name;
            selectedClientModel.textContent = client.car_model ? ' - ' + client.car_model : '';
            selectedClientBox.classList.add('show');
            searchResults.style.display = 'none';
        }

        clientSearch.addEventListener('focus', function() {
            if (this.value.length >= 2 && clientId.value) {
                searchResults.style.display = 'block';
            }
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                searchResults.style.display = 'none';
            }
        });
    }

    if (addLineBtn) {
        addLineBtn.addEventListener('click', function() {
            const row = document.querySelector('.line-row').cloneNode(true);
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
            const select = row.querySelector('select');
            select.selectedIndex = 0;
            row.querySelector('.line-total').textContent = '0.000';
            linesTable.appendChild(row);
            updateTotals();
        });

        linesTable.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-line')) {
                if (document.querySelectorAll('.line-row').length > 1) {
                    e.target.closest('.line-row').remove();
                    updateTotals();
                }
            }
        });

        linesTable.addEventListener('change', function(e) {
            if (e.target.tagName === 'SELECT') {
                const option = e.target.options[e.target.selectedIndex];
                const priceInput = e.target.closest('tr').querySelector('.price');
                if (option.dataset.price) {
                    priceInput.value = option.dataset.price;
                }
                updateLineTotal(e.target.closest('tr'));
            }
            if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
                updateLineTotal(e.target.closest('tr'));
            }
        });

        calcInputs.forEach(input => {
            input.addEventListener('input', updateTotals);
        });

        function updateLineTotal(row) {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const total = qty * price;
            row.querySelector('.line-total').textContent = total.toFixed(3);
            updateTotals();
        }

        function updateTotals() {
            let linesTotal = 0;
            document.querySelectorAll('.line-row').forEach(row => {
                const total = parseFloat(row.querySelector('.line-total').textContent) || 0;
                linesTotal += total;
            });

            const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;
            const droitTimbre = parseFloat(document.querySelector('input[name="droit_timbre"]').value) || 0;

            const ht = linesTotal;
            const tva = ht * (taxRate / 100);
            const ttc = ht + tva + droitTimbre;

            document.getElementById('total_ht').textContent = ht.toFixed(3);
            document.getElementById('total_tva').textContent = tva.toFixed(3);
            document.getElementById('total_ttc').textContent = ttc.toFixed(3);
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});

function clearClientSelection() {
    document.getElementById('clientId').value = '';
    document.getElementById('clientSearch').value = '';
    document.getElementById('selectedClientBox').classList.remove('show');
    document.getElementById('clientSearch').focus();
}
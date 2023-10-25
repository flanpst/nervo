let shouldUpdateFields = true;

document.addEventListener('DOMContentLoaded', function() {
    const clearFiltersBtn = document.querySelector('#clearFilters');
    clearFiltersBtn.addEventListener('click', function() {
        shouldUpdateFields = false; 
        handleChange('All', 'All', 'All', true);
        shouldUpdateFields = true;
    });

    ['client', 'year', 'type'].forEach(id => {
        document.getElementById(id).addEventListener('change', function() {
            updatePortfolioContent();
        });
});
});

function updateSelectOptions(selectElement, values, defaultValue = 'All') {
    selectElement.innerHTML = '';
    
    const allOption = document.createElement('option');
    allOption.value = 'All';
    allOption.text = 'All';
    selectElement.add(allOption);

    values.forEach(value => {
        const option = document.createElement('option');
        option.value = value;
        option.text = value;
        if (value === defaultValue) {
            option.selected = true;
        }
        selectElement.add(option);
    });
}

function handleChange(clientValue = null, yearValue = null, typeValue = null, reset = false) {
    
    const clientSelect = document.querySelector('#client');
    const yearSelect = document.querySelector('#year');
    const typeSelect = document.querySelector('#type');

    // Se nenhum valor for fornecido, pega o valor atual do campo
    clientValue = clientValue !== null ? clientValue : clientSelect.value;
    yearValue = yearValue !== null ? yearValue : yearSelect.value;
    typeValue = typeValue !== null ? typeValue : typeSelect.value;

    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: {
            'action': 'my_portfolio_query_filter',
            'client': clientValue,
            'year': yearValue,
            'type': typeValue,
        },
        success: function(response) {
            if (response.success && shouldUpdateFields && response.data) {
                if(response.data.clients) {
                    updateSelectOptions(clientSelect, response.data.clients, clientValue);
                }
                if(response.data.years) {
                    updateSelectOptions(yearSelect, response.data.years, yearValue);
                }
                if(response.data.types) {
                    updateSelectOptions(typeSelect, response.data.types, typeValue);
                }
                if (reset) {
                    updatePortfolioContent(clientValue, yearValue, typeValue);
                }
            } else {
                console.error(response.data);
            }
        },
        error: function(error) {
            console.error(error);
        }
    });
}

function updatePortfolioContent(clientValue = null, yearValue = null, typeValue = null) {
    clientValue = clientValue !== null ? clientValue : jQuery('#client').val();
    yearValue = yearValue !== null ? yearValue : jQuery('#year').val();
    typeValue = typeValue !== null ? typeValue : jQuery('#type').val();

    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: {
            action: 'my_portfolio_query_filter',
            client: clientValue,
            year: yearValue,
            type: typeValue,
        },
        success: function(response) {
            if (response && response.success) {
                const portfolioContainer = jQuery('#portfolio-container');
                // Substitui o conteúdo anterior com o novo HTML
                portfolioContainer.html(response.data.html);
                updateSelectOptions(jQuery('#client')[0], response.data.clients, clientValue);
                updateSelectOptions(jQuery('#year')[0], response.data.years, yearValue);
                updateSelectOptions(jQuery('#type')[0], response.data.types, typeValue);
            } else {
                console.error('No data returned or error:', response.data);
            }
        },
        error: function(error) {
            console.error(error);
        }
    });
}

let shouldUpdateFields = true;

document.addEventListener('DOMContentLoaded', function() {
    const clearFiltersBtn = document.querySelector('#clearFilters');
    clearFiltersBtn.addEventListener('click', function() {
        shouldUpdateFields = false; 
        handleChange('All', 'All', 'All');
        shouldUpdateFields = true;
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

function handleChange(clientValue = null, yearValue = null, typeValue = null) {
    
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
            if (response.success && shouldUpdateFields) {
                updateSelectOptions(clientSelect, response.data.clients, clientValue);
                updateSelectOptions(yearSelect, response.data.years, yearValue);
                updateSelectOptions(typeSelect, response.data.types, typeValue);
            } else {
                console.error(response.data);
            }
        },
        error: function(error) {
            console.error(error);
        }
    });
}


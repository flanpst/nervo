// Função que obtém os dados dos filtros
function getFilters() {
    // Obtemos o valor do campo cliente
    const client = document.querySelector('select[id="filters"]').value;

    // Obtemos o valor do campo ano
    const year = document.querySelector('input[name="year"]').value;

    // Obtemos o valor do campo tipo
    const type = document.querySelector('select[id="type"]').value;

    // Criamos o objeto de filtros
    const filters = {
        client,
        year,
        type,
    };

    // Retornamos o objeto de filtros
    return filters;
}

// Função que envia os dados para o widget
function sendFilters() {
    // Obtemos os dados dos filtros
    const filters = getFilters();

    // Enviamos os dados para o widget
    const widget = window.elementor.getWidgetById('portfolio-filter');
    widget.updateSettings(filters);
}

// Atribuimos os eventos aos campos
$(function() {
    // Atribuimos o evento de change ao campo cliente
    $('#filters').on('change', sendFilters);
});
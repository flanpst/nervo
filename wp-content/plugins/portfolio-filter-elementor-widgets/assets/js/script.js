function handleChange() {
    // Obtém os valores dos seletores
    const clientValue = document.querySelector('#client').value;
    const yearValue = document.querySelector('#year').value;
    const typeValue = document.querySelector('#type').value;

    // Faz a requisição AJAX para filtrar os itens do portfólio
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
            // Manipula a resposta da requisição
            console.log(response);

            // Renderiza os itens do portfólio filtrados
            const newQueryArgs = {
                'post__in': response.posts,
            };
        },
        error: function(error) {
            // Trata erros, se houver
            console.error(error);
        }
    });
}


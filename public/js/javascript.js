$(document).ready(function () {
    $('.filterable .btn-filter').click(function () {
        var $panel = $(this).parents('.filterable'),
                $filters = $panel.find('.filters input'),
                $tbody = $panel.find('.table tbody');
        if ($filters.prop('disabled') == true) {
            $filters.prop('disabled', false);
            $filters.first().focus();
        } else {
            $filters.val('').prop('disabled', true);
            $tbody.find('.no-result').remove();
            $tbody.find('tr').show();
        }
    });

    $('.filterable .filters input').keyup(function (e) {
        /* Ignore tab key */
        var code = e.keyCode || e.which;
        if (code == '9')
            return;
        /* Useful DOM data and selectors */
        var $input = $(this),
                inputContent = $input.val().toLowerCase(),
                $panel = $input.parents('.filterable'),
                column = $panel.find('.filters th').index($input.parents('th')),
                $table = $panel.find('.table'),
                $rows = $table.find('tbody tr');
        /* Dirtiest filter function ever ;) */
        var $filteredRows = $rows.filter(function () {
            var value = $(this).find('td').eq(column).text().toLowerCase();
            return value.indexOf(inputContent) === -1;
        });
        /* Clean previous no-result if exist */
        $table.find('tbody .no-result').remove();
        /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
        $rows.show();
        $filteredRows.hide();
        /* Prepend no-result row if all rows are filtered */
        if ($filteredRows.length === $rows.length) {
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="' + $table.find('.filters th').length + '">Nenhum resultado encontrado</td></tr>'));
        }
    });

    /*MODAL*/
    var panels = $('.vote-results');
    var panelsButton = $('.dropdown-results');
    panels.hide();

    //Click dropdown
    panelsButton.click(function () {
        //get data-for attribute
        var dataFor = $(this).attr('data-for');
        var idFor = $(dataFor);

        //current button
        var currentButton = $(this);
        idFor.slideToggle(400, function () {
            //Completed slidetoggle
            if (idFor.is(':visible'))
            {
                currentButton.html('Hide Results');
            }
            else
            {
                currentButton.html('View Results');
            }
        })
    });

    /*Carrinho de solicitações*/
    $('.linhaCarrinhoSolicitacao').each(function () {
                        
        var disponiveis = $(this).find('.disponiveis').text();
            
        $(this).children().find('.quantidadeSolicitada').attr('max', disponiveis);

    });


    /*Carrinho de compras*/
    $(".valorUnitario").maskMoney({showSymbol: true, symbol: "R$", decimal: ",", thousands: "."});


    $('.valorUnitario, .quantidadeItensCompra').blur(function () {
        var total = 0;
        $('.linhaCarrinho').each(function () {

            var valorUnitario = $(this).find('.valorUnitario').val();
            var quantidadeItensCompra = $(this).find('.quantidadeItensCompra').val();
            var valorUnitarioComCifrao = valorUnitario.replace(",", ".");

            var multiplica = (parseFloat(quantidadeItensCompra) * parseFloat(valorUnitarioComCifrao));
            total += multiplica;
            

        })

        

        total = parseFloat(total).toFixed(2);
        
        total = total.replace(".", ",");

        $('#total').text("Valor total: R$ " + total);

    });

    $('.imprimir').click(function () {

        $('.no-print').css("display", "none");
        $('.print').css("display", "block");
        window.print();
        window.close();
        
    });

    $('input[type=tel]').mask("(00) 0000-00009");
    $("#cnpj").mask("99.999.999/9999-99");


    $('button').click(function () {

        var inputElements = document.querySelectorAll('input, select, textarea');

        for (var i = 0, l = inputElements.length; i < l; i++) {
            if (inputElements[i].tagName == 'SELECT') {
                inputElements[i].options[inputElements[i].selectedIndex].value = inputElements[i].options[inputElements[i].selectedIndex].value.toUpperCase();
            } else {
                inputElements[i].value = inputElements[i].value.toUpperCase();
            }
        }

    });

});



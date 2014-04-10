$(window).ready(function(){
    $('.campo-busca').click(function(){
        $(this).val('');
        $(this).attr('title', '');
        $('#'+$(this).attr('destino')).val('');
        campoBusca($(this));
    }).keyup(function(){
        campoBusca($(this));
    });
    $('.campo-busca').parent().children('.dropdown-menu').on('click', '.item', function() {
        cp = $(this).parent().parent().parent().children('.campo-busca');
        $(cp).val($(this).text());
        $(cp).attr('title', $(this).attr('title'));
        $('#'+$(cp).attr('destino')).val($(this).attr('id'));
    });
});

var statusCampoBusca = 0;
function campoBusca(cp){
    if(statusCampoBusca == 0 && $(cp).attr('fonte')){
        $.ajax({
            data: {'palavra-chave': $(cp).val()},
            dataType: 'html',
            type: 'GET',
            url: $(cp).attr('fonte') + ((jQPaginaData['tipo']>0)?('?tipo=' + jQPaginaData['tipo']):('')),
            beforeSend: function(){
                statusCampoBusca = 1;
                $(cp).parent().children('.dropdown-menu').html('<li><div class="LOADING-ESPECIFICO"></div></li>');
            },
            success: function(data){
                $(cp).parent().children('.dropdown-menu').html(data.toString());
            },
            eror: function(){
                $(cp).parent().children('.dropdown-menu').html('<li><a>Não foi possível fazer a consulta.</a></li>');
            },
            complete: function(){
                statusCampoBusca = 0;
                $(cp).parent().find('li div.LOADING-ESPECIFICO').remove();
            }
        });
    }
}
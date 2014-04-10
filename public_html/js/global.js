function irTopo(){
    $('html, body').animate({scrollTop: 0}, 500);
}

function setDialogo(tipo, mensagem){
    $.get(
        '/index/ajax-dialogo', {tipo: tipo, mensagem: mensagem},
        function(data){
            $('#DIALOGO').html(data.toString());
            irTopo();
        },
        'html'
    );
}

function setDialogoCadastrados(){
    setDialogo('Alerta', '<b>Funcionalidade exclusiva</b> para usuários cadastrados!');
}

$(document).ready(function(){
    // RELOADING //
    $('html, body').animate({scrollTop: 0}, 0);
    
    // BOTAO TOPO //
    $('#TOPO').click(irTopo);
    $(window).bind("scroll", function(){
        if($(window).scrollTop() > 90){
            $('#TOPO').css('opacity', '1');
            $('#TOPO').css('visibility', 'visible');
        }else{
            $('#TOPO').css('opacity', '0');
            $('#TOPO').css('visibility', 'hidden');
        }
    });
});

function vaiPara($link){
    document.location.href=$link;
}
function ligaDialogo(){
    document.getElementById('DIALOGO').style.display = 'block';
}
function desligaDialogo(){
    document.getElementById('DIALOGO').style.display = 'none';
}

function soCadastrados(){
    carregarAjaxOculto('/index/ajax-mensagem/tipo/Alerta/mensagem/Funcionalidade exclusiva para usuários cadastrados!', 'MENSAGENS');
    $('html, body').animate({
        scrollTop: 0
    }, 500);
}
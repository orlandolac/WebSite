function ajaxNoticiasImportancia(noticia_id, user_importancia_id, a){
    soCadastrados();
}

function ajaxNoticiasPartidoPositivar(noticia_id, partido_id){
    soCadastrados();
}
function ajaxNoticiasPartidoNegativar(noticia_id, partido_id){
    soCadastrados();
}

function ajaxNoticiasPessoaPositivar(noticia_id, pessoa_id){
    soCadastrados();
}
function ajaxNoticiasPessoaNegativar(noticia_id, pessoa_id){
    soCadastrados();
}

function ajaxNoticiasLigar(noticia_id){
    carregarAjaxOculto('/noticias/ajax-noticias-ligar/id/' + noticia_id, 'NADA');
}
function ajaxNoticiasPartidoLigar(noticia_id, partido_id){
    carregarAjaxOculto('/noticias/ajax-noticias-partido-ligar/id/' + noticia_id + '/partido_id/' + partido_id, 'NADA');
}
function ajaxNoticiasPessoaLigar(noticia_id, pessoa_id){
    carregarAjaxOculto('/noticias/ajax-noticias-pessoa-ligar/id/' + noticia_id + '/pessoa_id/' + pessoa_id, 'NADA');
}
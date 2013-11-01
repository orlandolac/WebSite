function ajaxNoticiasImportancia(noticia_id, user_importancia_id, flag){
    destino = 'NADA';
    if(flag){
        flag.style.backgroundImage = 'url("/temas/default/default/icones/star_blue.png")';
        $('#miniaturaNoticia'+noticia_id).animate({
            opacity: 0
        }, 1000, function(){
            document.getElementById('miniaturaNoticia'+noticia_id).style.display = 'none';
        });
    }else{
        destino = 'indiceNoticia' + noticia_id;
    }
    carregarAjaxOculto('/noticias/ajax-noticias-importancia/id/' + noticia_id + '/user_importancia_id/' + user_importancia_id, destino);
}

function ajaxNoticiasPartidoPositivar(noticia_id, partido_id){
    carregarAjaxOculto('/noticias/ajax-noticias-partido-positivar/id/' + noticia_id + '/partido_id/' + partido_id, 'indicesNoticia' + noticia_id);
}
function ajaxNoticiasPartidoNegativar(noticia_id, partido_id){
    carregarAjaxOculto('/noticias/ajax-noticias-partido-negativar/id/' + noticia_id + '/partido_id/' + partido_id, 'indicesNoticia' + noticia_id);
}

function ajaxNoticiasPessoaPositivar(noticia_id, pessoa_id){
    carregarAjaxOculto('/noticias/ajax-noticias-pessoa-positivar/id/' + noticia_id + '/pessoa_id/' + pessoa_id, 'indicesNoticia' + noticia_id);
}
function ajaxNoticiasPessoaNegativar(noticia_id, pessoa_id){
    carregarAjaxOculto('/noticias/ajax-noticias-pessoa-negativar/id/' + noticia_id + '/pessoa_id/' + pessoa_id, 'indicesNoticia' + noticia_id);
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
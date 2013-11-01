function ajaxProjetosVotarSim(projeto_id){
    classe = $('#urnaProjeto'+projeto_id).attr('class');
    if(classe == 'botoes' || classe == 'botoes votouNao'){
        $('#urnaProjeto'+projeto_id).attr('class', 'botoes votouSim');
        carregarAjaxOculto('/projetos/ajax-projetos-votar-sim/id/' + projeto_id, 'estatisticaProjeto' + projeto_id);
    }
}
function ajaxProjetosVotarNao(projeto_id){
    classe = $('#urnaProjeto'+projeto_id).attr('class');
    if(classe == 'botoes' || classe == 'botoes votouSim'){
        $('#urnaProjeto'+projeto_id).attr('class', 'botoes votouNao');
        carregarAjaxOculto('/projetos/ajax-projetos-votar-nao/id/' + projeto_id, 'estatisticaProjeto' + projeto_id);
    }
}

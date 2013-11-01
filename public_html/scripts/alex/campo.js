function entrou(cp){
    if(cp.value == cp.lang){
        cp.value = '';
    }
}
function saiu(cp){
    if(cp.value == ''){
        cp.value = cp.lang;
    }
}
function marcaCampoErro(cp){
    cp.style.background = '#ffefef';
}
function marcaCampoOk(cp){
    cp.style.background = '#dfd';
}
function desmarcaCampo(cp){
    cp.style.background = '#fff';
}
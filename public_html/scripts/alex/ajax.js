var reqAjax;
var idElemento;
var idOculto = 0;

function carregarAjax(arquivo, elemento){
    ligaDialogo();
    document.getElementById('DIALOGO').innerHTML='<div class="LOADING"></div>';
    idElemento = elemento;
    loadXMLDocAjax(arquivo);
}

function carregarAjaxEspecifico(arquivo, elemento){
    document.getElementById(''+elemento).innerHTML='<div class="LOADING-ESPECIFICO"></div>';
    idElemento = elemento;
    loadXMLDocAjax(arquivo);
}

function carregarAjaxOculto(arquivo, elemento){
    idOculto = 1;
    idElemento = elemento;
    loadXMLDocAjax(arquivo);
}

function loadXMLDocAjax(arquivo){
    reqAjax = null;
    if(window.XMLHttpRequest){
        reqAjax = new XMLHttpRequest();
        reqAjax.onreadystatechange = processReqChangeAjax;
        reqAjax.open("POST", arquivo, true);
        reqAjax.send(null);
    }else if(window.ActiveXObject){
        reqAjax = new ActiveXObject("Microsoft.XMLHTTP");
        if(reqAjax){
            reqAjax.onreadystatechange = processReqChangeAjax;
            reqAjax.open("POST", arquivo, true);
            reqAjax.send();
        }
    }
}

function processReqChangeAjax(){
    if(reqAjax.readyState == 4){
        if(reqAjax.status == 200){
            document.getElementById('' + idElemento).innerHTML = reqAjax.responseText;
            transferirScripts();
            if(idOculto == 0 && idElemento != 'DIALOGO'){
                desligaDialogo();
            }
        }else{
            alert("Houve um problema ao obter os dados:\n" + reqAjax.statusText);
        }
    }
}

function transferirScripts(){
    divScriptsAjax = document.getElementById('scriptsAjax');
    if(divScriptsAjax){
        novoScript = document.createElement('script');
        scriptsAjax = document.getElementById('' + idElemento).getElementsByTagName('script');
        for(i = 0; i < scriptsAjax.length; i++){
            if(scriptsAjax[i].innerHTML == ''){
                novoScriptExt = document.createElement('script');
                src = document.createAttribute('src');
                src.value = scriptsAjax[i].src;
                novoScriptExt.setAttributeNode(src);
                type = document.createAttribute('type');
                type.value = scriptsAjax[i].type;
                novoScriptExt.setAttributeNode(type);
                divScriptsAjax.appendChild(novoScriptExt);
            }else{
                novoScript.text += scriptsAjax[i].innerHTML;
            }
        }
        divScriptsAjax.appendChild(novoScript);
    }else{
        alert('A camada que receberá os scripts não foi encontrada.');
    }
}
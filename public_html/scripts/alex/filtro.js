
listaNumero = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];	

listaAlfabeto = [
    'A', 'O', 'E', 'I', 'U',
    'a', 'o', 'e', 'i', 'u',
    'Q', 'W', 'R', 'T', 'Y', 'P', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M',
    'q', 'w', 'r', 't', 'y', 'p', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm'
    ];

listaAcento = [
    'Á', 'À', 'Â', 'Ä', 'Ã',
    'á', 'à', 'â', 'ä', 'ã',
    'É', 'È', 'Ê', 'Ë',
    'é', 'è', 'ê', 'ë',
    'Í', 'Ì', 'Î', 'Ï',
    'í', 'ì', 'î', 'ï',
    'Ó', 'Ò', 'Ô', 'Ö', 'Õ',
    'ó', 'ò', 'ô', 'ö', 'õ',
    'Ú', 'Ù', 'Û', 'Ü',
    'ú', 'ù', 'û', 'ü'
    ];	

listaAspa = ["'", '"', '`', '´'];

listaSimbolo = [
    '+', '-', '*', '/', '=', '%', '(', ')', '[', ']', '{', '}', '<', '>',
    '³', '²', '¹', '¬', '¢', '£',
    '!', '?', ':', ';', '.', ',', '_',
    "'", '"', '`', '´', '^', '~', '¨',
    '@', '#', '$', '§', '&', '|', '\\',
    ];

xObjeto = null;
xFuncao = null;
xCursor = 0;
xMenos = 0;

function mascara(objeto,funcao){
    xObjeto=objeto;
    getPosCursor();
    xFuncao=funcao;
    setTimeout("execMascara()",1);
}
function execMascara(){
    xObjeto.value=xFuncao(xObjeto.value);
}
function getPosCursor() {
    xObjeto = xObjeto;
    xMenos  = 0;
    if (typeof(xObjeto.selectionStart) != "undefined") {
        xCursor = xObjeto.selectionStart;
    }else if (document.selection) {
        var range = document.selection.createRange();
        var storedRange = range.duplicate();
        storedRange.moveToElementText(xObjeto);
        storedRange.setEndPoint("EndToEnd", range);
        xCursor = storedRange.text.length - range.text.length;
    }
}
function setPosCursor(){
    if(xMenos > 0){
        xObjeto.selectionEnd = xCursor;
    }
}

//return novo.replace(/\s+|\s+/g,' ');
//
//Um caractere de espaço
//Um caractere de tabulação
//Um personagem retorno de carro
//Um caractere de nova linha
//Um caractere de tabulação vertical
//Um caractere de alimentação de forma

function so(valor, lista){
    novo = '';
    for(i=0; i<valor.length; i++){
        if(lista.indexOf(valor.charAt(i)) > -1){
            novo +=  valor.charAt(i);
        }else{
            xMenos++;
        }
    }
    return novo.replace(/\s+|\s+/g,' ');
}
function soNumero(valor){
    return so(valor, listaNumero);
}
function soNumeroEspaco(valor){
    return so(valor, listaNumero.concat(new Array(' ')));
}
function soAlfabeto(valor){
    return so(valor, listaAlfabeto);
}
function soAlfabetoEspaco(valor){
    return so(valor, listaAlfabeto.concat(new Array(' ')));
}
function soAlfabetoNumero(valor){
    return so(valor, listaAlfabeto.concat(listaNumero));
}
function soAlfabetoNumeroEspaco(valor){
    return so(valor, listaAlfabeto.concat(listaNumero, new Array(' ')));
}
function soAlfabetoAcento(valor){
    return so(valor, listaAlfabeto.concat(listaAcento));
}
function soAlfabetoAcentoEspaco(valor){
    return so(valor, listaAlfabeto.concat(listaAcento, new Array(' ')));
}
function soAlfabetoAcentoNumero(valor){
    return so(valor, listaAlfabeto.concat(listaAcento, listaNumero));
}
function soAlfabetoAcentoNumeroEspaco(valor){
    return so(valor, listaAlfabeto.concat(listaAcento, listaNumero, new Array(' ')));
}
function soAll(valor){
    return so(valor, listaAlfabeto.concat(listaAcento, listaNumero, listaSimbolo, new Array(' ')));
}
function soSimbolo(valor){
    valor = tiraNumero(valor);
    valor = tiraAlfabeto(obj);
    valor = tiraAcento(obj);
    return valor;
}
function soTextoSimples(valor){
    lista = [];
    for(i=0; i<listaAlfabeto.length; i++){
        lista.push(listaAlfabeto[i]);
    }
    for(i=0; i<listaAcento.length; i++){
        lista.push(listaAcento[i]);
    }
    for(i=0; i<listaNumero.length; i++){
        lista.push(listaNumero[i]);
    }
    lista.push(' ');
    return so(valor, lista);
}
function soTextoComposto(valor){
    lista = [];
    for(i=0; i<listaAlfabeto.length; i++){
        lista.push(listaAlfabeto[i]);
    }
    for(i=0; i<listaAcento.length; i++){
        lista.push(listaAcento[i]);
    }
    for(i=0; i<listaNumero.length; i++){
        lista.push(listaNumero[i]);
    }
    for(i=0; i<listaSimboloTexto.length; i++){
        lista.push(listaSimboloTexto[i]);
    }
    lista.push(' ');
    return so(valor, lista);
}

function soNomeUrl(valor){
    return so(valor, listaAlfabeto.concat(listaNumero, new Array('-')));
}

function tira(valor, lista){
    novo = '';
    for(i=0; i<valor.length; i++){
        if(lista.indexOf(valor.charAt(i)) > -1){
            xMenos++;
        }else{
            novo +=  valor.charAt(i);
        }
    }
    return novo;
}
function tiraAspas(valor){
    return tira(valor, listaAspa);
}
function tiraNumero(valor){
    return tira(valor, listaNumero);
}
function tiraAlfabeto(valor){
    return tira(valor, listaAlfabeto);
}
function tiraAcento(valor){
    return tira(valor, listaAcento);
}
function tiraSimbolo(valor){
    return soAlfabetoAcentoNumero(valor);
}

function soEmail(valor){
    return so(valor, listaAlfabeto.concat(listaNumero, new Array('_', '@', '.')));
}
function soData(v){
    v=v.replace(/\D/g, '');
    v=v.replace(/^(\d{2})(\d{2})(\d)/,"$1-$2-$3");
    return v;
}
function soCep(v){
    v=v.replace(/\D/g, '');
    v=v.replace(/^(\d{2})(\d{3})(\d)/,"$1-$2-$3");
    return v;
    return so(valor, listaNumero.concat(new Array('-', '.')));
}
//function soData(valor){
//    return so(valor, listaNumero.concat(new Array('-')));
//}
//function soCep(valor){
//    return so(valor, listaNumero.concat(new Array('-', '.')));
//}
function soCarteira(valor){
    return so(valor, listaNumero.concat(listaAlfabeto, new Array('-')));
}
function limpaEndereco(valor){
    return so(valor, listaNumero.concat(listaAlfabeto, listaAcento, new Array('-', ',', '.', ' ', '/', 'ª', 'º')));
}

function eDataValida(data) {
    bissexto = 0;
    tam = data.length;
    if(tam == 10){
        dia = data.substr(0,2);
        mes = data.substr(3,2);
        ano = data.substr(6,4);
        if((ano > 1900)||(ano < 2100)){
            switch (mes) {
                case '01':
                case '03':
                case '05':
                case '07':
                case '08':
                case '10':
                case '12': {
                    if(dia <= 31){
                        return true;
                    }
                    break;
                }
                case '04':
                case '06':
                case '09':
                case '11':
                    if(dia <= 30){
                        return true;
                    }
                    break;
                case '02':
                    if((ano % 4 == 0) || (ano % 100 == 0) || (ano % 400 == 0)){
                        bissexto = 1; 
                    } 
                    if((bissexto == 1) && (dia <= 29)){
                        return true;
                    }
                    if((bissexto != 1) && (dia <= 28)){
                        return true;
                    }
                    break;
            }
        }
    }
    return false;
}
function eDataNascimentoValida(data) {
    if(eDataValida(data)){
        hoje = new Date()
        anoHoje = hoje.getFullYear()
        if(anoHoje > data.substr(6,4)){
            return true;
        }
    }
    return false;
}
function eEmailValido(email){
    var exclude=/[^@\-\.\w]|^[_@\.\-]|[\._\-]{2}|[@\.]{2}|(@)[^@]*\1/;
    var check=/@[\w\-]+\./;
    var checkend=/\.[a-zA-Z]{2,3}$/;
    if(((email.search(exclude) != -1)||(email.search(check)) == -1)||(email.search(checkend) == -1)){
        return false;
    }else{
        return true;
    }
}
function soSite(v){
    //Esse sem comentarios para que voc� entenda sozinho ;-)
    v=v.replace(/^http:\/\/?/,"");
    dominio=v;
    caminho="";
    if(v.indexOf("/")>-1);
    dominio=v.split("/")[0];
    caminho=v.replace(/[^\/]*/,"");
    dominio=dominio.replace(/[^\w\.\+-:@]/g,"");
    caminho=caminho.replace(/[^\w\d\+-@:\?&=%\(\)\.]/g,"");
    caminho=caminho.replace(/([\?&])=/,"$1");
    if(caminho!="")dominio=dominio.replace(/\.+$/,"");
    v="http://"+dominio+caminho;
    return v;
}
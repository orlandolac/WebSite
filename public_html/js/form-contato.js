function salvar(){
    erro = 0;
    f = document.formulario;

    if(f.contato_nome.value.length < 2){
        campoMarcaErro(f.contato_nome); erro++;
    }

    f.contato_email.value = f.contato_email.value.toLowerCase();
    if(f.contato_email.value.length < 5 || !eEmailValido(f.contato_email.value)){
        campoMarcaErro(f.contato_email); erro++;
    }

    if(f.contato_tipo.value < 1){
        campoMarcaErro(f.contato_tipo); erro++;
    }

    f.contato_assunto.value = f.contato_assunto.value.toLowerCase();
    if(f.contato_assunto.value.length < 2){
        campoMarcaErro(f.contato_assunto); erro++;
    }

    f.contato_mensagem.value = f.contato_mensagem.value.toLowerCase();
    if(f.contato_mensagem.value.length < 2){
        campoMarcaErro(f.contato_mensagem); erro++;
    }

    if(erro == 0){
        f.submit();
    }

}
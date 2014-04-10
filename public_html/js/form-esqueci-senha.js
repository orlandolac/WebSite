function solicitar(){
    f = document.formulario;
    
    f.user_email.value = f.user_email.value.toLowerCase();
    if(!eEmailValido(f.user_email.value)){
        campoMarcaErro(f.user_email);
    }else{
        f.submit();
    }
}
function entrar(){
    erro = 0;
    f = document.formulario;
    
    if(f.user_senha.value.length < 6){
        campoMarcaErro(f.user_senha); erro++;
    }
    
    if(!eEmailValido(f.user_email.value)){
        campoMarcaErro(f.user_email); erro++;
    }
    
    if(erro == 0){
        f.submit();
    }
}

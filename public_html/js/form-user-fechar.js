function fechar(){
    f = document.formulario;
    qtdNum = f.user_senha.value.replace(/\D/g, '').length;
    if(f.user_senha.value.length < 6 || qtdNum < 1 || (f.user_senha.value.length - qtdNum) < 1){
        campoMarcaErro(f.user_senha);
    }else{
        if(confirm('Tem certeza que deseja fechar sua conta?')){
            f.submit();
        }
    }
    return 0;
}
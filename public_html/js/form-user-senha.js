function alterar(){
    erro = 0;
    f = document.formulario;
    
    qtdNum = f.user_senha_atual.value.replace(/\D/g, '').length;
    if(f.user_senha_atual.value.length < 6 || qtdNum < 1 || (f.user_senha_atual.value.length - qtdNum) < 1){
        campoMarcaErro(f.user_senha_atual); erro++;
    }
    
    qtdNum = f.user_senha.value.replace(/\D/g, '').length;
    if(f.user_senha.value.length < 6 || qtdNum < 1 || (f.user_senha.value.length - qtdNum) < 1){
        campoMarcaErro(f.user_senha); erro++;
    }
    
    if(f.user_senha_repitida.value < 6 || f.user_senha_repitida.value != f.user_senha.value){
        campoMarcaErro(f.user_senha_repitida); erro++;
    }
    
    if(erro == 0){
        f.submit();
    }
}
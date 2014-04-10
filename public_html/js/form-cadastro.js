function cadastrar(){
    erro=0;
    f = document.formulario;
    
    if(f.user_nome.value.length < 2){
        campoMarcaErro(f.user_nome); erro++;
    }
    
    status = 0;
    if(f.user_data_nascimento.value.length == 10){
        d = f.user_data_nascimento.value.substr(0, 2);
        m = f.user_data_nascimento.value.substr(3, 2);
        a = f.user_data_nascimento.value.substr(6);
        if(d > 0 && d < 31){
            if(m > 0 && m < 13){
                if(a > 1900 && a < 2014){
                    status = 1;
                    f.user_data_nascimento.value = d+'-'+m+'-'+a;
                }
            }
        }
    }
    if(status == 0){
        campoMarcaErro(f.user_data_nascimento); erro++;
    }
    
    if(!(f.user_sexo.value > 0)){
        campoMarcaErro(f.user_sexo); erro++;
    }
    
    if(!(f.user_escolaridade.value > 0)){
        campoMarcaErro(f.user_escolaridade); erro++;
    }
    
    f.user_email.value = f.user_email.value.toLowerCase();
    if(f.user_email.value.length < 5 || !eEmailValido(f.user_email.value)){
        campoMarcaErro(f.user_email); erro++;
    }
    
    qtdNum = f.user_senha.value.replace(/\D/g, '').length;
    if(f.user_senha.value.length < 6 || qtdNum < 1 || (f.user_senha.value.length - qtdNum) < 1){
        campoMarcaErro(f.user_senha); erro++;
    }
    
    if(f.user_senha_repitida.value < 6 || f.user_senha_repitida.value != f.user_senha.value){
        campoMarcaErro(f.user_senha_repitida); erro++;
    }
    
    if(erro == 0){
        if(f.termos.checked){
            f.submit();
        }else{
            alert('Para cadastrar-se você precisa ler e aceitar os Termos & Políticas do QueroPraticar.com.');
        }
    }
}
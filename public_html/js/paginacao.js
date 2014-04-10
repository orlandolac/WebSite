var jQPaginaStatus = 0;
$(window).ready(function(){
    $(window).bind("scroll", function(){
        if(($(window).scrollTop() + $(window).height() + 550) > $(document).height()){
            if(jQPaginaStatus == 0){
                if(jQPaginaAtual < jQPaginaFinal){
                    $.ajax({
                        data: jQPaginaData,
                        dataType: 'html',
                        type: 'GET',
                        url: jQPaginaCaminho + '/pagina/' + (++jQPaginaAtual),
                        beforeSend: function(){
                            jQPaginaStatus = 1;
                            $('#LOADING').html('<div class="LOADING"></div>');
                        },
                        success: function(data){
                            $('#RESULTADO').append(data); // data.toString()
                        },
                        eror: function(){
                            $('#RESULTADO').append('');
                        },
                        complete: function(){
                            jQPaginaStatus = 0;
                            $('#LOADING').html('');
                        }
                    });
                }else{
                    jQPaginaStatus = 1;
                }
            }
        }
    });
});
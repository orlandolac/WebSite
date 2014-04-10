function getLinha(id, nome, vertical, horizontal, dados) {
    new google.visualization.LineChart(document.getElementById('' + id)).
        draw(google.visualization.arrayToDataTable(dados),{
            title: nome,
            vAxis: {title: vertical},
            hAxis: {title: horizontal},
            colors: ['#090','#cc0','#d00','#ccc'],
            fontSize: 12
        }
    );
}

function getPizza(id, nome, acertou, pulou, errou, branco) {
    if((pulou + acertou + errou + branco) > 0){
        new google.visualization.PieChart(document.getElementById('' + id)).
            draw(google.visualization.arrayToDataTable([
                ['Resultado', 'qtd'],
                ['ACERTOS', acertou],
                ['PULOS', pulou],
                ['ERROS', errou],
                ['EM BRANCO', branco]
            ]), {title: nome, colors: ['#090','#cc0','#d00','#ccc'], fontSize: 12
        });
    }
}

google.load('visualization', '1', {packages: ['corechart']});
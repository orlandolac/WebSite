# Descrições dos Arquivos


01 - Modelagem.xml
====
Este arquivo possui o Modelo Lógico do banco de dados do sistema. Ele será muito
útil para que você entenda como os dados estão ordanizadas e relacionados. Para
abrir este arquivo você precisará utilizar o "DB Designer 4"


02 - Tabelas.sql
====
Este arquivo possui o Modelo Físico do banco de dados do sistema. Você utilizará
este arquivo para criar o banco de dados local que servirá como base para que
você desenvolva novos recursos. Este será o 1º arquivo a ser executado pelo seu
SGBD, ele criará o banco de dados e as tabelas


03 - Massa de Dados.sql
====
Este arquivo possui a Massa de Dados do bando de dados do sistema isso é, todos
os dados do banco que você poderá utilizar para desenvolver os novos recursos.
Este deve ser o 2º arquivo a ser executado pelo seu SGBD. A carga completa do
banco poder demorar bastante dependento do seu computador.


04 - Índices.sql
====
Este arquivo possui os Índices do banco de dados do sistema. Os Índices são
utilizados pelo banco de dados para agilizar o processo de encontrar registros
nas tabelas. Este arquivo deve ser executado pelo seu SGBD após a carga do banco
do aplicação.


05 - Gatilhos.sql
====
Este arquivo possui os gatilhos do banco de dados do sistema. Os gatilhos são
uma espécie de funções que são exetadas sempre que uma determinada operação é
realizada em uma tabela. Este será o último arquivo a ser executado pelo seu
SGDB. Apóes a execução deste arquivo você deverá ter o banco de dados pronto
para desenvolver.
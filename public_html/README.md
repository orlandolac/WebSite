# OPovoUnido / Public_HTML


Neste diretório ficarão os arquivos que poderão ser acessados diretamente pelos
visitantes. É PROIBIDO ecrever arquivos que são executados no loado do servidor,
como PHP, neste diretório ou em seus subdiretórios, assim como também é PROIBIDO
escrever código de utilizam "login" e/ou "senha" ou qualquer outro dado secreto.


bootstrap
====
Nesta pasta ficarão todos os arquivos do Bootstrap que vamos utilizar na parte
visual do site. Para quem não, o "Bootstrap" é um fremework de html+css+js que
gratúito e que nos ajudará muito com o designer do site.


css
====
Nesta pasta incluiremos os arquivos de CSS feitos por nós mesmos para o site. Se
você criar "regras de estilo" que afetam diversas áreas do site adicione-as ao
arquivo "style.css". Se sua regra for para resolver algo muito específico crie
um novo arquivos CSS com um nome bem intuitivo.


img
====
Nesta pasta colocaremos TODAS as imagens utilizadas pelo designer do sistema,
coisas como, imagens-padrão, icones, logos e fundos. Imagens e arquivos
relacionados ao conteúdo do site deverão ser colocadas em um subdiretório de
"uploads".


plugins
====
Está pasta é destinada apeas á armazenar os plugins utilizados pelo site. Para
cada plugins haverá um subdiretório com um nome intuitivo dentro do diretório
"plugins"


uploads
====
Nasta pasta ficarão todos os arquivos relacionados ao conteúdo do site, coisas
como fotos dos candidatos, bandeiras dos partidos, documentos e etc.




.htaccess
====
Este arquivo realiza configurações no ambiente apache do sistema. São estas
configurações que fazer com que todas as requisições sejam enviadas para o
controlador frontal (index.php), por exemplo.


favicon.ico
====
Este arquivo será o ícone do site no navegador do usuário.


LICENSE.txt
====
Este arquivo representa a licença GPL utilizada pelo OPovoUnido.com.


index.php
====
Este arquivo é o controlador frontal do sistema. Todas as requisições serão
direcionadas para ele, que decidirá que ação tomar.
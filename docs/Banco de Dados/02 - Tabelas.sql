CREATE TABLE dfl_contato (
  contato_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  contato_user_id INT(10) UNSIGNED NOT NULL,
  contato_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  contato_nome CHAR(100) NOT NULL,
  contato_email CHAR(100) NOT NULL,
  contato_assunto CHAR(100) NOT NULL,
  contato_mensagem VARCHAR(2000) NOT NULL,
  contato_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  contato_data_visto INT(10) UNSIGNED NULL,
  PRIMARY KEY(contato_id)
);

CREATE TABLE dfl_funcionalidade (
  func_id SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  func_tipo_id TINYINT(1) UNSIGNED NOT NULL,
  func_ordem SMALLINT(4) UNSIGNED NOT NULL,
  func_nome CHAR(100) NOT NULL,
  func_descricao VARCHAR(1000) NULL,
  PRIMARY KEY(func_id)
);

CREATE TABLE dfl_user (
  user_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  user_tipo_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  user_municipio_id SMALLINT(5) UNSIGNED NOT NULL,
  user_email CHAR(100) NOT NULL,
  user_senha CHAR(50) NOT NULL,
  user_apelido CHAR(50) NOT NULL,
  user_nome CHAR(200) NOT NULL,
  user_icone CHAR(100) NULL,
  user_data_nascimento DATE NULL,
  user_sexo_id CHAR(1) NOT NULL DEFAULT 0,
  user_data_login INT(10) UNSIGNED NOT NULL,
  user_data_ini INT(10) UNSIGNED NOT NULL,
  user_data_alt INT(10) UNSIGNED NOT NULL,
  user_id_facebook BIGINT(15) UNSIGNED NOT NULL DEFAULT 0,
  user_id_google CHAR(20) NOT NULL DEFAULT 0,
  user_id_twitter BIGINT(15) UNSIGNED NOT NULL DEFAULT 0,
  user_id_linkedin BIGINT(15) NOT NULL DEFAULT 0,
  PRIMARY KEY(user_id)
);

CREATE TABLE dfl_user_bloqueios (
  user_id INT(10) UNSIGNED NOT NULL,
  func_id SMALLINT(4) UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, func_id)
);

CREATE TABLE dfl_user_permissoes (
  user_id INT(10) UNSIGNED NOT NULL,
  func_id SMALLINT(4) UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, func_id)
);

CREATE TABLE dfl_usuario_x_apreciacoes (
  user_id INT(10) UNSIGNED NOT NULL,
  apreciacao_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, apreciacao_id)
);

CREATE TABLE dfl_usuario_x_regimes (
  user_id INT(10) UNSIGNED NOT NULL,
  regime_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, regime_id)
);

CREATE TABLE dfl_usuario_x_situacoes (
  user_id INT(10) UNSIGNED NOT NULL,
  situacao_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, situacao_id)
);

CREATE TABLE dfl_usuario_x_temas (
  user_id INT(10) UNSIGNED NOT NULL,
  tema_id TINYINT(3) UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, tema_id)
);

CREATE TABLE dfl_usuario_x_tipo_lei (
  tplei_id INT(10) NOT NULL AUTO_INCREMENT,
  user_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY(tplei_id, user_id)
);

CREATE TABLE dfl_usuario_x_veiculos (
  user_id INT(10) UNSIGNED NOT NULL,
  veiculo_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, veiculo_id)
);

CREATE TABLE his_dfl_contato (
  his_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  his_acao TINYINT(3) UNSIGNED NOT NULL,
  his_data INT(10) UNSIGNED NOT NULL,
  his_user_id INT(10) UNSIGNED NOT NULL,
  his_user_ip CHAR(20) NULL,
  his_detalhe TEXT NULL,
  PRIMARY KEY(his_id)
);

CREATE TABLE his_dfl_user (
  his_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  his_acao TINYINT(3) UNSIGNED NOT NULL,
  his_data INT(10) UNSIGNED NOT NULL,
  his_user_id INT(10) UNSIGNED NOT NULL,
  his_user_ip CHAR(20) NULL,
  his_detalhe TEXT NULL,
  PRIMARY KEY(his_id)
);

CREATE TABLE his_login (
  his_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  his_acao TINYINT(3) UNSIGNED NOT NULL,
  his_data INT(10) UNSIGNED NOT NULL,
  his_user_id INT(10) UNSIGNED NOT NULL,
  his_user_ip CHAR(20) NULL,
  his_detalhe TEXT NULL,
  PRIMARY KEY(his_id)
);

CREATE TABLE not_comentario (
  noticia_id INT(10) UNSIGNED NOT NULL,
  coment_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  coment_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  coment_pai_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  coment_qtd_filhos INT(10) UNSIGNED NOT NULL DEFAULT 0,
  coment_texto CHAR(255) NULL,
  coment_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  coment_user_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(noticia_id, coment_id)
);

CREATE TABLE not_noticia (
  noticia_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  noticia_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  noticia_grupo_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  noticia_qtd_agrupadas INT(10) UNSIGNED NOT NULL DEFAULT 0,
  noticia_user_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  noticia_veiculo_id INT(7) UNSIGNED NOT NULL,
  noticia_data INT(10) UNSIGNED NOT NULL DEFAULT 0,
  noticia_titulo CHAR(100) NOT NULL,
  noticia_resumo CHAR(255) NULL,
  noticia_imagem CHAR(255) NULL,
  noticia_link CHAR(255) NOT NULL,
  noticia_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  noticia_data_alt INT(10) UNSIGNED NOT NULL DEFAULT 0,
  noticia_qtd_ligacao INT(10) UNSIGNED NOT NULL DEFAULT 0,
  noticia_qtd_comentario INT(10) UNSIGNED NOT NULL DEFAULT 0,
  noticia_qtd_importancias INT(10) UNSIGNED NOT NULL DEFAULT 0,
  noticia_importancia_media TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(noticia_id),
  INDEX idx_data(noticia_data),
  INDEX idx_importancia_media(noticia_importancia_media, noticia_data)
);

CREATE TABLE not_noticia_backup (
  noticia_id INT(10) UNSIGNED NOT NULL,
  noticia_user_id INT(10) UNSIGNED NOT NULL,
  noticia_data INT(10) UNSIGNED NOT NULL,
  noticia_titulo CHAR(100) NOT NULL,
  noticia_resumo CHAR(255) NULL,
  noticia_imagem CHAR(255) NULL,
  noticia_link CHAR(255) NOT NULL,
  noticia_temas CHAR(50) NULL,
  noticia_partidos CHAR(255) NULL,
  noticia_pessoas CHAR(255) NULL,
  PRIMARY KEY(noticia_id, noticia_user_id)
);

CREATE TABLE not_noticia_x_partido (
  partido_id SMALLINT(4) UNSIGNED NOT NULL,
  noticia_id INT(10) UNSIGNED NOT NULL,
  noticia_aux_qtd_ligacao INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(partido_id, noticia_id)
);

CREATE TABLE not_noticia_x_pessoa (
  pessoa_id INT(10) UNSIGNED NOT NULL,
  noticia_id INT(10) UNSIGNED NOT NULL,
  noticia_aux_qtd_ligacao INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(pessoa_id, noticia_id)
);

CREATE TABLE not_noticia_x_tema (
  noticia_id INT(10) UNSIGNED NOT NULL,
  tema_id TINYINT(2) UNSIGNED NOT NULL,
  PRIMARY KEY(noticia_id, tema_id)
);

CREATE TABLE not_noticia_x_usuario (
  noticia_id INT(10) UNSIGNED NOT NULL,
  user_id INT(10) NOT NULL AUTO_INCREMENT,
  user_importancia_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(noticia_id, user_id)
);

CREATE TABLE not_veiculo (
  veiculo_id INT(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  veiculo_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  veiculo_nome CHAR(100) NOT NULL,
  veiculo_link CHAR(100) NOT NULL,
  PRIMARY KEY(veiculo_id),
  INDEX idx_veiculo_link(veiculo_link)
);

CREATE TABLE pro_apreciacao (
  apreciacao_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  apreciacao_grupo_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  apreciacao_origem_id TINYINT UNSIGNED NOT NULL DEFAULT 0,
  apreciacao_cd_origem INT(10) NOT NULL,
  apreciacao_nome CHAR(255) NOT NULL,
  apreciacao_descricao CHAR(255) NULL,
  PRIMARY KEY(apreciacao_id)
);

CREATE TABLE pro_comentario (
  projeto_id INT(10) UNSIGNED NOT NULL,
  coment_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  coment_status TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  coment_pai_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  coment_qtd_filhos INT(10) UNSIGNED NOT NULL DEFAULT 0,
  coment_texto CHAR(255) NULL,
  coment_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  coment_user_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(projeto_id, coment_id)
);

CREATE TABLE pro_projeto (
  projeto_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  projeto_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  projeto_origem_id INT(10) UNSIGNED NOT NULL DEFAULT 1,
  projeto_cd_origem INT(10) UNSIGNED NOT NULL,
  projeto_url CHAR(20) NULL,
  projeto_tplei_id INT(10) UNSIGNED NOT NULL DEFAULT 1,
  projeto_situacao_id INT(10) UNSIGNED NOT NULL DEFAULT 1,
  projeto_regime_id INT(10) UNSIGNED NOT NULL,
  projeto_apreciacao_id INT(10) UNSIGNED NOT NULL,
  projeto_numero INT(10) UNSIGNED NOT NULL,
  projeto_ano SMALLINT(4) UNSIGNED NOT NULL,
  projeto_nome CHAR(100) NOT NULL,
  projeto_ementa TEXT NULL,
  projeto_ementa_explicada TEXT NULL,
  projeto_descricao TEXT NULL,
  projeto_autor CHAR(255) NULL,
  projeto_link_inteiro_teor CHAR(255) NULL,
  projeto_data_apresentacao INT(10) UNSIGNED NULL,
  projeto_imagem CHAR(100) NULL,
  projeto_seo_titulo CHAR(255) NULL,
  projeto_seo_palavra_chave CHAR(100) NULL,
  projeto_seo_descricao CHAR(255) NULL,
  projeto_qtd_positivos INT(9) UNSIGNED NOT NULL DEFAULT 0,
  projeto_qtd_negativos INT(9) UNSIGNED NOT NULL DEFAULT 0,
  projeto_qtd_comentarios INT(9) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(projeto_id)
);

CREATE TABLE pro_projeto_vw (
  projeto_id INT(10) UNSIGNED NOT NULL,
  estado_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  projeto_vw_qtd_positivos INT(10) UNSIGNED NOT NULL DEFAULT 0,
  projeto_vw_qtd_negativos INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(projeto_id)
);

CREATE TABLE pro_projeto_x_partido (
  projeto_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  partido_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  partido_criacao_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  partido_participacao_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  partido_pontuacao SMALLINT(5) NOT NULL DEFAULT 0,
  PRIMARY KEY(projeto_id, partido_id),
  INDEX idx_partido_id(partido_id)
);

CREATE TABLE pro_projeto_x_pessoa (
  projeto_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_criacao_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_participacao_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_pontuacao SMALLINT(4) NOT NULL DEFAULT 0,
  PRIMARY KEY(projeto_id, pessoa_id),
  INDEX idx_pessoa_id(pessoa_id)
);

CREATE TABLE pro_projeto_x_tema (
  projeto_id INT(10) UNSIGNED NOT NULL,
  tema_id TINYINT(2) UNSIGNED NOT NULL,
  PRIMARY KEY(projeto_id, tema_id)
);

CREATE TABLE pro_projeto_x_usuario (
  projeto_id INT(10) UNSIGNED NOT NULL,
  user_id INTEGER UNSIGNED NOT NULL,
  user_participacao_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(projeto_id, user_id),
  INDEX idx_user_id(user_id)
);

CREATE TABLE pro_regime (
  regime_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  regime_grupo_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  regime_origem_id TINYINT UNSIGNED NOT NULL DEFAULT 0,
  regime_cd_origem INT(10) NOT NULL,
  regime_nome CHAR(255) NOT NULL,
  regime_descricao CHAR(255) NULL,
  PRIMARY KEY(regime_id)
);

CREATE TABLE pro_situacao (
  situacao_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  situacao_grupo_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  situacao_origem_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  situacao_cd_origem INT(10) NOT NULL,
  situacao_nome CHAR(255) NOT NULL,
  situacao_descricao CHAR(255) NULL,
  PRIMARY KEY(situacao_id)
);

CREATE TABLE pro_tema (
  tema_id TINYINT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
  tema_destaque SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  tema_peso FLOAT(5,2) NOT NULL DEFAULT 0.01,
  tema_nome CHAR(50) NOT NULL,
  PRIMARY KEY(tema_id)
);

CREATE TABLE pro_tipo_lei (
  tplei_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  tplei_grupo_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  tplei_origem_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  tplei_cd_origem INT(10) NOT NULL,
  tplei_sigla CHAR(10) NOT NULL,
  tplei_nome CHAR(255) NOT NULL,
  tplei_descricao CHAR(255) NULL,
  PRIMARY KEY(tplei_id)
);

CREATE TABLE pu_bem (
  bem_id BIGINT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
  bem_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  bem_pessoa_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  bem_eleicao_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  bem_estado_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  bem_tipo_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  bem_valor INT(10) UNSIGNED NULL,
  bem_nome CHAR(200) NULL,
  bem_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  bem_data_alt INT(10) UNSIGNED NOT NULL DEFAULT 0,
  bem_pessoa_cd_tse BIGINT(15) UNSIGNED NOT NULL DEFAULT 0,
  bem_refer_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(bem_id),
  INDEX idx_pessoa_id(bem_pessoa_id),
  INDEX idx_eleicao_id_x_estado_id(bem_eleicao_id, bem_estado_id),
  INDEX idx_pessoa_cd_tse(bem_pessoa_cd_tse)
);

CREATE TABLE pu_bem_tipo (
  bem_tipo_id SMALLINT(4) UNSIGNED NOT NULL,
  bem_tipo_nome CHAR(100) NOT NULL,
  bem_tipo_descricao CHAR(250) NULL,
  PRIMARY KEY(bem_tipo_id)
);

CREATE TABLE pu_candidatura (
  cand_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  cand_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  cand_pessoa_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  cand_eleicao_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  cand_estado_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  cand_municipio_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  cand_cargo_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  cand_partido_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  cand_legenda_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  cand_turno_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  cand_aux_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  cand_status_cand_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  cand_status_total_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  cand_urna_numero INT(8) UNSIGNED NULL,
  cand_urna_nome CHAR(100) NULL,
  PRIMARY KEY(cand_id),
  INDEX idx_pessoa_id(cand_pessoa_id),
  INDEX idx_eleicao_id_x_cargo_id(cand_eleicao_id, cand_estado_id, cand_municipio_id, cand_cargo_id)
);

CREATE TABLE pu_candidatura_detalhe (
  cand_id INT(10) UNSIGNED NOT NULL,
  cand_ocupacao_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  cand_escolaridade_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  cand_estado_civil_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  cand_idade TINYINT(2) UNSIGNED NULL,
  cand_total_patrimonio INT(10) UNSIGNED NULL,
  cand_total_verba INT(10) UNSIGNED NULL,
  cand_total_votos INT(9) UNSIGNED NULL,
  cand_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  cand_data_alt INT(10) UNSIGNED NOT NULL DEFAULT 0,
  cand_pessoa_cd_tse BIGINT(12) UNSIGNED NOT NULL DEFAULT 0,
  cand_refer_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(cand_id)
);

CREATE TABLE pu_cargo (
  cargo_id TINYINT(2) UNSIGNED NOT NULL,
  cargo_nome CHAR(50) NOT NULL,
  PRIMARY KEY(cargo_id)
);

CREATE TABLE pu_eleicao (
  eleicao_id SMALLINT(4) UNSIGNED NOT NULL,
  eleicao_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  eleicao_tipo_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(eleicao_id)
);

CREATE TABLE pu_estado (
  estado_id TINYINT(2) UNSIGNED NOT NULL,
  estado_sigla CHAR(3) NOT NULL,
  estado_nome CHAR(50) NOT NULL,
  PRIMARY KEY(estado_id)
);

CREATE TABLE pu_legenda (
  legenda_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  legenda_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  legenda_eleicao_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  legenda_estado_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  legenda_municipio_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  legenda_cargo_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  legenda_turno_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  legenda_cand_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  legenda_tipo_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  legenda_sigla CHAR(200) NULL,
  legenda_nome CHAR(200) NULL,
  legenda_imagem CHAR(100) NULL,
  legenda_composicao VARCHAR(500) NULL,
  legenda_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  legenda_data_alt INT(10) UNSIGNED NOT NULL DEFAULT 0,
  legenda_cd_tse BIGINT(15) UNSIGNED NOT NULL DEFAULT 0,
  legenda_refer_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(legenda_id),
  INDEX idx_eleicao_id_x_cargo_id(legenda_eleicao_id, legenda_estado_id, legenda_municipio_id, legenda_cargo_id),
  INDEX idx_cand_id(legenda_cand_id),
  INDEX idx_cd_tse(legenda_cd_tse)
);

CREATE TABLE pu_legenda_x_partido (
  legenda_id INT(10) UNSIGNED NOT NULL,
  partido_id SMALLINT(4) UNSIGNED NOT NULL,
  PRIMARY KEY(legenda_id, partido_id)
);

CREATE TABLE pu_municipio (
  municipio_id SMALLINT(4) UNSIGNED NOT NULL,
  municipio_estado_id TINYINT(2) UNSIGNED NOT NULL,
  municipio_nome CHAR(100) NOT NULL,
  municipio_cd_tse BIGINT(12) UNSIGNED NULL,
  PRIMARY KEY(municipio_id),
  INDEX idx_estado_id(municipio_estado_id),
  INDEX idx_cd_tse(municipio_cd_tse)
);

CREATE TABLE pu_ocupacao (
  ocupacao_id SMALLINT(4) UNSIGNED NOT NULL,
  ocupacao_nome CHAR(150) NOT NULL,
  PRIMARY KEY(ocupacao_id)
);

CREATE TABLE pu_partido (
  partido_id SMALLINT(4) UNSIGNED NOT NULL,
  partido_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  partido_url CHAR(50) NULL,
  partido_numero SMALLINT(4) UNSIGNED NULL,
  partido_sigla CHAR(10) NULL,
  partido_nome CHAR(200) NULL,
  partido_cnpj BIGINT(14) UNSIGNED NULL,
  partido_flags SMALLINT(5) UNSIGNED ZEROFILL NOT NULL DEFAULT 0,
  partido_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  partido_data_alt INT(10) UNSIGNED NOT NULL DEFAULT 0,
  partido_refer_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(partido_id),
  INDEX idx_url(partido_url),
  INDEX idx_nome(partido_nome)
);

CREATE TABLE pu_partido_de_para (
  partido_id SMALLINT(4) UNSIGNED NOT NULL,
  partido_origem_id INT(10) UNSIGNED NOT NULL,
  partido_cd_origem INT(10) UNSIGNED NOT NULL,
  partido_nome CHAR(100) NULL,
  PRIMARY KEY(partido_id, partido_origem_id, partido_cd_origem),
  INDEX idx_partido_cd_origem(partido_cd_origem)
);

CREATE TABLE pu_partido_diretorio (
  diretorio_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  diretorio_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  diretorio_partido_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  diretorio_estado_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  diretorio_municipio_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  diretorio_esfera_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  diretorio_tipo_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  diretorio_site CHAR(250) NULL,
  diretorio_email CHAR(250) NULL,
  diretorio_telefone CHAR(100) NULL,
  diretorio_fax CHAR(50) NULL,
  diretorio_endereco CHAR(250) NULL,
  diretorio_data_alt INT(10) UNSIGNED NOT NULL DEFAULT 0,
  diretorio_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  diretorio_refer_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(diretorio_id),
  INDEX idx_partido_id_x_municipio_id(diretorio_partido_id, diretorio_estado_id, diretorio_municipio_id)
);

CREATE TABLE pu_partido_diretorio_x_posto (
  diretorio_id SMALLINT(5) UNSIGNED NOT NULL,
  posto_id SMALLINT(4) UNSIGNED NOT NULL,
  posto_sequencial_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  posto_complemento CHAR(100) NULL,
  posto_arquivo CHAR(100) NULL,
  PRIMARY KEY(diretorio_id, posto_id, posto_sequencial_id)
);

CREATE TABLE pu_partido_diretorio_x_posto_x_pessoa (
  pessoa_id INT(10) UNSIGNED NOT NULL,
  diretorio_id SMALLINT(5) UNSIGNED NOT NULL,
  posto_id SMALLINT(4) UNSIGNED NOT NULL,
  sequencial_id SMALLINT(4) UNSIGNED NOT NULL,
  posto_obs CHAR(100) NULL,
  posto_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  posto_data_fim INT(10) UNSIGNED NOT NULL DEFAULT 0,
  posto_data_alt INT(10) UNSIGNED NOT NULL DEFAULT 0,
  posto_refer_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(pessoa_id, diretorio_id, posto_id, sequencial_id)
);

CREATE TABLE pu_partido_posto (
  posto_id SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  posto_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  posto_nome CHAR(100) NULL,
  PRIMARY KEY(posto_id)
);

CREATE TABLE pu_partido_x_eleicao (
  eleicao_id SMALLINT(4) UNSIGNED NOT NULL,
  partido_id SMALLINT(4) UNSIGNED NOT NULL,
  PRIMARY KEY(eleicao_id, partido_id)
);

CREATE TABLE pu_partido_x_ranking (
  rank_ano SMALLINT(4) UNSIGNED NOT NULL,
  partido_id SMALLINT(4) UNSIGNED NOT NULL,
  rank_qtd_pontos SMALLINT(5) NOT NULL DEFAULT 0,
  rank_qtd_aprovacoes INT(10) UNSIGNED NOT NULL DEFAULT 0,
  rank_qtd_reprovacoes INT(10) UNSIGNED NOT NULL DEFAULT 0,
  rank_qtd_positivos INT(10) UNSIGNED NOT NULL DEFAULT 0,
  rank_qtd_negtivos INT(10) UNSIGNED NOT NULL DEFAULT 0,
  cand_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(rank_ano, partido_id)
);

CREATE TABLE pu_pessoa (
  pessoa_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  pessoa_status_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_titulo_eleitor BIGINT(12) UNSIGNED ZEROFILL NOT NULL DEFAULT 0,
  pessoa_cpf BIGINT(11) UNSIGNED ZEROFILL NOT NULL DEFAULT 0,
  pessoa_url CHAR(250) NULL,
  pessoa_nome CHAR(200) NULL,
  pessoa_apelido CHAR(100) NULL,
  pessoa_diretorio CHAR(100) NULL,
  pessoa_flags SMALLINT(5) UNSIGNED ZEROFILL NOT NULL DEFAULT 0,
  PRIMARY KEY(pessoa_id),
  INDEX idx_url(pessoa_url),
  INDEX idx_titulo_eleitor(pessoa_titulo_eleitor),
  INDEX idx_cpf(pessoa_cpf)
);

CREATE TABLE pu_pessoa_detalhe (
  pessoa_id INT(10) UNSIGNED NOT NULL,
  pessoa_rg CHAR(20) NULL,
  pessoa_sexo_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_nacionalidade_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_nat_estado_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_nat_municipio_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_data_nascimento DATE NULL,
  pessoa_ocupacao_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_escolaridade_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_estado_civil_id TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_end_estado_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_end_municipio_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_endereco CHAR(250) NULL,
  pessoa_telefone CHAR(100) NULL,
  pessoa_fax CHAR(50) NULL,
  pessoa_email CHAR(250) NULL,
  pessoa_site CHAR(250) NULL,
  pessoa_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_data_alt INT(10) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_refer_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(pessoa_id)
);

CREATE TABLE pu_pessoa_de_para (
  pessoa_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_origem_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_cd_origem INT(10) UNSIGNED NOT NULL DEFAULT 0,
  pessoa_nome CHAR(100) NULL,
  PRIMARY KEY(pessoa_id, pessoa_origem_id, pessoa_cd_origem),
  INDEX idx_pessoa_cd_origem(pessoa_cd_origem)
);

CREATE TABLE pu_pessoa_imagem (
  pessoa_id INT(10) UNSIGNED NOT NULL,
  imagem_tipo CHAR(20) NULL,
  imagem_data BLOB NULL,
  PRIMARY KEY(pessoa_id)
);

CREATE TABLE pu_pessoa_termo (
  termo_id BIGINT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  termo_tipo TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  termo_nome CHAR(100) NOT NULL,
  termo_qtd_buscas BIGINT(15) UNSIGNED NOT NULL DEFAULT 0,
  termo_qtd_resultados BIGINT(12) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(termo_id),
  UNIQUE INDEX idx_nome(termo_nome),
  INDEX idx_qtd_buscas(termo_qtd_buscas)
);

CREATE TABLE pu_pessoa_termo_indice (
  termo_id BIGINT(12) UNSIGNED NOT NULL,
  pessoa_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY(termo_id, pessoa_id)
);

CREATE TABLE pu_pessoa_x_ranking (
  rank_ano SMALLINT(4) UNSIGNED NOT NULL,
  pessoa_id INT(10) UNSIGNED NOT NULL,
  rank_qtd_pontos SMALLINT(5) NOT NULL DEFAULT 0,
  rank_qtd_aprovacoes INT(10) UNSIGNED NOT NULL DEFAULT 0,
  rank_qtd_reprovacoes INT(10) UNSIGNED NOT NULL DEFAULT 0,
  rank_qtd_positivos INT(10) UNSIGNED NOT NULL DEFAULT 0,
  rank_qtd_negtivos INT(10) UNSIGNED NOT NULL DEFAULT 0,
  cand_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(rank_ano, pessoa_id)
);

CREATE TABLE pu_vaga (
  eleicao_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  estado_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  municipio_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  cargo_id TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  vaga_qtd TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  vaga_salario SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  vaga_data_ini INT(10) UNSIGNED NOT NULL DEFAULT 0,
  vaga_data_alt INT(10) UNSIGNED NOT NULL DEFAULT 0,
  vaga_refer_id SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(eleicao_id, estado_id, municipio_id, cargo_id)
);



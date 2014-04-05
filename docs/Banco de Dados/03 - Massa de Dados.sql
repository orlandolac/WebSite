-- BEM
CREATE INDEX idx_pessoa_id ON pu_bem ( bem_pessoa_id );
CREATE INDEX idx_eleicao_id_x_estado_id ON pu_bem ( bem_eleicao_id, bem_estado_id );
CREATE INDEX idx_pessoa_cd_tse ON pu_bem ( bem_pessoa_cd_tse );

-- CANDIDATURA
CREATE INDEX idx_pessoa_id ON pu_candidatura ( cand_pessoa_id );
CREATE INDEX idx_eleicao_id_x_cargo_id ON pu_candidatura ( cand_eleicao_id, cand_estado_id, cand_municipio_id, cand_cargo_id );
CREATE INDEX idx_pessoa_cd_tse ON pu_candidatura_detalhe ( cand_pessoa_cd_tse );

-- MUNICIPIO
CREATE INDEX idx_estado_id ON pu_municipio ( municipio_estado_id );
CREATE INDEX idx_cd_tse ON pu_municipio ( municipio_cd_tse );

-- PARTIDO
CREATE INDEX idx_url ON pu_partido ( partido_url );
CREATE INDEX idx_nome ON pu_partido ( partido_nome );

-- PESSOA
CREATE INDEX idx_url ON pu_pessoa ( pessoa_url );
CREATE INDEX idx_titulo_eleitor ON pu_pessoa ( pessoa_titulo_eleitor );
CREATE INDEX idx_cpf ON pu_pessoa ( pessoa_cpf );

-- TERMO
CREATE INDEX idx_nome ON pu_pessoa_termo ( termo_nome );
CREATE INDEX idx_qtd_buscas ON pu_pessoa_termo ( termo_qtd_buscas );

-- NOTICIA
CREATE INDEX idx_veiculo_link ON not_veiculo ( veiculo_link );
CREATE INDEX idx_data ON not_noticia ( noticia_data );
CREATE INDEX idx_importancia_media ON not_noticia ( noticia_importancia_media, noticia_data );

-- PROJETO
CREATE INDEX idx_projeto_ano ON pro_projeto ( projeto_ano );
CREATE INDEX idx_projeto_tplei_id ON pro_projeto ( projeto_tplei_id );
CREATE INDEX idx_projeto_situacao_id ON pro_projeto ( projeto_situacao_id );

CREATE INDEX idx_user_id ON pro_projeto_x_usuario ( user_id );
CREATE INDEX idx_pessoa_id ON pro_projeto_x_pessoa ( pessoa_id );
CREATE INDEX idx_partido_id ON pro_projeto_x_partido ( partido_id );

-- DE PARA
CREATE INDEX idx_pessoa_cd_origem ON pu_pessoa_de_para ( pessoa_cd_origem );
CREATE INDEX idx_partido_cd_origem ON pu_partido_de_para ( partido_cd_origem );

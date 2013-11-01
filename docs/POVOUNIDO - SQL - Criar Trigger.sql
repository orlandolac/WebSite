DROP TRIGGER IF EXISTS pessoa_termo_delete;
DELIMITER //
CREATE TRIGGER pessoa_termo_delete AFTER DELETE ON pu_pessoa_termo FOR EACH ROW BEGIN

END;//
DELIMITER ;

DROP TRIGGER IF EXISTS candidatura_update;
DELIMITER //
CREATE TRIGGER candidatura_update AFTER UPDATE ON pu_candidatura FOR EACH ROW BEGIN

END;//
DELIMITER ;

-- PROJETOS ----------------------------------------------------------------------------------------

DROP TRIGGER IF EXISTS projeto_x_usuario_insert;
DELIMITER //
CREATE TRIGGER projeto_x_usuario_insert AFTER INSERT ON pro_projeto_x_usuario FOR EACH ROW BEGIN

        IF NEW.user_participacao_id=1 THEN
            UPDATE pro_projeto SET projeto_qtd_positivos=projeto_qtd_positivos+1 WHERE projeto_id=NEW.projeto_id;
	END IF;

        IF NEW.user_participacao_id=2 THEN
            UPDATE pro_projeto SET projeto_qtd_negativos=projeto_qtd_negativos+1 WHERE projeto_id=NEW.projeto_id;
	END IF;

END;//
DELIMITER ;

DROP TRIGGER IF EXISTS projeto_x_usuario_update;
DELIMITER //
CREATE TRIGGER projeto_x_usuario_update AFTER UPDATE ON pro_projeto_x_usuario FOR EACH ROW BEGIN

        IF NEW.user_participacao_id <> OLD.user_participacao_id THEN

            IF NEW.user_participacao_id = 1 THEN
                UPDATE pro_projeto SET projeto_qtd_positivos=projeto_qtd_positivos+1 WHERE projeto_id=NEW.projeto_id;
                UPDATE pro_projeto SET projeto_qtd_negativos=projeto_qtd_negativos-1 WHERE projeto_id=NEW.projeto_id && projeto_qtd_negativos > 0;
            END IF;

            IF NEW.user_participacao_id = 2 THEN
                UPDATE pro_projeto SET projeto_qtd_negativos=projeto_qtd_negativos+1 WHERE projeto_id=NEW.projeto_id;
                UPDATE pro_projeto SET projeto_qtd_positivos=projeto_qtd_positivos-1 WHERE projeto_id=NEW.projeto_id && projeto_qtd_positivos > 0;
            END IF;

	END IF;

END;//
DELIMITER ;

-- NOTICIAS ----------------------------------------------------------------------------------------

DROP TRIGGER IF EXISTS noticia_x_usuario_insert;
DELIMITER //
CREATE TRIGGER noticia_x_usuario_insert AFTER INSERT ON not_noticia_x_usuario FOR EACH ROW BEGIN

        UPDATE not_noticia SET noticia_qtd_importancias=noticia_qtd_importancias+1, noticia_importancia_media=(SELECT AVG(user_importancia_id) FROM not_noticia_x_usuario WHERE noticia_id=NEW.noticia_id) WHERE noticia_id=NEW.noticia_id;

END;//
DELIMITER ;

DROP TRIGGER IF EXISTS noticia_x_usuario_update;
DELIMITER //
CREATE TRIGGER noticia_x_usuario_update AFTER UPDATE ON not_noticia_x_usuario FOR EACH ROW BEGIN

        UPDATE not_noticia SET noticia_importancia_media=(SELECT AVG(user_importancia_id) FROM not_noticia_x_usuario WHERE noticia_id=NEW.noticia_id) WHERE noticia_id=NEW.noticia_id;

END;//
DELIMITER ;

DROP TRIGGER IF EXISTS noticia_x_usuario_delete;
DELIMITER //
CREATE TRIGGER noticia_x_usuario_delete AFTER DELETE ON not_noticia_x_usuario FOR EACH ROW BEGIN

        UPDATE not_noticia SET noticia_importancia_media=(SELECT AVG(user_importancia_id) FROM not_noticia_x_usuario WHERE noticia_id=OLD.noticia_id) WHERE noticia_id=OLD.noticia_id;
        UPDATE not_noticia SET noticia_qtd_importancias=noticia_qtd_importancias-1 WHERE noticia_id=OLD.noticia_id && noticia_qtd_importancias>0;

END;//
DELIMITER ;

CREATE USER 'sistema_regitro_tutorias_usuario'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON sistema_registro_tutorias.* TO 'sistema_regitro_tutorias_usuario'@'localhost';
FLUSH PRIVILEGES;
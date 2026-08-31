CREATE USER 'sistema_regitro_tutorias_usuario'@'localhost' IDENTIFIED BY 'Morales300802';
GRANT ALL PRIVILEGES ON sistema_registro_tutorias.* TO 'sistema_regitro_tutorias_usuario'@'localhost';
FLUSH PRIVILEGES;

GRANT ALL PRIVILEGES ON sistema_registro_tutorias_test.* TO 'sistema_regitro_tutorias_usuario'@'localhost';
FLUSH PRIVILEGES;
CREATE TABLE registros_inscripciones (
    id_registro INTEGER AUTO_INCREMENT PRIMARY KEY,
    id_inscripcion INTEGER,
    cedula_alumno VARCHAR(50),
    fecha_inscripcion DATE,
    FOREIGN KEY fk_inscripcion (id_inscripcion) REFERENCES inscripciones(id_inscripcion),
    FOREIGN KEY fk_alumnos (cedula_alumno) REFERENCES alumnos(cedula)
    );
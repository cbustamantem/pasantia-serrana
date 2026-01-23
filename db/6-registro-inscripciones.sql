CREATE TABLE registros_inscripciones (
    id_registro INTEGER AUTO_INCREMENT PRIMARY KEY,
    id_inscripcion INTEGER,
    id_alumno INTEGER,
    fecha_inscripcion DATE,
    FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id_inscripcion),
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno)
    );
CREATE TABLE inscripciones (
    id_inscripcion INTEGER AUTO_INCREMENT PRIMARY KEY, 
    id_curso INTEGER, 
    id_materia INTEGER, 
    cedula_profesor INTEGER,
    FOREIGN KEY fk_curso (id_curso) REFERENCES cursos(id_curso), 
    FOREIGN KEY fk_materia (id_materia) REFERENCES materias(id_materia),
    FOREIGN KEY fk_profesor (cedula_profesor) REFERENCES profesores(cedula)    
    );

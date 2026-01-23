CREATE TABLE inscripciones (
    id_inscripcion INTEGER AUTO_INCREMENT PRIMARY KEY, 
    id_curso INTEGER, 
    id_materia INTEGER, 
    id_profesor INTEGER,
    FOREIGN KEY (id_curso) REFERENCES cursos(id_curso), 
    FOREIGN KEY (id_materia) REFERENCES materias(id_materia),
    FOREIGN KEY (id_profesor) REFERENCES profesores(id_profesor)    
    );

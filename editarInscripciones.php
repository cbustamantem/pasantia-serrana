<?php

/* =========================
 * CONFIGURACIÓN
 * ========================= */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '123123');
define('DB_NAME', 'colegio');

/* =========================
 * FUNCIÓN DE CONEXIÓN
 * ========================= */
function conectarBD() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $conexion->set_charset("utf8");
    return $conexion;
}

/* =========================
 * FUNCIÓN OBTENER INSCRIPCIÓN POR ID
 * ========================= */
function obtenerInscripcion($id_inscripcion) {
    $conexion = conectarBD();

    $sql = "SELECT id_inscripcion, id_curso, id_materia, cedula_profesor FROM inscripciones WHERE id_inscripcion = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id_inscripcion);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $inscripcion = $resultado->fetch_assoc();

    $stmt->close();
    $conexion->close();

    return $inscripcion;
}

/* =========================
 * FUNCIÓN OBTENER CURSOS
 * ========================= */
function obtenerCursos() {
    $conexion = conectarBD();

    $sql = "SELECT id_curso, descripcion FROM cursos ORDER BY descripcion";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    $cursos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $cursos[] = $fila;
    }

    $conexion->close();
    return $cursos;
}

/* =========================
 * FUNCIÓN OBTENER MATERIAS
 * ========================= */
function obtenerMaterias() {
    $conexion = conectarBD();

    $sql = "SELECT id_materia, descripcion FROM materias ORDER BY descripcion";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    $materias = [];
    while ($fila = $resultado->fetch_assoc()) {
        $materias[] = $fila;
    }

    $conexion->close();
    return $materias;
}

/* =========================
 * FUNCIÓN OBTENER PROFESORES
 * ========================= */
function obtenerProfesores() {
    $conexion = conectarBD();

    $sql = "SELECT cedula, nombre, apellido FROM profesores ORDER BY nombre, apellido";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    $profesores = [];
    while ($fila = $resultado->fetch_assoc()) {
        $profesores[] = $fila;
    }

    $conexion->close();
    return $profesores;
}

/* =========================
 * FUNCIÓN ACTUALIZAR INSCRIPCIÓN
 * ========================= */
function actualizarInscripcion($id_inscripcion, $id_curso, $id_materia, $cedula_profesor) {
    $conexion = conectarBD();

    $sql = "UPDATE inscripciones SET id_curso = ?, id_materia = ?, cedula_profesor = ? WHERE id_inscripcion = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("issi", $id_curso, $cedula_profesor, $id_materia, $id_inscripcion);

    $resultado = $stmt->execute();

    $stmt->close();
    $conexion->close();

    return $resultado;
}

/* =========================
 * PROCESAMIENTO DEL POST
 * ========================= */
$mensaje = "";
$inscripcion = null;

// Obtener ID de inscripción desde GET
$id_inscripcion = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_inscripcion > 0) {
    $inscripcion = obtenerInscripcion($id_inscripcion);
    if (!$inscripcion) {
        $mensaje = "❌ Inscripción no encontrada.";
    }
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_inscripcion = intval($_POST['id_inscripcionX'] ?? 0);
    $id_curso = intval($_POST['id_cursoX'] ?? 0);
    $id_materia = intval($_POST['id_materiaX'] ?? 0);
    $cedula_profesor = trim($_POST['cedula_profesorX'] ?? '');

    if ($id_inscripcion === 0 || $id_curso === 0 || $id_materia === 0 || $cedula_profesor === '') {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        if (actualizarInscripcion($id_inscripcion, $id_curso, $id_materia, $cedula_profesor)) {
            $mensaje = "✅ Inscripción actualizada correctamente.";
            $inscripcion = obtenerInscripcion($id_inscripcion);
        } else {
            $mensaje = "❌ Error al actualizar la inscripción.";
        }
    }
}

/* =========================
 * OBTENER DATOS PARA SELECTORES
 * ========================= */
$cursos = obtenerCursos();
$materias = obtenerMaterias();
$profesores = obtenerProfesores();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Inscripción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        form {
            width: 500px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        select, input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            margin-top: 20px;
            padding: 10px 15px;
            margin-right: 10px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }
        button[type="submit"] {
            background-color: #2196F3;
            color: white;
            font-weight: bold;
        }
        button[type="submit"]:hover {
            background-color: #0b7dda;
        }
        button[type="reset"] {
            background-color: #6c757d;
            color: white;
            font-weight: bold;
        }
        button[type="reset"]:hover {
            background-color: #5a6268;
        }
        .mensaje {
            margin-top: 15px;
            font-weight: bold;
            padding: 10px;
            border-radius: 4px;
        }
        .mensaje.exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .button-group {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Editar Inscripción</h2>

<?php if ($mensaje): ?>
    <div class="mensaje <?= strpos($mensaje, '✅') !== false ? 'exito' : 'error' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<?php if ($inscripcion): ?>
    <form method="POST" action="">
        <input type="hidden" name="id_inscripcionX" value="<?= $inscripcion['id_inscripcion'] ?>">

        <label for="id_cursoX">
            Curso:
            <select name="id_cursoX" id="id_cursoX" required>
                <option value="">-- Selecciona un curso --</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?= $curso['id_curso'] ?>" 
                        <?= $curso['id_curso'] == $inscripcion['id_curso'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($curso['descripcion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label for="id_materiaX">
            Materia:
            <select name="id_materiaX" id="id_materiaX" required>
                <option value="">-- Selecciona una materia --</option>
                <?php foreach ($materias as $materia): ?>
                    <option value="<?= $materia['id_materia'] ?>"
                        <?= $materia['id_materia'] == $inscripcion['id_materia'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($materia['descripcion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label for="cedula_profesorX">
            Profesor:
            <select name="cedula_profesorX" id="cedula_profesorX" required>
                <option value="">-- Selecciona un profesor --</option>
                <?php foreach ($profesores as $profesor): ?>
                    <option value="<?= htmlspecialchars($profesor['cedula']) ?>"
                        <?= $profesor['cedula'] == $inscripcion['cedula_profesor'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="button-group">
            <button type="submit">Actualizar</button>
            <button type="reset">Limpiar</button>
        </div>
    </form>
<?php else: ?>
    <p>Por favor, selecciona una inscripción para editar.</p>
<?php endif; ?>

</body>
</html>
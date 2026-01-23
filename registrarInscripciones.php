<?php
/* =========================
 * MOSTRAR ERRORES
 * ========================= */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
 * FUNCIÓN OBTENER CURSOS
 * ========================= */
function obtenerCursos() {
    $conexion = conectarBD();

    $sql = "SELECT id_curso, descripcion FROM cursos ORDER BY descripcion";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta de cursos: " . $conexion->error);
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
        die("Error en la consulta de materias: " . $conexion->error);
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
        die("Error en la consulta de profesores: " . $conexion->error);
    }

    $profesores = [];
    while ($fila = $resultado->fetch_assoc()) {
        $profesores[] = $fila;
    }

    $conexion->close();
    return $profesores;
}

/* =========================
 * FUNCIÓN REGISTRAR INSCRIPCIÓN
 * ========================= */
function registrarInscripcion($id_curso, $id_materia, $cedula_profesor) {
    $conexion = conectarBD();

    $sql = "INSERT INTO inscripciones (id_curso, id_materia, cedula_profesor)
            VALUES (?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("iss", $id_curso, $id_materia, $cedula_profesor);

    $resultado = $stmt->execute();

    $stmt->close();
    $conexion->close();

    return $resultado;
}

/* =========================
 * PROCESAMIENTO DEL POST
 * ========================= */
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_curso        = intval($_POST['id_cursoX'] ?? 0);
    $id_materia      = intval($_POST['id_materiaX'] ?? 0);
    $cedula_profesor = trim($_POST['cedula_profesorX'] ?? '');

    if ($id_curso === 0 || $id_materia === 0 || $cedula_profesor === '') {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        if (registrarInscripcion($id_curso, $id_materia, $cedula_profesor)) {
            $mensaje = "✅ Inscripción registrada correctamente.";
        } else {
            $mensaje = "❌ Error al registrar la inscripción.";
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
    <title>Registrar Inscripción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        form {
            width: 100%;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }
        select, input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        select:focus, input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            margin-right: 10px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }
        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        button[type="reset"] {
            background-color: #6c757d;
            color: white;
        }
        button[type="reset"]:hover {
            background-color: #5a6268;
        }
        .mensaje {
            margin-top: 15px;
            font-weight: bold;
            padding: 12px;
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
        .mensaje.aviso {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .button-group {
            margin-top: 20px;
        }
        .info-debug {
            background-color: #e8f4f8;
            padding: 10px;
            border-radius: 4px;
            margin-top: 15px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Registrar Nueva Inscripción</h2>

    <?php if ($mensaje): ?>
        <div class="mensaje <?= strpos($mensaje, '✅') !== false ? 'exito' : 'error' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($profesores)): ?>
        <div class="mensaje aviso">
            ⚠️ No hay profesores registrados. Por favor, registra profesores primero.
        </div>
    <?php endif; ?>

    <?php if (empty($cursos)): ?>
        <div class="mensaje aviso">
            ⚠️ No hay cursos registrados. Por favor, registra cursos primero.
        </div>
    <?php endif; ?>

    <?php if (empty($materias)): ?>
        <div class="mensaje aviso">
            ⚠️ No hay materias registradas. Por favor, registra materias primero.
        </div>
    <?php endif; ?>

    <?php if (!empty($profesores) && !empty($cursos) && !empty($materias)): ?>
        <form method="POST" action="">
            <label for="id_cursoX">
                Curso:
                <select name="id_cursoX" id="id_cursoX" required>
                    <option value="">-- Selecciona un curso --</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?= $curso['id_curso'] ?>">
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
                        <option value="<?= $materia['id_materia'] ?>">
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
                        <option value="<?= htmlspecialchars($profesor['cedula']) ?>">
                            <?= htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="button-group">
                <button type="submit">Registrar</button>
                <button type="reset">Limpiar</button>
            </div>
        </form>

        <div class="info-debug">
            <strong>Información de depuración:</strong><br>
            Cursos disponibles: <?= count($cursos) ?><br>
            Materias disponibles: <?= count($materias) ?><br>
            Profesores disponibles: <?= count($profesores) ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
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
 * FUNCIÓN OBTENER CURSO POR ID
 * ========================= */
function obtenerCurso($id) {
    $conexion = conectarBD();

    $sql = "SELECT id_curso, descripcion FROM cursos WHERE id_curso = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $curso = $resultado->fetch_assoc();

    $stmt->close();
    $conexion->close();

    return $curso;
}

/* =========================
 * FUNCIÓN ACTUALIZAR CURSO
 * ========================= */
function actualizarCurso($id, $descripcion) {
    $conexion = conectarBD();

    $sql = "UPDATE cursos SET descripcion = ? WHERE id_curso = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("si", $descripcion, $id);

    $resultado = $stmt->execute();

    $stmt->close();
    $conexion->close();

    return $resultado;
}

/* =========================
 * PROCESAMIENTO DEL POST
 * ========================= */
$mensaje = "";
$curso = null;

// Obtener ID del curso desde GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// DEBUG
echo "<!-- DEBUG: ID recibido = " . $id . " -->";

if ($id > 0) {
    $curso = obtenerCurso($id);
    // DEBUG
    echo "<!-- DEBUG: Curso obtenido = " . json_encode($curso) . " -->";
    if (!$curso) {
        $mensaje = "❌ Curso no encontrado.";
    }
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id           = intval($_POST['idX'] ?? 0);
    $descripcion  = trim($_POST['descripcionX'] ?? '');

    if ($id === 0 || $descripcion === '') {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        if (actualizarCurso($id, $descripcion)) {
            $mensaje = "✅ Curso actualizado correctamente.";
            $curso = obtenerCurso($id);
        } else {
            $mensaje = "❌ Error al actualizar el curso.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Curso</title>
    <style>
        form {
            width: 400px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 6px;
            box-sizing: border-box;
        }
        button {
            margin-top: 15px;
            padding: 8px 12px;
            margin-right: 10px;
        }
        .mensaje {
            margin-top: 15px;
            font-weight: bold;
        }
        .button-group {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<h2>Editar Curso</h2>

<?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<?php if ($curso): ?>
    <form method="POST" action="">
        <input type="hidden" name="idX" value="<?= htmlspecialchars($curso['id_curso']) ?>">

        <label>
            Descripción:
            <input type="text" name="descripcionX" value="<?= htmlspecialchars($curso['descripcion']) ?>" required>
        </label>

        <div class="button-group">
            <button type="submit">Actualizar</button>
            <button type="reset">Limpiar</button>
        </div>
    </form>
<?php else: ?>
    <p>Por favor, selecciona un curso para editar.</p>
<?php endif; ?>

</body>
</html>

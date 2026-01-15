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
 * FUNCIÓN OBTENER ALUMNO POR ID
 * ========================= */
function obtenerAlumno($id) {
    $conexion = conectarBD();

    $sql = "SELECT id, cedula, nombre, apellido FROM alumnos WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $alumno = $resultado->fetch_assoc();

    $stmt->close();
    $conexion->close();

    return $alumno;
}

/* =========================
 * FUNCIÓN ACTUALIZAR ALUMNO
 * ========================= */
function actualizarAlumno($id, $cedula, $nombre, $apellido) {
    $conexion = conectarBD();

    $sql = "UPDATE alumnos SET cedula = ?, nombre = ?, apellido = ? WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("sssi", $cedula, $nombre, $apellido, $id);

    $resultado = $stmt->execute();

    $stmt->close();
    $conexion->close();

    return $resultado;
}

/* =========================
 * PROCESAMIENTO DEL POST
 * ========================= */
$mensaje = "";
$alumno = null;

// Obtener ID del alumno desde GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $alumno = obtenerAlumno($id);
    if (!$alumno) {
        $mensaje = "❌ Alumno no encontrado.";
    }
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id       = intval($_POST['idX'] ?? 0);
    $cedula   = trim($_POST['cedulaX'] ?? '');
    $nombre   = trim($_POST['nombreX'] ?? '');
    $apellido = trim($_POST['apellidoX'] ?? '');

    if ($id === 0 || $cedula === '' || $nombre === '' || $apellido === '') {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        if (actualizarAlumno($id, $cedula, $nombre, $apellido)) {
            $mensaje = "✅ Alumno actualizado correctamente.";
            $alumno = obtenerAlumno($id);
        } else {
            $mensaje = "❌ Error al actualizar el alumno.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Alumno</title>
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

<h2>Editar Alumno</h2>

<?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<?php if ($alumno): ?>
    <form method="POST" action="">
        <input type="hidden" name="idX" value="<?= htmlspecialchars($alumno['id']) ?>">

        <label>
            Cédula:
            <input type="text" name="cedulaX" value="<?= htmlspecialchars($alumno['cedula']) ?>" required>
        </label>

        <label>
            Nombre:
            <input type="text" name="nombreX" value="<?= htmlspecialchars($alumno['nombre']) ?>" required>
        </label>

        <label>
            Apellido:
            <input type="text" name="apellidoX" value="<?= htmlspecialchars($alumno['apellido']) ?>" required>
        </label>

        <div class="button-group">
            <button type="submit">Actualizar</button>
            <button type="reset">Limpiar</button>
        </div>
    </form>
<?php else: ?>
    <p>Por favor, selecciona un alumno para editar.</p>
<?php endif; ?>

</body>
</html>
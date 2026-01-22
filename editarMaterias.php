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
 * FUNCIÓN OBTENER MATERIA POR ID
 * ========================= */
function obtenerMateria($id_materia) {
    $conexion = conectarBD();

    $sql = "SELECT id_materia, descripcion FROM materias WHERE id_materia = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id_materia);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $materia = $resultado->fetch_assoc();

    $stmt->close();
    $conexion->close();

    return $materia;
}

/* =========================
 * FUNCIÓN ACTUALIZAR MATERIA
 * ========================= */
function actualizarMateria($id_materia, $descripcion) {
    $conexion = conectarBD();

    $sql = "UPDATE materias SET descripcion = ? WHERE id_materia = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("si", $descripcion, $id_materia);

    $resultado = $stmt->execute();

    $stmt->close();
    $conexion->close();

    return $resultado;
}

/* =========================
 * PROCESAMIENTO DEL POST
 * ========================= */
$mensaje = "";
$materia = null;

// Obtener ID de la materia desde GET
$id_materia = isset($_GET['id_materia']) ? intval($_GET['id_materia']) : 0;

if ($id_materia > 0) {
    $materia = obtenerMateria($id_materia);
    if (!$materia) {
        $mensaje = "❌ Materia no encontrada.";
    }
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_materia   = intval($_POST['id_materiaX'] ?? 0);
    $descripcion  = trim($_POST['descripcionX'] ?? '');

    if ($id_materia === 0 || $descripcion === '') {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        if (actualizarMateria($id_materia, $descripcion)) {
            $mensaje = "✅ Materia actualizada correctamente.";
            $materia = obtenerMateria($id_materia);
        } else {
            $mensaje = "❌ Error al actualizar la materia.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Materia</title>
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

<h2>Editar Materia</h2>

<?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<?php if ($materia): ?>
    <form method="POST" action="">
        <input type="hidden" name="id_materiaX" value="<?= htmlspecialchars($materia['id_materia']) ?>">

        <label>
            Descripción:
            <input type="text" name="descripcionX" value="<?= htmlspecialchars($materia['descripcion']) ?>" required>
        </label>

        <div class="button-group">
            <button type="submit">Actualizar</button>
            <button type="reset">Limpiar</button>
        </div>
    </form>
<?php else: ?>
    <p>Por favor, selecciona una materia para editar.</p>
<?php endif; ?>

</body>
</html>

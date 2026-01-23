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
 * FUNCIÓN OBTENER PROFESOR POR CÉDULA
 * ========================= */
function obtenerProfesor($cedula) {
    $conexion = conectarBD();

    $sql = "SELECT cedula, nombre, apellido, telefono FROM profesores WHERE cedula = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $profesor = $resultado->fetch_assoc();

    $stmt->close();
    $conexion->close();

    return $profesor;
}

/* =========================
 * FUNCIÓN ACTUALIZAR PROFESOR
 * ========================= */
function actualizarProfesor($cedula, $nombre, $apellido, $telefono) {
    $conexion = conectarBD();

    $sql = "UPDATE profesores SET nombre = ?, apellido = ?, telefono = ? WHERE cedula = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("sssi", $nombre, $apellido, $telefono, $cedula);

    $resultado = $stmt->execute();

    $stmt->close();
    $conexion->close();

    return $resultado;
}

/* =========================
 * PROCESAMIENTO DEL POST
 * ========================= */
$mensaje = "";
$profesor = null;

// Obtener cédula del profesor desde GET
$cedula = isset($_GET['cedula']) ? intval($_GET['cedula']) : 0;

if ($cedula > 0) {
    $profesor = obtenerProfesor($cedula);
    if (!$profesor) {
        $mensaje = "❌ Profesor no encontrado.";
    }
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $cedula   = intval($_POST['cedulaX'] ?? 0);
    $nombre   = trim($_POST['nombreX'] ?? '');
    $apellido = trim($_POST['apellidoX'] ?? '');
    $telefono = trim($_POST['telefonoX'] ?? '');

    if ($cedula === 0 || $nombre === '' || $apellido === '') {
        $mensaje = "⚠️ Cédula, nombre y apellido son obligatorios.";
    } else {
        if (actualizarProfesor($cedula, $nombre, $apellido, $telefono)) {
            $mensaje = "✅ Profesor actualizado correctamente.";
            $profesor = obtenerProfesor($cedula);
        } else {
            $mensaje = "❌ Error al actualizar el profesor.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Profesor</title>
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
        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="number"] {
            background-color: #f5f5f5;
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
        h2 {
            color: #2c3e50;
        }
    </style>
</head>
<body>

<h2>Editar Profesor</h2>

<?php if ($mensaje): ?>
    <div class="mensaje <?= strpos($mensaje, '✅') !== false ? 'exito' : 'error' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<?php if ($profesor): ?>
    <form method="POST" action="">
        <label>
            Cédula (No editable):
            <input type="number" name="cedulaX" value="<?= htmlspecialchars($profesor['cedula']) ?>" readonly>
        </label>

        <label>
            Nombre:
            <input type="text" name="nombreX" value="<?= htmlspecialchars($profesor['nombre']) ?>" required>
        </label>

        <label>
            Apellido:
            <input type="text" name="apellidoX" value="<?= htmlspecialchars($profesor['apellido']) ?>" required>
        </label>

        <label>
            Teléfono:
            <input type="text" name="telefonoX" value="<?= htmlspecialchars($profesor['telefono'] ?? '') ?>">
        </label>

        <div class="button-group">
            <button type="submit">Actualizar</button>
            <button type="reset">Limpiar</button>
        </div>
    </form>
<?php else: ?>
    <p>Por favor, selecciona un profesor para editar.</p>
<?php endif; ?>

</body>
</html>
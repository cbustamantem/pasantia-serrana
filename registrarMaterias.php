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
 * FUNCIÓN REGISTRAR MATERIA
 * ========================= */
function registrarMateria($descripcion) {
    $conexion = conectarBD();

    $sql = "INSERT INTO materias (descripcion)
            VALUES (?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("s", $descripcion);

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

    $descripcion = trim($_POST['descripcionX'] ?? '');

    if ($descripcion === '') {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        if (registrarMateria($descripcion)) {
            $mensaje = "✅ Materia registrada correctamente.";
        } else {
            $mensaje = "❌ Error al registrar la materia.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Materia</title>
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
        }
        button {
            margin-top: 15px;
            padding: 8px 12px;
        }
        .mensaje {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Registrar Materia</h2>

<?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label>
        Descripción:
        <input type="text" name="descripcionX" required>
    </label>

    <button type="submit">Registrar</button>
</form>

</body>
</html>

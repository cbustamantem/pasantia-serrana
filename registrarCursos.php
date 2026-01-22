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
 * FUNCIÓN REGISTRAR CURSO
 * ========================= */
function registrarCurso($descripcion) {
    $conexion = conectarBD();

    $sql = "INSERT INTO cursos (descripcion)
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
        $mensaje = "⚠️ El campo descripción es obligatorio.";
    } else {
        if (registrarCurso($descripcion)) {
            $mensaje = "✅ Curso registrado correctamente.";
        } else {
            $mensaje = "❌ Error al registrar el curso.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Curso</title>
    <style>
        form {
            width: 400px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 6px;
        }
        textarea {
            resize: vertical;
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

<h2>Registrar Curso</h2>

<?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label>
        Descripción:
        <textarea name="descripcionX" required></textarea>
    </label>

    <button type="submit">Registrar</button>
</form>

</body>
</html>

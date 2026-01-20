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
 * FUNCIÓN REGISTRAR LIBRO
 * ========================= */
function registrarLibro($titulo, $autor) {
    $conexion = conectarBD();

    $sql = "INSERT INTO libros (titulo, autor)
            VALUES (?, ?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("ss", $titulo, $autor);

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

    $titulo = trim($_POST['tituloX'] ?? '');
    $autor  = trim($_POST['autorX'] ?? '');

    if ($titulo === '' || $autor === '') {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        if (registrarLibro($titulo, $autor)) {
            $mensaje = "✅ Libro registrado correctamente.";
        } else {
            $mensaje = "❌ Error al registrar el libro.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Libro</title>
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

<h2>Registrar Libro</h2>

<?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label>
        Título:
        <input type="text" name="tituloX" required>
    </label>

    <label>
        Autor:
        <input type="text" name="autorX" required>
    </label>

    <button type="submit">Registrar</button>
</form>

</body>
</html>

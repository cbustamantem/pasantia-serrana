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
 * FUNCIÓN OBTENER LIBRO POR ID
 * ========================= */
function obtenerLibro($id_libro) {
    $conexion = conectarBD();

    $sql = "SELECT id_libro, titulo, autor FROM libros WHERE id_libro = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id_libro);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $libro = $resultado->fetch_assoc();

    $stmt->close();
    $conexion->close();

    return $libro;
}

/* =========================
 * FUNCIÓN ACTUALIZAR LIBRO
 * ========================= */
function actualizarLibro($id_libro, $titulo, $autor) {
    $conexion = conectarBD();

    $sql = "UPDATE libros SET titulo = ?, autor = ? WHERE id_libro = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("ssi", $titulo, $autor, $id_libro);

    $resultado = $stmt->execute();

    $stmt->close();
    $conexion->close();

    return $resultado;
}

/* =========================
 * PROCESAMIENTO DEL POST
 * ========================= */
$mensaje = "";
$libro = null;

// Obtener ID del libro desde GET
$id_libro = isset($_GET['id_libro']) ? intval($_GET['id_libro']) : 0;

if ($id_libro > 0) {
    $libro = obtenerLibro($id_libro);
    if (!$libro) {
        $mensaje = "❌ Libro no encontrado.";
    }
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_libro = intval($_POST['id_libroX'] ?? 0);
    $titulo   = trim($_POST['tituloX'] ?? '');
    $autor    = trim($_POST['autorX'] ?? '');

    if ($id_libro === 0 || $titulo === '' || $autor === '') {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        if (actualizarLibro($id_libro, $titulo, $autor)) {
            $mensaje = "✅ Libro actualizado correctamente.";
            $libro = obtenerLibro($id_libro);
        } else {
            $mensaje = "❌ Error al actualizar el libro.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Libro</title>
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

<h2>Editar Libro</h2>

<?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<?php if ($libro): ?>
    <form method="POST" action="">
        <input type="hidden" name="id_libroX" value="<?= htmlspecialchars($libro['id_libro']) ?>">

        <label>
            Título:
            <input type="text" name="tituloX" value="<?= htmlspecialchars($libro['titulo']) ?>" required>
        </label>

        <label>
            Autor:
            <input type="text" name="autorX" value="<?= htmlspecialchars($libro['autor']) ?>" required>
        </label>

        <div class="button-group">
            <button type="submit">Actualizar</button>
            <button type="reset">Limpiar</button>
        </div>
    </form>
<?php else: ?>
    <p>Por favor, selecciona un libro para editar.</p>
<?php endif; ?>

</body>
</html>

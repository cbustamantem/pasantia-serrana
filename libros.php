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
 * FUNCIÓN LISTAR LIBROS
 * ========================= */
function listarLibros() {
    $conexion = conectarBD();

    $sql = "SELECT id_libro, titulo, autor FROM libros";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    $libros = [];

    while ($fila = $resultado->fetch_assoc()) {
        $libros[] = $fila;
    }

    $conexion->close();
    return $libros;
}

/* =========================
 * FUNCIÓN ELIMINAR LIBRO POR ID
 * ========================= */
function eliminarLibro($id_libro) {
    $conexion = conectarBD();

    $sql = "DELETE FROM libros WHERE id_libro = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id_libro);

    $resultado = $stmt->execute();

    $stmt->close();
    $conexion->close();

    return $resultado;
}

/* =========================
 * PROCESAMIENTO DE ACCIONES
 * ========================= */
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_libro = trim($_POST['id_libro'] ?? '');

    if ($id_libro !== '') {
        if (eliminarLibro($id_libro)) {
            $mensaje = "✅ Libro eliminado correctamente.";
        } else {
            $mensaje = "❌ Error al eliminar el libro.";
        }
    }
}

/* =========================
 * USO DEL LISTADO
 * ========================= */
$libros = listarLibros();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Libros</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-agregar {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .btn-agregar:hover {
            background-color: #45a049;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
            font-weight: bold;
        }
        .btn-editar {
            padding: 5px 10px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
        }
        .btn-editar:hover {
            background-color: #0b7dda;
        }
        .btn-eliminar {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-eliminar:hover {
            background-color: #da190b;
        }
        .mensaje {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-weight: bold;
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
    </style>
</head>
<body>

<div class="header">
    <h2>Listado de Libros</h2>
    <a href="registrarLibros.php" class="btn-agregar">+ Agregar Libro</a>
</div>

<?php if ($mensaje): ?>
    <div class="mensaje <?= strpos($mensaje, '✅') !== false ? 'exito' : 'error' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Autor</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($libros) > 0): ?>
            <?php foreach ($libros as $libro): ?>
                <tr>
                    <td><?= htmlspecialchars($libro['id_libro']) ?></td>
                    <td><?= htmlspecialchars($libro['titulo']) ?></td>
                    <td><?= htmlspecialchars($libro['autor']) ?></td>
                    <td>
                        <a href="editarLibros.php?id_libro=<?= urlencode($libro['id_libro']) ?>" class="btn-editar">Editar</a>
                        
                        <form style="display: inline;" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este libro?');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_libro" value="<?= htmlspecialchars($libro['id_libro']) ?>">
                            <button type="submit" class="btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">No hay libros registrados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

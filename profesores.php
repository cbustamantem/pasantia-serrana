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
 * FUNCIÓN LISTAR PROFESORES
 * ========================= */
function listarProfesores() {
    $conexion = conectarBD();

    $sql = "SELECT cedula, nombre, apellido, telefono FROM profesores ORDER BY nombre, apellido";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    $profesores = [];

    while ($fila = $resultado->fetch_assoc()) {
        $profesores[] = $fila;
    }

    $conexion->close();
    return $profesores;
}

/* =========================
 * FUNCIÓN ELIMINAR PROFESOR POR CÉDULA
 * ========================= */
function eliminarProfesor($cedula) {
    $conexion = conectarBD();

    $sql = "DELETE FROM profesores WHERE cedula = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $cedula);

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
    $cedula = intval($_POST['cedula'] ?? 0);

    if ($cedula > 0) {
        if (eliminarProfesor($cedula)) {
            $mensaje = "✅ Profesor eliminado correctamente.";
        } else {
            $mensaje = "❌ Error al eliminar el profesor.";
        }
    }
}

/* =========================
 * USO DEL LISTADO
 * ========================= */
$profesores = listarProfesores();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Profesores</title>
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
    <h2>Listado de Profesores</h2>
    <a href="registrarProfesores.php" class="btn-agregar">+ Agregar Profesor</a>
</div>

<?php if ($mensaje): ?>
    <div class="mensaje <?= strpos($mensaje, '✅') !== false ? 'exito' : 'error' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($profesores) > 0): ?>
            <?php foreach ($profesores as $profesor): ?>
                <tr>
                    <td><?= htmlspecialchars($profesor['cedula']) ?></td>
                    <td><?= htmlspecialchars($profesor['nombre']) ?></td>
                    <td><?= htmlspecialchars($profesor['apellido']) ?></td>
                    <td><?= htmlspecialchars($profesor['telefono']) ?></td>
                    <td>
                        <a href="editarProfesores.php?cedula=<?= urlencode($profesor['cedula']) ?>" class="btn-editar">Editar</a>
                        
                        <form style="display: inline;" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este profesor?');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="cedula" value="<?= htmlspecialchars($profesor['cedula']) ?>">
                            <button type="submit" class="btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">No hay profesores registrados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
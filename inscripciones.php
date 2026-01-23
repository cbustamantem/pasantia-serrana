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
 * FUNCIÓN LISTAR INSCRIPCIONES CON JOINS
 * ========================= */
function listarInscripciones() {
    $conexion = conectarBD();

    $sql = "SELECT 
                i.id_inscripcion,
                c.descripcion AS curso,
                m.descripcion AS materia,
                p.nombre AS profesor_nombre,
                p.apellido AS profesor_apellido
            FROM inscripciones i
            INNER JOIN cursos c ON i.id_curso = c.id_curso
            INNER JOIN materias m ON i.id_materia = m.id_materia
            INNER JOIN profesores p ON i.id_profesor = p.id_profesor
            ORDER BY c.descripcion, m.descripcion";
    
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    $inscripciones = [];

    while ($fila = $resultado->fetch_assoc()) {
        $inscripciones[] = $fila;
    }

    $conexion->close();
    return $inscripciones;
}

/* =========================
 * FUNCIÓN ELIMINAR INSCRIPCIÓN
 * ========================= */
function eliminarInscripcion($id_inscripcion) {
    $conexion = conectarBD();

    $sql = "DELETE FROM inscripciones WHERE id_inscripcion = ?";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id_inscripcion);

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
    $id_inscripcion = intval($_POST['id_inscripcion'] ?? 0);

    if ($id_inscripcion > 0) {
        if (eliminarInscripcion($id_inscripcion)) {
            $mensaje = "✅ Inscripción eliminada correctamente.";
        } else {
            $mensaje = "❌ Error al eliminar la inscripción.";
        }
    }
}

/* =========================
 * USO DEL LISTADO
 * ========================= */
$inscripciones = listarInscripciones();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Inscripciones</title>
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
    <h2>Listado de Inscripciones</h2>
    <a href="registrarInscripciones.php" class="btn-agregar">+ Agregar Inscripción</a>
</div>

<?php if ($mensaje): ?>
    <div class="mensaje <?= strpos($mensaje, '✅') !== false ? 'exito' : 'error' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Curso</th>
            <th>Materia</th>
            <th>Profesor</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($inscripciones) > 0): ?>
            <?php foreach ($inscripciones as $inscripcion): ?>
                <tr>
                    <td><?= htmlspecialchars($inscripcion['curso']) ?></td>
                    <td><?= htmlspecialchars($inscripcion['materia']) ?></td>
                    <td><?= htmlspecialchars($inscripcion['profesor_nombre'] . ' ' . $inscripcion['profesor_apellido']) ?></td>
                    <td>
                        <a href="editarInscripciones.php?id=<?= $inscripcion['id_inscripcion'] ?>" class="btn-editar">Editar</a>
                        
                        <form style="display: inline;" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta inscripción?');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_inscripcion" value="<?= $inscripcion['id_inscripcion'] ?>">
                            <button type="submit" class="btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">No hay inscripciones registradas.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
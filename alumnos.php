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
 * FUNCIÓN LISTAR ALUMNOS
 * ========================= */
function listarAlumnos() {
    $conexion = conectarBD();

    $sql = "SELECT cedula, nombre, apellido FROM alumnos";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    $alumnos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $alumnos[] = $fila;
    }

    $conexion->close();
    return $alumnos;
}

/* =========================
 * USO DEL LISTADO
 * ========================= */
$alumnos = listarAlumnos();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Alumnos</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>

<h2>Listado de Alumnos</h2>

<table>
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombre</th>
            <th>Apellido</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alumnos as $alumno): ?>
            <tr>
                <td><?= htmlspecialchars($alumno['cedula']) ?></td>
                <td><?= htmlspecialchars($alumno['nombre']) ?></td>
                <td><?= htmlspecialchars($alumno['apellido']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>


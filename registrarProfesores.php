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
* FUNCIÓN REGISTRAR ALUMNO
* ========================= */
function registrarProfesor($cedula, $nombre, $apellido, $telefono) {
$conexion = conectarBD();

$sql = "INSERT INTO profesores (cedula, nombre, apellido, telefono)
VALUES (?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
die("Error al preparar consulta: " . $conexion->error);
}

$stmt->bind_param("ssss", $cedula, $nombre, $apellido, $telefono);

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

$cedula   = trim($_POST['cedula'] ?? '');
$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if ($cedula === '' || $nombre === '' || $apellido === '') {
$mensaje = "⚠️ Todos los campos son obligatorios.";
} else {
if (registrarProfesor($cedula, $nombre, $apellido, $telefono)) {
$mensaje = "✅ Profesor registrado correctamente.";
} else {
$mensaje = "❌ Error al registrar el profesor.";
}
}
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar Profesor</title>
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

<h2>Registrar Profesor</h2>

<?php if ($mensaje): ?>
<div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="POST" action="">
<label>
Cédula:
<input type="text" name="cedula" required>
</label>

<label>
Nombre:
<input type="text" name="nombre" required>
</label>

<label>
Apellido:
<input type="text" name="apellido" required>
</label>

<label>
Telefono:
<input type="text" name="telefono" required>
</label>

<button type="submit">Registrar</button>
</form>

</body>
</html>


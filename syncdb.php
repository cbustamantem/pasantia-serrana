<?php

/* =========================
 * CONFIGURACIÓN
 * ========================= */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '123123');
define('DB_NAME', 'colegio');
define('DB_FOLDER', __DIR__ . '/db');

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
 * FUNCIÓN EJECUTAR SYNC
 * ========================= */
function ejecutarSync() {
    $conexion = conectarBD();
    
    // Obtener lista de archivos SQL
    $archivos_sql = glob(DB_FOLDER . '/*.sql');
    
    if (empty($archivos_sql)) {
        return [
            'exito' => false,
            'mensaje' => '❌ No se encontraron archivos SQL en la carpeta db/',
            'detalles' => []
        ];
    }
    
    $resultados = [];
    $todos_exito = true;
    
    foreach ($archivos_sql as $archivo) {
        $nombre_archivo = basename($archivo);
        
        try {
            // Leer contenido del archivo SQL
            $sql = file_get_contents($archivo);
            
            if ($sql === false) {
                $resultados[$nombre_archivo] = [
                    'exito' => false,
                    'mensaje' => 'Error al leer el archivo'
                ];
                $todos_exito = false;
                continue;
            }
            
            // Ejecutar el SQL
            if ($conexion->multi_query($sql)) {
                // Consumir todos los resultados
                while ($conexion->next_result()) {
                    if ($resultado = $conexion->store_result()) {
                        $resultado->free();
                    }
                }
                
                $resultados[$nombre_archivo] = [
                    'exito' => true,
                    'mensaje' => '✅ Ejecutado correctamente'
                ];
            } else {
                $resultados[$nombre_archivo] = [
                    'exito' => false,
                    'mensaje' => 'Error: ' . $conexion->error
                ];
                $todos_exito = false;
            }
        } catch (Exception $e) {
            $resultados[$nombre_archivo] = [
                'exito' => false,
                'mensaje' => 'Excepción: ' . $e->getMessage()
            ];
            $todos_exito = false;
        }
    }
    
    $conexion->close();
    
    return [
        'exito' => $todos_exito,
        'mensaje' => $todos_exito ? '✅ Sincronización completada exitosamente' : '⚠️ Sincronización completada con errores',
        'detalles' => $resultados
    ];
}

/* =========================
 * PROCESAMIENTO
 * ========================= */
$resultado_sync = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ejecutar_sync'])) {
    $resultado_sync = ejecutarSync();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sincronización de Base de Datos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .container {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info p {
            margin: 5px 0;
        }
        .btn-sync {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-sync:hover {
            background-color: #45a049;
        }
        .btn-sync:active {
            transform: scale(0.98);
        }
        .resultado {
            margin-top: 30px;
            padding: 20px;
            border-radius: 4px;
        }
        .resultado.exito {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .resultado.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .resultado h2 {
            margin-top: 0;
        }
        .detalles {
            margin-top: 15px;
            background-color: rgba(0,0,0,0.05);
            padding: 15px;
            border-radius: 4px;
        }
        .detalle-item {
            padding: 10px;
            margin: 5px 0;
            background-color: white;
            border-left: 4px solid #ddd;
            border-radius: 2px;
        }
        .detalle-item.exito {
            border-left-color: #4CAF50;
            color: #155724;
        }
        .detalle-item.error {
            border-left-color: #f44336;
            color: #721c24;
        }
        .archivo-nombre {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .archivo-mensaje {
            font-size: 13px;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🔄 Sincronización de Base de Datos</h1>
    
    <div class="info">
        <p><strong>Descripción:</strong> Este script ejecutará todos los archivos SQL de la carpeta <code>db/</code></p>
        <p><strong>Ruta:</strong> <code><?= DB_FOLDER ?></code></p>
        <p><strong>Base de datos:</strong> <code><?= DB_NAME ?></code></p>
    </div>

    <form method="POST">
        <button type="submit" name="ejecutar_sync" class="btn-sync">▶ Ejecutar Sincronización</button>
    </form>

    <?php if ($resultado_sync): ?>
        <div class="resultado <?= $resultado_sync['exito'] ? 'exito' : 'error' ?>">
            <h2><?= htmlspecialchars($resultado_sync['mensaje']) ?></h2>
            
            <?php if (!empty($resultado_sync['detalles'])): ?>
                <div class="detalles">
                    <h3>Detalles:</h3>
                    <?php foreach ($resultado_sync['detalles'] as $archivo => $detalle): ?>
                        <div class="detalle-item <?= $detalle['exito'] ? 'exito' : 'error' ?>">
                            <span class="archivo-nombre">📄 <?= htmlspecialchars($archivo) ?></span>
                            <span class="archivo-mensaje"><?= htmlspecialchars($detalle['mensaje']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
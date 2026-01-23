<?php

/* =========================
 * CONFIGURACIÓN
 * ========================= */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '123123');
define('DB_NAME', 'colegio');

/* =========================
 * DETERMINAR PÁGINA ACTUAL
 * ========================= */
$pagina = isset($_GET['page']) ? basename($_GET['page']) : 'inicio';

// Páginas permitidas
$paginas_permitidas = [
    'inicio',
    'alumnos',
    'cursos',
    'libros',
    'materias',
    'profesores',
    'sincronizar'
];

// Validar que la página sea permitida
if (!in_array($pagina, $paginas_permitidas)) {
    $pagina = 'inicio';
}

// Mapeo de páginas a archivos
$mapeo_paginas = [
    'inicio' => null,
    'alumnos' => 'alumnos.php',
    'cursos' => 'cursos.php',
    'libros' => 'libros.php',
    'materias' => 'materias.php',
    'profesores' => 'profesores.php',
    'sincronizar' => 'syncdb.php'
];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Escolar - Colegio María Serrana</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        /* HEADER */
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .header-logo {
            flex-shrink: 0;
        }

        .header-logo img {
            height: 80px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .header-content {
            flex: 1;
        }

        header h1 {
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        header p {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }

        /* CONTENEDOR PRINCIPAL */
        .container {
            display: flex;
            min-height: calc(100vh - 110px);
        }

        /* SIDEBAR / MENÚ LATERAL */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            overflow-y: auto;
        }

        .sidebar-title {
            padding: 15px 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #bdc3c7;
            letter-spacing: 1px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover {
            background-color: #34495e;
            border-left-color: #667eea;
            color: #667eea;
        }

        .sidebar-menu a.active {
            background-color: #34495e;
            border-left-color: #667eea;
            color: #667eea;
            font-weight: bold;
        }

        .sidebar-menu a span {
            margin-left: 10px;
        }

        /* ICONO DE MENÚ */
        .menu-icon {
            width: 20px;
            height: 20px;
            text-align: center;
            font-size: 18px;
        }

        /* CONTENIDO PRINCIPAL */
        .content {
            flex: 1;
            padding: 40px;
            background-color: #f5f5f5;
            overflow-y: auto;
        }

        /* TARJETA DE INICIO */
        .inicio-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .inicio-container h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 32px;
            text-align: center;
        }

        .inicio-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .card-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .card p {
            color: #7f8c8d;
            font-size: 14px;
        }

        /* CONTENIDO DE PÁGINA */
        .page-content {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .header-logo img {
                height: 60px;
            }

            header h1 {
                font-size: 22px;
            }

            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                display: flex;
                overflow-x: auto;
            }

            .sidebar-menu {
                display: flex;
            }

            .sidebar-menu li {
                flex-shrink: 0;
            }

            .content {
                padding: 20px;
            }

            .inicio-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header>
        <div class="header-logo">
            <img src="assets/logo-maria-serrana.png" alt="Logo Colegio María Serrana">
        </div>
        <div class="header-content">
            <h1>Sistema de Gestión Escolar</h1>
            <p>Colegio María Serrana - Centro de Educación Superior</p>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="container">
        
        <!-- SIDEBAR MENÚ -->
        <aside class="sidebar">
            <div class="sidebar-title">Menú Principal</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php?page=inicio" class="<?= $pagina === 'inicio' ? 'active' : '' ?>">
                        <span class="menu-icon">🏠</span>
                        <span>Inicio</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=alumnos" class="<?= $pagina === 'alumnos' ? 'active' : '' ?>">
                        <span class="menu-icon">👨‍🎓</span>
                        <span>Alumnos</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=profesores" class="<?= $pagina === 'profesores' ? 'active' : '' ?>">
                        <span class="menu-icon">👨‍🏫</span>
                        <span>Profesores</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=cursos" class="<?= $pagina === 'cursos' ? 'active' : '' ?>">
                        <span class="menu-icon">📚</span>
                        <span>Cursos</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=materias" class="<?= $pagina === 'materias' ? 'active' : '' ?>">
                        <span class="menu-icon">📖</span>
                        <span>Materias</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=libros" class="<?= $pagina === 'libros' ? 'active' : '' ?>">
                        <span class="menu-icon">📕</span>
                        <span>Libros</span>
                    </a>
                </li>
                <li style="border-top: 1px solid #34495e; margin-top: 10px;">
                    <a href="index.php?page=sincronizar" class="<?= $pagina === 'sincronizar' ? 'active' : '' ?>">
                        <span class="menu-icon">🔄</span>
                        <span>Sincronizar BD</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="content">
            <?php if ($pagina === 'inicio'): ?>
                <!-- PÁGINA DE INICIO -->
                <div class="inicio-container">
                    <h2>Bienvenido al Sistema de Gestión Escolar</h2>
                    <p style="text-align: center; color: #7f8c8d; margin-bottom: 40px;">
                        Colegio María Serrana - Centro de Educación Superior
                    </p>

                    <div class="inicio-cards">
                        <a href="index.php?page=alumnos" class="card">
                            <div class="card-icon">👨‍🎓</div>
                            <h3>Alumnos</h3>
                            <p>Gestiona el registro de alumnos</p>
                        </a>

                        <a href="index.php?page=profesores" class="card">
                            <div class="card-icon">👨‍🏫</div>
                            <h3>Profesores</h3>
                            <p>Administra los datos de profesores</p>
                        </a>

                        <a href="index.php?page=cursos" class="card">
                            <div class="card-icon">📚</div>
                            <h3>Cursos</h3>
                            <p>Gestiona los cursos disponibles</p>
                        </a>

                        <a href="index.php?page=materias" class="card">
                            <div class="card-icon">📖</div>
                            <h3>Materias</h3>
                            <p>Administra las materias escolares</p>
                        </a>

                        <a href="index.php?page=libros" class="card">
                            <div class="card-icon">📕</div>
                            <h3>Libros</h3>
                            <p>Gestiona el inventario de libros</p>
                        </a>

                        <a href="index.php?page=sincronizar" class="card">
                            <div class="card-icon">🔄</div>
                            <h3>Sincronizar Base de Datos</h3>
                            <p>Sincroniza los archivos SQL</p>
                        </a>
                    </div>
                </div>

            <?php elseif ($mapeo_paginas[$pagina]): ?>
                <!-- CARGAR PÁGINA DINÁMICAMENTE -->
                <div class="page-content">
                    <?php 
                        $archivo = __DIR__ . '/' . $mapeo_paginas[$pagina];
                        if (file_exists($archivo)) {
                            include $archivo;
                        } else {
                            echo "<p>❌ Error: No se pudo encontrar la página solicitada.</p>";
                        }
                    ?>
                </div>

            <?php endif; ?>
        </div>
    </div>

</body>
</html>
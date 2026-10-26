<?php
session_start();
// Verificar si ha iniciado sesión, sino redirigir al login
if (!isset($_SESSION['usuario'])) {
    header("Location: login23.php");
    exit();
}

include('conexionfinal.php');

$usuario_actual =$_SESSION['usuario'];
$rol_actual =$_SESSION['rol']; // 'lector' (estudiante) o 'administrador' (profesor)

$totalEstudiantes = 0;
$totalSecciones = 20; 
$totalEncargados = 0;
$totalActividades = 0;

// Contar el total global de estudiantes en todas las tablas de sección
$secciones_array = ['1_1', '1_2', '1_3', '1_4', '1_5', '1_6', '1_7', '1_8', '1_9', '1_10', '1_11', '2_1', '2_2', '2_3', '2_4', '2_5', '2_6', '2_7', '2_8', '2_9'];
foreach ($secciones_array as$sec) {
    $tabla = "estudiantes_" . $sec;
    $res =$conn->query("SELECT COUNT(*) AS total FROM `$tabla`");
    if ($res) {
        $fila =$res->fetch_assoc();
        $totalEstudiantes +=$fila['total'];
    }
}

// Contar Encargados
$resEncargados =$conn->query("SELECT COUNT(*) AS total FROM encargados");
if ($resEncargados) {
    $fila =$resEncargados->fetch_assoc();
    $totalEncargados =$fila['total'];
}

// Contar Actividades
$resActividades =$conn->query("SELECT COUNT(*) AS total FROM actividades");
if ($resActividades) {
    $fila =$resActividades->fetch_assoc();
    $totalActividades =$fila['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inicio - Servicio Social INAC</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
* { box-sizing: border-box; }
body { margin: 0; background-color: #e9e5dc; font-family: 'Segoe UI', Arial, sans-serif; color: #4b463f; }
.dashboard { width: 100%; min-height: 100vh; padding: 30px; }
.welcome { background-color: #ffffff; border-radius: 16px; padding: 30px; border: 1px solid #ddd5c8; box-shadow: 0 5px 18px rgba(70,55,35,0.07); margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; gap: 25px; }
.welcome-text { flex: 1; }
.welcome-title { font-size: 1.7rem; font-weight: 700; color: #5f4024; margin-bottom: 8px; }
.welcome-description { color: #81796e; font-size: 0.95rem; margin: 0; max-width: 650px; line-height: 1.6; }
.welcome-icon { width: 75px; height: 75px; background-color: #f0e7d8; color: #9a6426; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; flex-shrink: 0; }
.section-title { font-size: 1rem; font-weight: 700; color: #5f4024; margin-bottom: 15px; }
.stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 30px; }
.stat-card { background-color: #ffffff; border: 1px solid #ddd5c8; border-radius: 14px; padding: 22px; box-shadow: 0 4px 14px rgba(70,55,35,0.06); transition: all 0.2s ease; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(70,55,35,0.10); }
.stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
.stat-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; background-color: #f0e7d8; color: #9a6426; font-size: 1.2rem; }
.stat-label { font-size: 0.78rem; color: #8a8379; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
.stat-number { font-size: 1.9rem; font-weight: 700; color: #4e493f; line-height: 1; margin-bottom: 6px; }
.stat-description { font-size: 0.75rem; color: #9b948a; }
.quick-section { background-color: #ffffff; border: 1px solid #ddd5c8; border-radius: 14px; padding: 25px; box-shadow: 0 4px 14px rgba(70,55,35,0.06); margin-bottom: 25px; }
.quick-actions { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
.quick-action { background-color: #f7f4ee; border: 1px solid #e1d8c9; border-radius: 11px; padding: 17px; text-decoration: none; display: flex; align-items: center; gap: 13px; color: #5b554c; transition: all 0.2s ease; }
.quick-action:hover { background-color: #eee4d3; border-color: #cdbb9e; color: #70471f; transform: translateY(-2px); }
.quick-icon { width: 38px; height: 38px; border-radius: 9px; background-color: #e9ddca; color: #9a6426; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
.quick-title { font-size: 0.85rem; font-weight: 600; margin-bottom: 2px; }
.quick-description { font-size: 0.72rem; color: #91897e; }
.system-status { background-color: #ffffff; border: 1px solid #ddd5c8; border-radius: 14px; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 14px rgba(70,55,35,0.05); }
.status-left { display: flex; align-items: center; gap: 12px; }
.status-icon { width: 36px; height: 36px; border-radius: 50%; background-color: #e5efe4; color: #54734d; display: flex; align-items: center; justify-content: center; }
.status-title { font-size: 0.82rem; font-weight: 600; color: #575149; }
.status-description { font-size: 0.7rem; color: #969087; }
.status-badge { background-color: #e5efe4; color: #54734d; padding: 6px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; }
@media (max-width: 1000px) { .stats { grid-template-columns: repeat(2, 1fr); } .quick-actions { grid-template-columns: 1fr; } }
@media (max-width: 600px) { .dashboard { padding: 18px; } .welcome { padding: 22px; } .welcome-title { font-size: 1.35rem; } .welcome-icon { width: 60px; height: 60px; font-size: 1.8rem; } .stats { grid-template-columns: 1fr; } }
</style>
</head>

<body>

<div class="dashboard">

    <!-- Tarjeta de Bienvenida con control de Sesión y Rol -->
    <div class="welcome">
        <div class="welcome-text">
            <div class="welcome-title">
                Bienvenido, <?php echo ucfirst($usuario_actual); ?> (Rol: <?php echo ucfirst($rol_actual); ?>)
            </div>
            <p class="welcome-description">
                Sistema de Control y Registro de Servicio Social Estudiantil del Instituto Nacional "Albert Camus" (INAC). 
                Gestiona las bitácoras, estudiantes y actividades de manera rápida y organizada.
            </p>
        </div>
        <div class="welcome-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
            <a href="login23.php" class="btn btn-outline-danger btn-sm fw-bold">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </div>

    <div class="section-title">
        Resumen general del servicio social
    </div>

    <!-- Estadísticas del Sistema -->
    <div class="stats">
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-label">Estudiantes</div>
            </div>
            <div class="stat-number"><?php echo $totalEstudiantes; ?></div>
            <div class="stat-description">Registrados en el sistema</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </div>
                <div class="stat-label">Secciones</div>
            </div>
            <div class="stat-number"><?php echo $totalSecciones; ?></div>
            <div class="stat-description">Grados y secciones habilitados</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="stat-label">Encargados</div>
            </div>
            <div class="stat-number"><?php echo $totalEncargados; ?></div>
            <div class="stat-description">Supervisores registrados</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-clipboard-check-fill"></i>
                </div>
                <div class="stat-label">Actividades</div>
            </div>
            <div class="stat-number"><?php echo $totalActividades; ?></div>
            <div class="stat-description">Tipos de servicio habilitados</div>
        </div>
    </div>

    <!-- Acciones Rápidas adaptadas estrictamente a los Roles -->
    <div class="quick-section">
        <div class="section-title">
            Acciones disponibles (<?php echo ucfirst($rol_actual); ?>)
        </div>

        <div class="quick-actions">
            <?php if ($rol_actual == 'administrador'): ?>
                <!-- ACCIONES PARA EL ADMINISTRADOR / PROFESOR -->
                <a href="horassociales.php" class="quick-action">
                    <div class="quick-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                    <div>
                        <div class="quick-title">Agregar Horas Sociales</div>
                        <div class="quick-description">Registrar nueva bitácora (horassociales.php)</div>
                    </div>
                </a>

                <a href="consultar_horas.php" class="quick-action">
                    <div class="quick-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <div>
                        <div class="quick-title">Consultar Horas Sociales</div>
                        <div class="quick-description">Revisar registros de alumnos</div>
                    </div>
                </a>

            <?php else: ?>
                <!-- ACCIONES PARA EL LECTOR / ESTUDIANTE (Solo consulta, sin edición) -->
                <a href="consultar_horas.php" class="quick-action">
                    <div class="quick-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <div>
                        <div class="quick-title">Consultar Horas Sociales</div>
                        <div class="quick-description">Ver el estado de sus horas registradas</div>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Estado del Sistema -->
    <div class="system-status">
        <div class="status-left">
            <div class="status-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="status-title">Instituto Nacional "Albert Camus" - INAC</div>
                <div class="status-description">Sesión activa como: <b><?php echo ucfirst($rol_actual); ?></b> (Permisos restringidos)</div>
            </div>
        </div>
        <div class="status-badge">
            <i class="bi bi-circle-fill me-1" style="font-size:6px;"></i> Operativo
        </div>
    </div>

</div>

</body>
</html>
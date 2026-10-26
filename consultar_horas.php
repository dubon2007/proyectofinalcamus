<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login23.php");
    exit();
}
include 'conexionfinal.php';

$rol_actual = $_SESSION['rol'];$seccion_seleccionada = isset($_GET['seccion']) ? trim($_GET['seccion']) : "";
$estudiante_seleccionado = isset($_GET['estudiante']) ? trim($_GET['estudiante']) : "";

$resultado = null;
$total_horas = 0;

// Si ya eligió un estudiante, buscamos sus registros de horas sociales
if (!empty($estudiante_seleccionado)) {
    $est_esc =$conn->real_escape_string($estudiante_seleccionado);$sql = "SELECT * FROM bitacora_servicio_social WHERE nombre_estudiante = '$est_esc' ORDER BY id DESC";
    $resultado = $conn->query($sql);
    
    if ($resultado &&$resultado->num_rows > 0) {
        // Clonamos el resultado o recorremos para sumar las horas
        // Guardamos los registros en un array para mostrarlos en la tabla
        $registros = [];
        while ($row =$resultado->fetch_assoc()) {
            $total_horas += floatval($row['horas']);
            $registros[] =$row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Horas Sociales - INAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 rounded shadow">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2><i class="bi bi-search"></i> Consulta de Horas Sociales por Alumno</h2>
            <a href="home.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver al Inicio</a>
        </div>

        <!-- Filtros de Búsqueda Dinámicos (Sección -> Estudiante) -->
        <form method="GET" id="form_consulta" class="row g-3 mb-4 bg-light p-3 rounded border">
            <div class="col-md-5">
                <label class="form-label fw-bold text-xs uppercase">1. Seleccione la Sección:</label>
                <select name="seccion" id="select_seccion" class="form-select" required>
                    <option value="">Seleccione sección</option>
                    <?php 
                    $secciones = ['1-1', '1-2', '1-3', '1-4', '1-5', '1-6', '1-7', '1-8', '1-9', '1-10', '1-11', '2-1', '2-2', '2-3', '2-4', '2-5', '2-6', '2-7', '2-8', '2-9'];
                    foreach ($secciones as $sec) {$selected = ($seccion_seleccionada ==$sec) ? 'selected' : '';
                        echo "<option value=\"$sec\" $selected>$sec</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-5">
                <label class="form-label fw-bold text-xs uppercase">2. Seleccione el Estudiante:</label>
                <select name="estudiante" id="select_estudiante" class="form-select" required>
                    <option value="">Primero seleccione una sección</option>
                    <?php 
                    // Si ya venía una sección seleccionada por GET, cargamos los estudiantes para mantener el estado
                    if (!empty($seccion_seleccionada)) {
                        $tabla_est = "estudiantes_" . str_replace('-', '_', $seccion_seleccionada);
                        $res_est =$conn->query("SELECT nombre_estudiante FROM `$tabla_est` ORDER BY nombre_estudiante ASC");
                        if ($res_est) {
                            while ($e = $res_est->fetch_assoc()) {$sel = ($estudiante_seleccionado ==$e['nombre_estudiante']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($e['nombre_estudiante']) . '" ' . $sel . '>' . htmlspecialchars($e['nombre_estudiante']) . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Consultar</button>
            </div>
        </form>

        <?php if (!empty($estudiante_seleccionado)): ?>
            <!-- Tarjeta de Resumen de Horas Acumuladas -->
            <div class="alert alert-success d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-0">Estudiante: <b><?php echo htmlspecialchars($estudiante_seleccionado); ?></b></h5>
                    <small class="text-muted">Sección: <?php echo htmlspecialchars($seccion_seleccionada); ?></small>
                </div>
                <div class="text-end">
                    <span class="fs-4 fw-bold">Total Horas: <span class="badge bg-dark"><?php echo number_format($total_horas, 1); ?> hrs</span></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabla de resultados -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Proyecto</th>
                        <th>Fecha</th>
                        <th>Actividad</th>
                        <th>Horas</th>
                        <th>Encargado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($estudiante_seleccionado)): ?>
                        <?php if (!empty($registros)): ?>
                            <?php foreach ($registros as$row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nombre_proyecto']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                    <td><?php echo htmlspecialchars($row['actividades']); ?></td>
                                    <td><b><?php echo htmlspecialchars($row['horas']); ?></b></td>
                                    <td><?php echo htmlspecialchars($row['encargado']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-danger py-4 fw-bold">
                                    <i class="bi bi-exclamation-circle"></i> Este estudiante no tiene horas sociales registradas. Total: 0 horas.
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-muted py-4">Seleccione una sección y un estudiante para visualizar sus registros.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-3 text-center" role="alert">
            <small><i class="bi bi-info-circle-fill"></i> Modo de consulta activo (<?php echo ucfirst($rol_actual); ?>): Visualización protegida sin permisos de modificación directa.</small>
        </div>
    </div>

    <!-- Script de AJAX para actualizar el selector de estudiantes al cambiar la sección -->
    <script>
        document.getElementById('select_seccion').addEventListener('change', function() {
            let seccionSeleccionada = this.value;
            let selectEstudiante = document.getElementById('select_estudiante');

            if (seccionSeleccionada === "") {
                selectEstudiante.innerHTML = '<option value="">Primero seleccione una sección</option>';
                return;
            }

            selectEstudiante.innerHTML = '<option value="">Cargando estudiantes...</option>';

            let formData = new FormData();
            formData.append('seccion', seccionSeleccionada);

            fetch('obtener_estudiantes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                selectEstudiante.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                selectEstudiante.innerHTML = '<option value="">Error al cargar estudiantes</option>';
            });
        });
    </script>
</body>
</html>
<?php
// Incluir conexión
include 'conexionfinal.php';

if (isset($_POST['seccion'])) {
    $seccion = trim($_POST['seccion']);
    
    // Transformar el formato (ej. "1-5" se convierte en "estudiantes_1_5")
    $tabla_estudiantes = "estudiantes_" . str_replace('-', '_', $seccion);
    
    // Validar de manera segura que la tabla exista o hacer la consulta directamente
    $sql = "SELECT nombre_estudiante FROM `$tabla_estudiantes` ORDER BY nombre_estudiante ASC";
    $resultado = $conn->query($sql);

    echo '<option value="">Seleccione un estudiante</option>';
    
    if ($resultado &&$resultado->num_rows > 0) {
        while ($row =$resultado->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['nombre_estudiante']) . '">' . htmlspecialchars($row['nombre_estudiante']) . '</option>';
        }
    } else {
        echo '<option value="">No hay estudiantes en esta sección</option>';
    }
}
?>
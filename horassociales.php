<?php
// Incluir el archivo de conexión a la base de datos
include 'conexionfinal.php';

$mensaje = "";

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_estudiante = $conn->real_escape_string($_POST['nombre_estudiante']);
    $seccion = $conn->real_escape_string($_POST['seccion']);
    $nombre_proyecto = $conn->real_escape_string($_POST['nombre_proyecto']);

    // Recoger los datos de la tabla dinámica (filas)
    $fechas = $_POST['fecha'];
    $encargados = $_POST['encargado'];
    $firma_encargados = $_POST['firma_encargado'];
    $actividades = $_POST['actividades'];
    $horas = $_POST['horas'];
    $hora_entradas = $_POST['hora_entrada'];
    $hora_salidas = $_POST['hora_salida'];
    $firma_alumnos = $_POST['firma_alumno'];

    $exito = true;

    // Iterar sobre cada fila enviada
    for ($i = 0; $i < count($fechas); $i++) {
        // Solo guardar si al menos la fecha o la actividad tienen contenido
        if (!empty($fechas[$i]) || !empty($actividades[$i])) {
            $f = $conn->real_escape_string($fechas[$i]);
            $enc = $conn->real_escape_string($encargados[$i]);
            $f_enc = $conn->real_escape_string($firma_encargados[$i]);
            $act = $conn->real_escape_string($actividades[$i]);
            $hrs = !empty($horas[$i]) ? floatval($horas[$i]) : 0;
            $h_ent = !empty($hora_entradas[$i]) ? $conn->real_escape_string($hora_entradas[$i]) : NULL;
            $h_sal = !empty($hora_salidas[$i]) ? $conn->real_escape_string($hora_salidas[$i]) : NULL;
            $f_alu = $conn->real_escape_string($firma_alumnos[$i]);

            // Construir la consulta SQL para insertar cada fila
            if ($h_ent && $h_sal) {
                $sql = "INSERT INTO bitacora_servicio_social (nombre_estudiante, seccion, nombre_proyecto, fecha, encargado, firma_encargado, actividades, horas, hora_entrada, hora_salida, firma_alumno) 
                        VALUES ('$nombre_estudiante', '$seccion', '$nombre_proyecto', '$f', '$enc', '$f_enc', '$act', $hrs, '$h_ent', '$h_sal', '$f_alu')";
            } else {
                $sql = "INSERT INTO bitacora_servicio_social (nombre_estudiante, seccion, nombre_proyecto, fecha, encargado, firma_encargado, actividades, horas, firma_alumno) 
                        VALUES ('$nombre_estudiante', '$seccion', '$nombre_proyecto', '$f', '$enc', '$f_enc', '$act', $hrs, '$f_alu')";
            }

            if (!$conn->query($sql)) {
                $exito = false;
            }
        }
    }

    if ($exito) {
        $mensaje = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-center'>¡Bitácora guardada exitosamente en la base de datos!</div>";
    } else {
        $mensaje = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-center'>Hubo un error al guardar algunos registros: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Registro de Servicio Social Estudiantil (2026)</title>
    <!-- Tailwind CSS para un diseño limpio y profesional -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .max-w-6xl { max-width: 100% !important; border: none !important; box-shadow: none !important; padding: 0 !important; }
            input, select { border: none !important; background: transparent !important; appearance: none; -webkit-appearance: none; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-2 sm:px-6">

    <!-- Botones Flotantes Superiores -->
    <div class="fixed top-4 right-4 z-50 no-print flex gap-2">
        <button onclick="window.print()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-semibold transition">
            🖨️ Imprimir / Guardar PDF
        </button>
        <button onclick="toggleImage()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-semibold transition">
            📸 Ver / Ocultar Imagen Original
        </button>
    </div>

    <!-- Contenedor de la Imagen Flotante para Comparación -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4 no-print">
        <div class="bg-white rounded-lg max-w-4xl w-full p-4 relative max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-bold text-gray-800">Imagen de Referencia (Original)</h3>
                <button onclick="toggleImage()" class="text-gray-500 hover:text-gray-800 font-bold text-xl">&times;</button>
            </div>
            <img src="camus 34.png" alt="Bitácora Original" class="w-full h-auto rounded border">
        </div>
    </div>

    <div class="max-w-6xl mx-auto bg-white border-2 border-black p-4 sm:p-8 shadow-2xl">
        
        <!-- Mensajes de Estado -->
        <?php echo $mensaje; ?>

        <!-- Encabezado Institucional -->
        <div class="text-center border-b-2 border-black pb-4 mb-4 relative">
            <div class="absolute right-2 top-0 border-2 border-dashed border-gray-600 rounded-full w-20 h-20 flex items-center justify-center text-[10px] text-center font-bold p-1 uppercase text-gray-600 hidden sm:flex">
                Sello INAC
            </div>
            <h2 class="text-sm sm:text-base font-bold tracking-wide">INSTITUTO NACIONAL "ALBERT CAMUS"</h2>
            <h3 class="text-sm sm:text-base font-bold tracking-wide">INAC</h3>
            <h4 class="text-xs sm:text-sm font-semibold mt-1">SERVICIO SOCIAL ESTUDIANTIL</h4>
            <h1 class="text-sm sm:text-lg font-extrabold mt-2 underline">BITÁCORA DE REGISTRO DE SERVICIO SOCIAL ESTUDIANTIL (2026)</h1>
        </div>

        <!-- Formulario Principal -->
        <form action="" method="POST">
            
            <!-- Datos Generales -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 mb-4 border-b-2 border-black pb-4">
                
                <!-- SECCIÓN -->
                <div class="md:col-span-3 flex items-center gap-2 border border-black p-2 bg-gray-50">
                    <label class="font-bold text-xs sm:text-sm">SECCIÓN:</label>
                    <select name="seccion" id="select_seccion" required class="w-full bg-transparent border-b border-gray-400 focus:outline-none px-1 text-sm uppercase">
                        <option value="">Seleccione</option>
                        <?php 
                        $secciones = ['1-1', '1-2', '1-3', '1-4', '1-5', '1-6', '1-7', '1-8', '1-9', '1-10', '1-11', '2-1', '2-2', '2-3', '2-4', '2-5', '2-6', '2-7', '2-8', '2-9'];
                        foreach ($secciones as $sec) {
                            echo "<option value=\"$sec\">$sec</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- NOMBRE DEL ESTUDIANTE -->
                <div class="md:col-span-9 flex items-center gap-2 border border-black p-2 bg-gray-50">
                    <label class="font-bold text-xs sm:text-sm whitespace-nowrap">NOMBRE DEL ESTUDIANTE:</label>
                    <select name="nombre_estudiante" id="select_estudiante" required class="w-full bg-transparent border-b border-gray-400 focus:outline-none px-1 text-sm uppercase">
                        <option value="">Primero seleccione una sección</option>
                    </select>
                </div>

                <div class="col-span-12 flex items-center gap-2 border border-black p-2 bg-gray-50">
                    <label class="font-bold text-xs sm:text-sm whitespace-nowrap">NOMBRE DEL PROYECTO:</label>
                    <input type="text" name="nombre_proyecto" required class="w-full bg-transparent border-b border-gray-400 focus:outline-none px-1 text-sm uppercase" placeholder="Ej. Apoyo administrativo escolar">
                </div>
            </div>

            <!-- Tabla de Registros Diarios -->
            <div class="overflow-x-auto mb-6">
                <table class="w-full border-collapse border border-black text-center text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-gray-200 border-b border-black">
                            <th class="border border-black p-1 sm:p-2 w-28">FECHA</th>
                            <th class="border border-black p-1 sm:p-2">ENCARGADO</th>
                            <th class="border border-black p-1 sm:p-2 w-28">FIRMA DEL ENCARGADO</th>
                            <th class="border border-black p-1 sm:p-2">ACTIVIDADES</th>
                            <th class="border border-black p-1 sm:p-2 w-16">HORAS</th>
                            <th class="border border-black p-1 sm:p-2 w-24">HORA DE ENTRADA</th>
                            <th class="border border-black p-1 sm:p-2 w-24">HORA DE SALIDA</th>
                            <th class="border border-black p-1 sm:p-2 w-28">FIRMA DEL ALUMNO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Consultar opciones de encargados desde la BD
                        $res_encargados = $conn->query("SELECT * FROM encargados");
                        $opciones_encargados = '<option value="">Seleccione</option>';
                        while ($enc = $res_encargados->fetch_assoc()) {
                            $opciones_encargados .= '<option value="' . htmlspecialchars($enc['nombre_encargado']) . '">' . htmlspecialchars($enc['nombre_encargado']) . '</option>';
                        }

                        // Consultar opciones de actividades desde la BD
                        $res_actividades = $conn->query("SELECT * FROM actividades");
                        $opciones_actividades = '<option value="">Seleccione</option>';
                        while ($act = $res_actividades->fetch_assoc()) {
                            $opciones_actividades .= '<option value="' . htmlspecialchars($act['nombre_actividad']) . '">' . htmlspecialchars($act['nombre_actividad']) . '</option>';
                        }

                        // Generamos 7 filas vacías como en la imagen de ejemplo
                        for ($i = 0; $i < 7; $i++): 
                        ?>
                        <tr class="h-12 border-b border-black">
                            <td class="border border-black p-1">
                                <input type="text" name="fecha[]" class="w-full text-center bg-transparent focus:outline-none text-xs" placeholder="DD/MM">
                            </td>
                            <td class="border border-black p-1">
                                <select name="encargado[]" class="w-full text-center bg-transparent focus:outline-none text-xs">
                                    <?php echo $opciones_encargados; ?>
                                </select>
                            </td>
                            <td class="border border-black p-1">
                                <input type="text" name="firma_encargado[]" class="w-full text-center bg-transparent focus:outline-none text-xs" placeholder="[Firma]">
                            </td>
                            <td class="border border-black p-1">
                                <select name="actividades[]" class="w-full text-center bg-transparent focus:outline-none text-xs">
                                    <?php echo $opciones_actividades; ?>
                                </select>
                            </td>
                            <td class="border border-black p-1">
                                <input type="number" step="0.5" name="horas[]" class="w-full text-center bg-transparent focus:outline-none text-xs">
                            </td>
                            <td class="border border-black p-1">
                                <input type="time" name="hora_entrada[]" class="w-full text-center bg-transparent focus:outline-none text-[11px]">
                            </td>
                            <td class="border border-black p-1">
                                <input type="time" name="hora_salida[]" class="w-full text-center bg-transparent focus:outline-none text-[11px]">
                            </td>
                            <td class="border border-black p-1">
                                <input type="text" name="firma_alumno[]" class="w-full text-center bg-transparent focus:outline-none text-xs" placeholder="[Firma]">
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pie de página con Coordinación -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pt-2 border-t border-black text-xs sm:text-sm mb-6">
                <div>
                    <p class="font-bold">Profa. Mirma Xiomara Jiménez Lazo.</p>
                    <p class="text-gray-700">Coordinadora del Servicio Social.</p>
                </div>
                <div class="mt-4 sm:mt-0 no-print flex gap-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg shadow transition">
                        💾 Guardar Bitácora en Base de Datos
                    </button>
                    <button type="button" onclick="window.print()" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-6 rounded-lg shadow transition">
                        🖨️ Imprimir / Guardar PDF
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleImage() {
            const modal = document.getElementById('imageModal');
            modal.classList.toggle('hidden');
        }

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
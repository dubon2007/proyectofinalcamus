<?php
session_start();
include 'conexionfinal.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $password = md5($_POST['password']); // Encriptación básica para matchear con la tabla

    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND password = '$password'";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows == 1) {
        $user = $resultado->fetch_assoc();
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol'] = $user['rol'];

        // Redirigir al home adaptado
        header("Location: home.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Servicio Social INAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full border border-gray-200">
        
        <!-- Imagen del Escudo Institucional Arriba -->
        <div class="flex flex-col items-center mb-6">
            <img src="camus 34.png" alt="Escudo INAC" class="w-28 h-28 object-contain mb-3 drop-shadow-md">
            <h1 class="text-lg font-bold text-gray-800 text-center">INSTITUTO NACIONAL "ALBERT CAMUS"</h1>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Servicio Social Estudiantil</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-xs text-center font-semibold">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de Login -->
        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Usuario:</label>
                <input type="text" name="usuario" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Ej. estudiante o profesor">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Contraseña:</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg shadow transition text-sm">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-6 text-center border-t pt-4 text-[11px] text-gray-500">
            <p class="font-semibold text-gray-600">Credenciales de prueba:</p>
            <p>• Estudiante (Lector): <b>estudiante</b> / <b>estudiante</b></p>
            <p>• Profesor (Administrador): <b>profesor</b> / <b>profesor</b></p>
        </div>
    </div>

</body>
</html>
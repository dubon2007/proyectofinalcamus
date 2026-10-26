<?php
// Datos de conexión proporcionados por tu panel de control de InfinityFree
$servername = "sql213.infinityfree.com"; // Reemplaza con tu Host MySQL (ej. sql100.epizy.com)
$username = "if0_42365350";               // Reemplaza con tu Usuario MySQL
$password = "e28NAw2PcfZlyIi";       // Reemplaza con tu Contraseña
$dbname = "if0_42365350_proyectofinal";       // Reemplaza con el Nombre de tu Base de Datos

// Crear conexión utilizando mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si hay errores de conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres a UTF-8 para evitar problemas con tildes y eñes
$conn->set_charset("utf8");

// Nota: Puedes incluir este archivo en tus otros scripts usando: include 'conexion.php';
?>
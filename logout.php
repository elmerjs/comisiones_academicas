<?php
ob_start(); // Limpia cualquier salida previa (por si acaso)
session_start();
session_unset();
session_destroy();
header('Location: index.html');
exit;
?>
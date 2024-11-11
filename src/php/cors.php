<?php
// Permitir el acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permitir los métodos POST y GET desde cualquier origen
header("Access-Control-Allow-Methods: POST, GET");
// Permitir que los encabezados Content-Type y Authorization se incluyan en las solicitudes
header("Access-Control-Allow-Headers: Content-Type, Authorization");


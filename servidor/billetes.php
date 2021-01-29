<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *'); 

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		require 'verVuelos.php';
		mostrarVuelos();
		break;
	case 'POST':
		require 'comprarVuelo.php';
		comprarVuelo();
		break;
	default:
		echo "No disponible";
		break;
}
?>
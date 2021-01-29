<?php
function comprarVuelo()
{

    require 'vendor/autoload.php'; // incluir lo bueno de Composer
    require 'conexion.php';

$arrEsperado = array(
    "codigo" => "IB706",
    "dni" => "44556677H",
    "apellido" => "Rodriguez",
    "nombre" => "María",
    "dniPagador" => "44556677H",
    "tarjeta" => "038 0025 5553 5553"
);

function JSONCorrectoAnnadir($recibido)
{

    $aux = false;

    if (isset($recibido["codigo"]) && isset($recibido["dni"]) && isset($recibido["apellido"]) && isset($recibido["nombre"]) && isset($recibido["dniPagador"]) && isset($recibido["tarjeta"]))
    {
        $aux = true;
    }

    return $aux;
    
}

$arrMensaje = array();

$parameters = file_get_contents("php://input");

if (isset($parameters))
{

    $mensajeRecibido = json_decode($parameters, true);

    if (JSONCorrectoAnnadir($mensajeRecibido))
    {

        $codigo = $mensajeRecibido["codigo"];
        $dni = $mensajeRecibido["dni"];
        $apellido = $mensajeRecibido["apellido"];
        $nombre = $mensajeRecibido["nombre"];
        $dniPagador = $mensajeRecibido["dniPagador"];
        $tarjeta = $mensajeRecibido["tarjeta"];


        $permitted_chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $codigoVenta = strtoupper(substr(str_shuffle($permitted_chars) , 0, 9));

        $plazaOcupada = array('$inc' => array(
                    'plazas_disponibles' => -1
        ));

        $result = $coleccion->updateOne(array(
            "codigo" => $codigo
        ) , $plazaOcupada);

        $asiento = random_int(1, 199);

        $billete = array('$push' => array(
                    'vendidos' => array(
                    'asiento' => $asiento,
                    'dni' => $dni,
                    'apellido' => $apellido,
                    'nombre' => $nombre,
                    'dniPagador' => $dniPagador,
                    'tarjeta' => $tarjeta,
                    'codigoVenta' => $codigoVenta
                )
            )
        );
        $result = $coleccion->updateOne(array("codigo" => $codigo) , $billete);
        
        //-----------------------------------------

        if (isset ( $result ) && $result) { // Si pasa por este if, la query está está bien y se ha insertado correctamente
            $arrMensaje["estado"] = true;
            $arrMensaje["mensaje"] = "Se ha completado la compra";
        }
        else { // Se ha producido algún error al ejecutar la query
            $arrMensaje["estado"] = false;
            $arrMensaje["mensaje"] = "No se ha podido realizar la compra por error en la query";

        }

    }
    else { // Nos ha llegado un json no tiene los campos necesarios
        $arrMensaje["estado"] = false;
        $arrMensaje["mensaje"] = "No se ha podido realizar la compra por que los campos no tiene los datos correspondientes";
        $arrMensaje["recibido"] = $mensajeRecibido;
        $arrMensaje["esperado"] = $arrEsperado;
    }

}
else { // No nos han enviado el json correctamente
    $arrMensaje["estado"] = false;
    $arrMensaje["mensaje"] = "No se ha podido realizar la compra por error en los datos recibidos";

}

$mensajeJSON = json_encode($arrMensaje, JSON_PRETTY_PRINT);

//echo "<pre>";  // Descomentar si se quiere ver resultado "bonito" en navegador. Solo para pruebas
echo $mensajeJSON;
//echo "</pre>"; // Descomentar si se quiere
}

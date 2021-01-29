<?php
function borrarVuelo()
{
    require 'vendor/autoload.php'; // incluir lo bueno de Composer
    require 'conexion.php';


    $arrEsperado = array(
        "codigo" => "IB706",
        "dni" => "44556677H",
        "codigoVenta" => "GHJ7766GG"
    );

    function JSONCorrectoAnnadir($recibido)
    {

        $aux = false;

        if (isset($recibido["codigo"]) && isset($recibido["dni"]) && isset($recibido["codigoVenta"])) {
            $aux = true;
        }

        return $aux;
    }

    $arrMensaje = array(); 

    $parameters = file_get_contents("php://input");

    if (isset($parameters)) {

        $mensajeRecibido = json_decode($parameters, true);

        if (JSONCorrectoAnnadir($mensajeRecibido)) {

            $codigo = $mensajeRecibido["codigo"];
            $dni = $mensajeRecibido["dni"];
            $codigoVenta = $mensajeRecibido["codigoVenta"];

            
            $plazaOcupada = array('$inc' => array(
                'plazas_disponibles' => +1
            ));
            
            $result = $coleccion->updateOne(array(
                "codigo" => $codigo
            ), $plazaOcupada);

            $plazaOcupada = array( '$pull' => array(
                        'vendidos' => array(
                        'dni' => $dni,
                        'codigoVenta' => $codigoVenta
                    )
                )
            );
            $result = $coleccion->updateOne(array("codigo" => $codigo) , $plazaOcupada);

         //-----------------------------------------

        if (isset ( $result ) && $result) { // Si pasa por este if, la query está está bien y se ha insertado correctamente
            $arrMensaje["estado"] = true;
            $arrMensaje["mensaje"] = "Se ha cancelado la compra";
        }
        else { // Se ha producido algún error al ejecutar la query
            $arrMensaje["estado"] = false;
            $arrMensaje["mensaje"] = "No se ha podido cancelar la compra por error en la query";

        }

    }
    else { // Nos ha llegado un json no tiene los campos necesarios
        $arrMensaje["estado"] = false;
        $arrMensaje["mensaje"] = "No se ha podido cancelar la compra por que los campos no tiene los datos correspondientes";
        $arrMensaje["recibido"] = $mensajeRecibido;
        $arrMensaje["esperado"] = $arrEsperado;
    }

}
else { // No nos han enviado el json correctamente
    $arrMensaje["estado"] = false;
    $arrMensaje["mensaje"] = "No se ha podido cancelar la compra por error en los datos recibidos";

}

$mensajeJSON = json_encode($arrMensaje, JSON_PRETTY_PRINT);

//echo "<pre>";  // Descomentar si se quiere ver resultado "bonito" en navegador. Solo para pruebas
echo $mensajeJSON;
//echo "</pre>"; // Descomentar si se quiere
}

?>
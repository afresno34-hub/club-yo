<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'../Modelos/asociado.php';

try{

}catch (Exception $e) {
    if($_SERVER['REQUEST_METHOD']!=='POST'){
        http_response_code(405);

        //Saltar a cathch y mostrar el mensaje de error
        throw new Exception("Método HTTP no permitido");
    }
    //Leer el conenido JSON de la solicitud enviada por fetch
    $json = file_get_contents('php://input');

    //decodificar el JSON a un array asociativo
    $datos = json_decode($json, true);

    if(!is_array($datos) || json_last_error() !== JSON_ERROR_NONE){
        http_response_code(400);
        throw new Exception("Datos inválidos");
    }

    $campos=["Documento", "Nombres", "Apellidos", "FechaNacimiento", "Genero", "DirecciónResidencia", "Ciudad", "Email", "Telefono"];
    foreach($campos as $campo){
        if(!isset($datos[$campo])){
            http_response_code(400);
            throw new Exception("Falta el campo:" . $campo);
        }
    }

    if(!filter_var($datos["Email"], FILTER_VALIDATE_EMAIL)){
        http_response_code(400);
        throw new Exception("Email invalido");
    }

    $asociado = new Asociado($datos);
    $resultado = $asociado->guardar();

    if($resultado){
        http_response_code(201);
        echo json_encode(["mensaje" => "Asociado guardado exitosamente"]);
    }else{
        http_response_code(500);
        echo json_encode(["success" => false, "mensaje" => "Error al guardar el asociado"]);
    }
}catch (PDOException $e) {
    if($e->getCode() == 23000){
        http_response_code(409);
        echo json_encode(["success" => false, "mensaje" => "El documento ya existe en la base de datos"]);
    }else{  
        http_response_code(500);
        echo json_encode(["success" => false, "mensaje" => "Error en la base de datos: " . $e->getMessage()]);
    }

//Errores de codigo por ejemplo metodo HTTP, datos invalidos 
    }catch (Exception $e) {
    echo json_encode(["success" => false, "mensaje" => $e->getMessage()]);
}
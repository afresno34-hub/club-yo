<?php
require_once __DIR__.'../Config/conexion.php';
class Asociado{
    private $documento;
    private $nombres;
    private $apellidos;
    private $fechaNacimiento;
    private $genero;
    private $direccionResidencia;
    private $ciudad;
    private $email;
    private $telefono;

    private $conexion;

    public function __construct($datos){
        $this->documento = $datos['documento'];
        $this->nombres = $datos['nombres'];
        $this->apellidos = $datos['apellidos'];
        $this->fechaNacimiento = $datos['fechaNacimiento'];
        $this->genero = $datos['genero'];
        $this->direccionResidencia = $datos['direccionResidencia'];
        $this->ciudad = $datos['ciudad'];
        $this->email = $datos['email'];
        $this->telefono = $datos['telefono'];

        $conexionDB = new Conexion();
        $this->conexion = $conexionDB->conectar();
    }

    public function guardar(){
    $sql = "INSERT INTO asociados (documento, nombres, apellidos, fechaNacimiento, genero, direccionResidencia, ciudad, email, telefono) VALUES (:documento, :nombres, :apellidos, :fechaNacimiento, :genero, :direccionResidencia, :ciudad, :email, :telefono)";
    $consulta = $this->conexion->prepare($sql);
    return $consulta->execute([
        ":documento" => $this->documento,
        ":nombres" => $this->nombres,
        ":apellidos" => $this->apellidos,
        ":fechaNacimiento" => $this->fechaNacimiento,
        ":genero" => $this->genero,
        ":direccionResidencia" => $this->direccionResidencia,
        ":ciudad" => $this->ciudad,
        ":email" => $this->email,
        ":telefono" => $this->telefono
    ]);
    }
}

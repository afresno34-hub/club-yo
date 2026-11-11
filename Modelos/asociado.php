<?php
require_once __DIR__ . '/../Config/conexion.php';

class Asociado {
    private $documento;
    private $nombres;
    private $apellidos;
    private $fechaNacimiento;
    private $genero;
    private $direccionResidencia;
    private $ciudad;
    private $email;
    private $telefono;
    private $contrasena;
    private $rol;
    private $conexion;

    public function __construct($datos) {
        $this->documento = $datos['documento'] ?? ($datos['Documento'] ?? '');
        $this->nombres = $datos['nombres'] ?? ($datos['nombre'] ?? ($datos['Nombres'] ?? ($datos['Nombre'] ?? '')));
        $this->apellidos = $datos['apellidos'] ?? ($datos['Apellidos'] ?? '');
        $this->fechaNacimiento = $datos['fechaNacimiento'] ?? ($datos['FechaNacimiento'] ?? '');
        $this->genero = $datos['genero'] ?? ($datos['Genero'] ?? 'Otro');
        $this->direccionResidencia = $datos['direccionResidencia'] ?? ($datos['direcciónResidencia'] ?? ($datos['DirecciónResidencia'] ?? ($datos['direccion'] ?? '')));
        $this->ciudad = $datos['ciudad'] ?? ($datos['Ciudad'] ?? '');
        $this->email = $datos['email'] ?? ($datos['Email'] ?? '');
        $this->telefono = $datos['telefono'] ?? ($datos['Telefono'] ?? '');

        // Hasheo seguro de contraseña con Bcrypt (PASSWORD_DEFAULT)
        $clavePlana = $datos['contrasena'] ?? ($datos['contraseña'] ?? ($datos['password'] ?? ($datos['Password'] ?? ($datos['clave'] ?? ''))));
        if (strpos($clavePlana, '$2y$') === 0 || strpos($clavePlana, '$2a$') === 0) {
            $this->contrasena = $clavePlana;
        } else {
            $this->contrasena = password_hash($clavePlana, PASSWORD_DEFAULT);
        }

        $this->rol = $datos['rol'] ?? ($datos['Rol'] ?? 'Asociado');

        $conexionDB = new Conexion();
        $this->conexion = $conexionDB->conectar();
    }

    public function guardar() {
        $params = [
            ":documento" => $this->documento,
            ":nombres" => $this->nombres,
            ":apellidos" => $this->apellidos,
            ":fechaNacimiento" => $this->fechaNacimiento,
            ":genero" => $this->genero,
            ":direccionResidencia" => $this->direccionResidencia,
            ":ciudad" => $this->ciudad,
            ":email" => $this->email,
            ":telefono" => $this->telefono,
            ":contrasena" => $this->contrasena,
            ":rol" => $this->rol
        ];

        try {
            $sql = "INSERT INTO asociados (documento, nombres, apellidos, fechaNacimiento, genero, direccionResidencia, ciudad, email, telefono, Contraseña, Rol) 
                    VALUES (:documento, :nombres, :apellidos, :fechaNacimiento, :genero, :direccionResidencia, :ciudad, :email, :telefono, :contrasena, :rol)";
            $consulta = $this->conexion->prepare($sql);
            return $consulta->execute($params);
        } catch (PDOException $e) {
            // Compatibilidad por si la columna en MySQL fue creada con tilde (`direcciónResidencia`)
            if (strpos($e->getMessage(), 'direccionResidencia') !== false) {
                $sql = "INSERT INTO asociados (documento, nombres, apellidos, fechaNacimiento, genero, `direcciónResidencia`, ciudad, email, telefono, Contraseña, Rol) 
                        VALUES (:documento, :nombres, :apellidos, :fechaNacimiento, :genero, :direccionResidencia, :ciudad, :email, :telefono, :contrasena, :rol)";
                $consulta = $this->conexion->prepare($sql);
                return $consulta->execute($params);
            }
            throw $e;
        }
    }
}

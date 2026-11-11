<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/asociado.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new Exception("Método HTTP no permitido. Solo se permite POST");
    }

    // Leer el contenido JSON o formulario POST
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);

    if (!is_array($datos) || json_last_error() !== JSON_ERROR_NONE) {
        if (!empty($_POST)) {
            $datos = $_POST;
        } else {
            http_response_code(400);
            throw new Exception("Datos inválidos en la solicitud");
        }
    }

    // Normalizar contraseña y validaciones
    $pass = $datos['contrasena'] ?? ($datos['contraseña'] ?? ($datos['password'] ?? ($datos['Password'] ?? ($datos['clave'] ?? ''))));
    $confirmPass = $datos['confirmarContrasena'] ?? ($datos['confirmarContraseña'] ?? ($datos['confirmPassword'] ?? null));

    if (trim((string)$pass) === '') {
        http_response_code(400);
        throw new Exception("Falta el campo: contraseña");
    }

    // Validar mínimo 8 caracteres
    if (strlen($pass) < 8) {
        http_response_code(400);
        throw new Exception("La contraseña debe tener mínimo 8 caracteres");
    }

    // Comparar contraseñas si se envió confirmación
    if ($confirmPass !== null && trim((string)$confirmPass) !== '' && $pass !== $confirmPass) {
        http_response_code(400);
        throw new Exception("Las contraseñas no coinciden");
    }

    $datos['contrasena'] = $pass;

    // Normalizar nombres de campos comunes
    $doc = $datos['documento'] ?? ($datos['Documento'] ?? '');
    $nom = $datos['nombres'] ?? ($datos['nombre'] ?? ($datos['Nombres'] ?? ($datos['Nombre'] ?? '')));
    $ape = $datos['apellidos'] ?? ($datos['Apellidos'] ?? '');
    $dir = $datos['direccionResidencia'] ?? ($datos['direcciónResidencia'] ?? ($datos['DirecciónResidencia'] ?? ($datos['direccion'] ?? '')));
    $ciu = $datos['ciudad'] ?? ($datos['Ciudad'] ?? '');
    $tel = $datos['telefono'] ?? ($datos['Telefono'] ?? '');
    $ema = $datos['email'] ?? ($datos['Email'] ?? '');
    $gen = $datos['genero'] ?? ($datos['Genero'] ?? '');
    $fec = $datos['fechaNacimiento'] ?? ($datos['FechaNacimiento'] ?? '');

    $datosNormalizados = [
        'documento' => trim((string)$doc),
        'nombres' => trim((string)$nom),
        'apellidos' => trim((string)$ape),
        'direccionResidencia' => trim((string)$dir),
        'ciudad' => trim((string)$ciu),
        'telefono' => trim((string)$tel),
        'email' => trim((string)$ema),
        'genero' => trim((string)$gen),
        'fechaNacimiento' => trim((string)$fec),
        'contrasena' => $pass,
        'rol' => $datos['rol'] ?? ($datos['Rol'] ?? 'Asociado')
    ];

    $camposObligatorios = [
        "documento" => "Documento",
        "nombres" => "Nombres",
        "apellidos" => "Apellidos",
        "direccionResidencia" => "Dirección de Residencia",
        "ciudad" => "Ciudad",
        "telefono" => "Teléfono",
        "email" => "Email",
        "genero" => "Género",
        "fechaNacimiento" => "Fecha de Nacimiento",
        "contrasena" => "Contraseña"
    ];

    foreach ($camposObligatorios as $clave => $etiqueta) {
        if ($datosNormalizados[$clave] === '') {
            http_response_code(400);
            throw new Exception("Falta el campo: " . $etiqueta);
        }
    }

    if (!filter_var($datosNormalizados['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        throw new Exception("Email inválido");
    }

    $asociado = new Asociado($datosNormalizados);
    $resultado = $asociado->guardar();

    if ($resultado) {
        http_response_code(201);
        echo json_encode(["success" => true, "mensaje" => "Asociado guardado exitosamente", "message" => "Asociado guardado exitosamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "mensaje" => "Error al guardar el asociado", "message" => "Error al guardar el asociado"]);
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(["success" => false, "mensaje" => "El documento ya existe en la base de datos", "message" => "El documento ya existe en la base de datos"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "mensaje" => "Error en la base de datos: " . $e->getMessage(), "message" => "Error en la base de datos: " . $e->getMessage()]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "mensaje" => $e->getMessage(), "message" => $e->getMessage()]);
}
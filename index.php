<?php
// --- Verificación del estado del WAF ---
$waf_activo = true; // Cambiar a false para simular WAF apagado

// Si el WAF está apagado, mostrar mensaje y detener ejecución
if (!$waf_activo) {
    die('<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WAF Desactivado</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .alert-box {
                background-color: #fff3cd;
                border: 1px solid #ffeeba;
                color: #856404;
                padding: 20px;
                border-radius: 5px;
                max-width: 500px;
                text-align: center;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #dc3545;
            }
        </style>
    </head>
    <body>
        <div class="alert-box">
            <h1>ADVERTENCIA DE SEGURIDAD</h1>
            <p>El Web Application Firewall (WAF) está actualmente DESACTIVADO.</p>
            <p><strong>EL WAF ESTA APAGADO</strong></p>
            <p>Por razones de seguridad, el acceso a la aplicación no está disponible mientras el WAF esté desactivado.</p>
        </div>
    </body>
    </html>');
}

// --- Conexión a SQL Server en Azure (solo se ejecuta si WAF está activo) ---
$serverName = "tcp:jucaserver.database.windows.net,1433";
$connectionInfo = array(
    "UID" => "jucavarh",
    "PWD" => "A38391-bmu8tv",
    "Database" => "formulario_app",
    "LoginTimeout" => 30,
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

// Verificar conexión
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Mostrar mensaje que WAF está activo
$mensaje_waf = '<div class="waf-status" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                 <strong>EL WAF ESTA PRENDIDO</strong> - Aplicación protegida
               </div>';

// --- Insertar datos si se envió el formulario ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];

    if (!empty($nombre) && !empty($correo)) {
        $sql = "INSERT INTO usuarios (nombre, correo) VALUES (?, ?)";
        $params = array($nombre, $correo);
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            $mensaje_exito = "¡Registro guardado exitosamente!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario PHP - Captura y Consulta</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Tus estilos existentes... */
        
        .waf-status {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            text-align: center;
            border-left: 5px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $mensaje_waf; ?>
        
        <div class="card">
            <h1>Formulario de Captura</h1>
            
            <?php if (isset($mensaje_exito)): ?>
                <div class="alert"><?php echo $mensaje_exito; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                
                <button type="submit" class="btn">Guardar Registro</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Consulta de Información</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM usuarios";
                    $result = sqlsrv_query($conn, $sql);
                    
                    if ($result === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                    
                    $hay_datos = false;
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $hay_datos = true;
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['nombre']}</td>
                                <td>{$row['correo']}</td>
                              </tr>";
                    }
                    
                    if (!$hay_datos) {
                        echo "<tr><td colspan='3' class='no-data'>No hay registros en la base de datos</td></tr>";
                    }
                    
                    sqlsrv_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

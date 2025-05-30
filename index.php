<?php
// --- Conexión a SQL Server en Azure ---
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

// --- Insertar datos si se envió el formulario ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesamiento del formulario principal
    if (isset($_POST['nombre']) && isset($_POST['correo'])) {
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
    
    // Procesamiento del test WAF
    if (isset($_POST['waf_test'])) {
        $waf_test_result = $_POST['waf_test'];
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
        
        /* Nuevos estilos para la sección WAF */
        .waf-test-section {
            margin: 30px auto;
            padding: 20px;
            background: #f0f0f0;
            border: 2px solid red;
            max-width: 500px;
            border-radius: var(--border-radius);
        }
        
        .waf-test-section h3 {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .waf-test-input {
            width: 95%;
            padding: 8px;
            margin-bottom: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        
        .waf-test-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .waf-test-btn:hover {
            background-color: #c9302c;
        }
        
        .waf-status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }
        
        .waf-off {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        
        .waf-on {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
    </style>
</head>
<body>
    <div class="container">
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
            
            <!-- Sección de prueba WAF -->
            <div class="waf-test-section">
                <h3>Prueba de WAF</h3>
                
                <?php if (isset($waf_test_result)): ?>
                    <div class="waf-status <?php echo (strpos($waf_test_result, 'APAGADO') !== false) ? 'waf-off' : 'waf-on'; ?>">
                        <?php echo htmlspecialchars($waf_test_result); ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <input
                        type="text"
                        name="waf_test"
                        class="waf-test-input"
                        placeholder='Prueba WAF: <script>alert("TEST")</script>'
                    >
                    <button type="submit" class="waf-test-btn">Probar WAF</button>
                </form>
                
                <div style="margin-top: 20px; text-align: center;">
                    <p>Para probar el WAF:</p>
                    <ol style="text-align: left; padding-left: 20px;">
                        <li>Con WAF <strong>APAGADO</strong>, ingresa: 
                            <code>&lt;script&gt;alert("EL WAF ESTA APAGADO")&lt;/script&gt;</code>
                        </li>
                        <li>Con WAF <strong>PRENDIDO</strong>, ingresa: 
                            <code>&lt;script&gt;alert("EL WAF ESTA PRENDIDO")&lt;/script&gt;</code>
                        </li>
                    </ol>
                </div>
            </div>
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
    
    <!-- Mostrar alerta si se envió un script (solo cuando WAF está desactivado) -->
    <?php if (isset($waf_test_result) && strpos($waf_test_result, '<script>') !== false): ?>
        <script>
            try {
                <?php echo $waf_test_result; ?>
            } catch(e) {
                console.log("WAF está bloqueando la ejecución del script");
            }
        </script>
    <?php endif; ?>
</body>
</html>

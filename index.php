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
    // Sanitizar entradas
    $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
    
    // Validar correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("El correo electrónico no es válido");
    }

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
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
            --error-color: #ef233c;
            --bg-gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --border-radius: 12px;
            --box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            color: var(--dark-color);
        }

        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            margin-bottom: 30px;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        h1, h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        h1::after, h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(0, 0, 0, 0.1);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            background-color: rgba(76, 201, 240, 0.2);
            border-left: 5px solid var(--success-color);
            color: var(--dark-color);
            display: <?php echo isset($mensaje_exito) ? 'block' : 'none'; ?>;
            animation: fadeIn 0.5s ease;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            position: sticky;
            top: 0;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card {
                padding: 20px;
            }
            
            th, td {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Formulario de Captura</h1>
            
            <?php if (isset($mensaje_exito)): ?>
                <div class="alert"><?php echo htmlspecialchars($mensaje_exito, ENT_QUOTES, 'UTF-8'); ?></div>
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
                                <td>".htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8')."</td>
                                <td>".htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8')."</td>
                                <td>".htmlspecialchars($row['correo'], ENT_QUOTES, 'UTF-8')."</td>
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

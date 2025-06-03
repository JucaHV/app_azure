<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario PHP - Captura y Consulta</title>
    <style>
    :root {
        --primary-color: #000000;
        --secondary-color: #0033cc;
        --light-color: #f0f0f0;
        --dark-color: #000000;
        --success-color: #28a745;
        --error-color: #dc3545;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 40px 20px;
        min-height: 100vh;
    }

    .form-container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        padding: 40px;
        width: 100%;
        max-width: 600px;
        margin-bottom: 40px;
    }

    h1, h2 {
        color: var(--primary-color);
        margin-bottom: 30px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: var(--dark-color);
        font-weight: 600;
    }

    input {
        width: 100%;
        padding: 12px;
        border: 2px solid #ced4da;
        border-radius: 6px;
        font-size: 16px;
    }

    input:focus {
        border-color: var(--secondary-color);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 51, 204, 0.2);
    }

    .btn-submit {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 14px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
        transition: background 0.3s ease;
    }

    .btn-submit:hover {
        background-color: var(--secondary-color);
    }

    .response {
        margin-top: 30px;
        padding: 20px;
        border-radius: 6px;
        background-color: #e0f7f5;
        border-left: 4px solid var(--success-color);
        display: none;
    }

    .error {
        border-left-color: var(--error-color) !important;
        background-color: #fdecea !important;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    table {
        border-collapse: collapse;
        width: auto;
        min-width: 800px;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        padding: 16px 24px;
        text-align: left;
        border-bottom: 1px solid #eee;
        white-space: nowrap;
    }

    th {
        background-color: var(--primary-color);
        color: white;
    }

    tr:hover {
        background-color: #f1f1f1;
    }
</style>
</head>
<body>
    <!-- CONTENEDOR DEL FORMULARIO -->
    <div class="form-container">
        <h1>Formulario de Captura</h1>
        
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
                echo '<div class="response error">';
                echo '<h3>Error:</h3>';
                echo '<p>El correo electrónico no es válido</p>';
                echo '</div>';
                echo '<script>document.querySelector(".response").style.display = "block";</script>';
            } else if (!empty($nombre) && !empty($correo)) {
                $sql = "INSERT INTO usuarios (nombre, correo) VALUES (?, ?)";
                $params = array($nombre, $correo);
                $stmt = sqlsrv_query($conn, $sql, $params);

                if ($stmt === false) {
                    echo '<div class="response error">';
                    echo '<h3>Error al guardar los datos:</h3>';
                    echo '<p>'.htmlspecialchars(print_r(sqlsrv_errors(), true), ENT_QUOTES, 'UTF-8').'</p>';
                    echo '</div>';
                    echo '<script>document.querySelector(".response").style.display = "block";</script>';
                } else {
                    echo '<div class="response">';
                    echo '<h3>Datos guardados correctamente:</h3>';
                    echo '<p><strong>Nombre:</strong> '.htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8').'</p>';
                    echo '<p><strong>Correo:</strong> '.htmlspecialchars($correo, ENT_QUOTES, 'UTF-8').'</p>';
                    echo '</div>';
                    echo '<script>document.querySelector(".response").style.display = "block";</script>';
                }
            }
        }
        ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <button type="submit" class="btn-submit">Enviar Datos</button>
        </form>
    </div> <!-- FIN DEL FORMULARIO -->

    <!-- NUEVO CONTENEDOR DE LA TABLA -->
    <div class="table-container">
        <div style="padding: 30px; background-color: white; border-radius: 10px;">
            <h2 style="text-align:center; color: var(--primary-color); margin-bottom: 20px;">Usuarios Registrados</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                </tr>
                <?php
                $sql = "SELECT * FROM usuarios ORDER BY id DESC";
                $result = sqlsrv_query($conn, $sql);
                
                if ($result === false) {
                    echo '<tr><td colspan="3">Error al consultar la base de datos</td></tr>';
                } else {
                    $hay_datos = false;
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $hay_datos = true;
                        echo '<tr>';
                        echo '<td>'.htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8').'</td>';
                        echo '<td>'.htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8').'</td>';
                        echo '<td>'.htmlspecialchars($row['correo'], ENT_QUOTES, 'UTF-8').'</td>';
                        echo '</tr>';
                    }
                    
                    if (!$hay_datos) {
                        echo '<tr><td colspan="3">No hay registros en la base de datos</td></tr>';
                    }
                }
                
                sqlsrv_close($conn);
                ?>
            </table>
        </div>
    </div>
</body>
</html>

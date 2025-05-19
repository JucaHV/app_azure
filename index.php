<?php
// --- Conexión a SQL Server ---
$serverName = "dbserver5"; // O IP o nombre de tu instancia SQL Server
$connectionOptions = [
    "Database" => "formulario_app",
    "Uid" => "jucavarh",      // Cambia esto
    "PWD" => "A38391-bmu8tv",  // Cambia esto
    "CharacterSet" => "UTF-8"
];

// Crear conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Verificar conexión
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

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
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Formulario PHP - Captura y Consulta</title>
</head>
<body>
    <h2>Formulario de Captura</h2>
    <form method="POST" action="">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>
        <label>Correo:</label><br>
        <input type="email" name="correo" required><br><br>
        <input type="submit" value="Guardar">
    </form>

    <h2>Consulta de Información</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
        </tr>
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
            echo "<tr><td colspan='3'>No hay registros.</td></tr>";
        }

        sqlsrv_close($conn);
        ?>
    </table>
</body>
</html>

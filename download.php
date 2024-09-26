<?php
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_chartsoficiales";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];

    $column = '';
    switch ($type) {
        case 'ch':
            $column = 'Descarga_CH';
            break;
        case 'ghwtde':
            $column = 'Descarga_GHWTDE';
            break;
        case 'rb3':
            $column = 'Descarga_RB3';
            break;
        default:
            die("Tipo de descarga no válido");
    }

    $sql = "SELECT $column FROM chartsoficiales WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $url = $row[$column];

        if ($url) {
            $updateSql = "UPDATE chartsoficiales SET ${column}_count = ${column}_count + 1 WHERE ID = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $id);
            $updateStmt->execute();

            header("Location: $url");
            exit();
        } else {
            echo "URL no encontrada";
        }
    } else {
        echo "Registro no encontrado";
    }

    $stmt->close();
}

$conn->close();
?>

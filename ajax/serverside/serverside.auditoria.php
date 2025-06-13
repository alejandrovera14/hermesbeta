<?php
require 'serverside.conexion.php';

$pdo = new PDO("mysql:host=" . HOST_SS . ";dbname=" . DATABASE_SS, USER_SS, PASSWORD_SS);
$pdo->exec("SET NAMES utf8");

$columns = [
    'tipo_documento',
    'numero_documento',
    'nombre',
    'apellido',
    'nombre_editor',
    'fecha_cambio',
    'campo_modificado',
    'valor_anterior',
    'valor_nuevo'
];

$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$searchValue = $_GET['search']['value'] ?? '';

$sqlTotal = "SELECT COUNT(*) FROM vista_auditoria";
$totalRecords = $pdo->query($sqlTotal)->fetchColumn();

$sqlFiltered = "SELECT COUNT(*) FROM vista_auditoria";
if (!empty($searchValue)) {
    $sqlFiltered .= " WHERE ";
    $searchParts = [];
    foreach ($columns as $col) {
        $searchParts[] = "$col LIKE :search";
    }
    $sqlFiltered .= implode(" OR ", $searchParts);
}
$stmtFiltered = $pdo->prepare($sqlFiltered);
if (!empty($searchValue)) {
    $stmtFiltered->bindValue(':search', '%' . $searchValue . '%');
}
$stmtFiltered->execute();
$totalFiltered = $stmtFiltered->fetchColumn();

$sqlData = "SELECT * FROM vista_auditoria";
if (!empty($searchValue)) {
    $sqlData .= " WHERE ";
    $searchParts = [];
    foreach ($columns as $col) {
        $searchParts[] = "$col LIKE :search";
    }
    $sqlData .= implode(" OR ", $searchParts);
}
$sqlData .= " ORDER BY fecha_cambio DESC LIMIT :start, :length";
$stmtData = $pdo->prepare($sqlData);

if (!empty($searchValue)) {
    $stmtData->bindValue(':search', '%' . $searchValue . '%');
}
$stmtData->bindValue(':start', $start, PDO::PARAM_INT);
$stmtData->bindValue(':length', $length, PDO::PARAM_INT);
$stmtData->execute();
$data = $stmtData->fetchAll(PDO::FETCH_NUM);

$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);

<?php
require_once('config.php');

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$uid = $jsonArray['id_user'];
//jobのidの配列
$ids_job = $jsonArray['ids_job'];

$stmt = $pdo->prepare("SELECT * FROM Report WHERE id_job IN('".implode("','",$ids_job)."')");
$stmt->execute();
$result_data = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $row_array["id_report"] = $row["id_report"];
    $row_array["id_job"] = $row["id_job"];
    $row_array["id_keli"] = $row["id_keli"];
    $row_array["id_user"] = $row["id_user"];
    $row_array["comment"] = $row["comment"];
    $row_array["done"] = $row["done"];
    $row_array["created"] = $row["created"];
    $row_array["modified"] = $row["modified"];
    $row_array["image"] = $row["image"];

    array_push($result_data, $row_array);

}

echo json_encode(array_values($result_data));

?>
<?php

require_once('config.php');

header("Content-Type: application/json; charset=utf-8");

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$decodedData = json_decode($jsonEncoded, true);

$date = date('Y-m-d H:i:s');
$id_relation = $decodedData['id_relation'];

$stmt = $pdo->prepare("DELETE FROM Relation WHERE id_relation = '$id_relation'");
$stmt->execute();
$stmt->closeCursor();

?>
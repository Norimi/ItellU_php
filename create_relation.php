<?php

require_once('config.php');

header("Content-Type: application/json; charset=utf-8");

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$decodedData = json_decode($jsonEncoded);

//取り消しの場合は
$applying = true;

$relation_prms = array(
    ':id_user' => $decodedData->id_user,
    ':id_friend' => $decodedData->id_friend,
    ':bool' => $applying,
);

$stmt = $pdo->prepare('INSERT INTO Relation (id_user, id_friend, applying) VALUES (:id_user, :id_friend, :bool)');
$stmt->execute($relation_prms);
$stmt->closeCursor();

?>


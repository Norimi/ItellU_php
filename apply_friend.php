<?php
require_once('config.php');

//POSTされた名前でDBを検索する

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$id_user = $jsonArray['id_user'];
$id_friend = $jsonArray['id_friend'];
$applying = true;
$date = date('Y-m-d H:i:s');

$parameters = array(
    'id_user' => $jsonArray['id_user'],
    'id_friend' => $jsonArray['id_friend'],
    'applying' => $applying,
    ':created' => $date,
    ':modified' => $date,
);

$stmt = $pdo->prepare('INSERT INTO Relation (id_user, id_friend, applying, created, modified) VALUES (:id_user, :id_friend, :applying, :created, :modified)');
$stmt->execute($parameters);
$stmt->closeCursor();

?>
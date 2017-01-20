<?php

require_once('config.php');

header("Content-Type: application/json; charset=utf-8");

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded);

//現在時刻を取得
$date = date('Y-m-d H:i:s');
error_log(serialize($jsonArray));

$grp_prms = array(
    ':name'=>$jsonArray->name,
    ':description' => $jsonArray->description,
    ':created' => $date,
    ':modified' => $date,

);

$stmt = $pdo->prepare('INSERT INTO Groups (name, description, created, modified) VALUES (:name, :description, :created, :modified)');
$stmt->execute($grp_prms);
$id_group = $pdo->lastInsertId();
$stmt->closeCursor();

//uidをもとにして自分をGroupに紐づける
$id_friend = 0;
$applying = false;
$relation_prms = array(
    ':id_user' => $jsonArray->id_user,
    ':id_friend' => $id_friend,
    ':id_group' => $id_group,
    ':bool' => $applying,
    ':created' => $date,
    ':modified' => $date,
);

$stmt = $pdo->prepare('INSERT INTO Relation (id_user, id_friend, id_group, applying, created, modified) VALUES (:id_user, :id_friend, :id_group, :bool, :created, :modified)');
$stmt->execute($relation_prms);
$stmt->closeCursor();

echo json_encode($id_group);

?>




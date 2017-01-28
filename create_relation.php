<?php

require_once('config.php');

header("Content-Type: application/json; charset=utf-8");

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$decodedData = json_decode($jsonEncoded);
$decodedDataArr = json_decode($jsonEncoded, true);

$date = date('Y-m-d H:i:s');

//groupとfriendの関係作成時に共有中
//group申請のときはid_friendが0(String)のデータが送信され
//friend申請のときはid_groupが0のデータが送信される

//申請中フラグ:受諾されればfalseとなる(accept_application.php)
//group招待時と共有のため場合分け
if($decodedDataArr['id_group'] > 0){
    $applying = false;
} else {
    //友達申請の場合は0が送信される
    $applying = true;
}

$relation_prms = array(
    ':id_user' => $decodedData->id_user,
    ':id_friend' => $decodedData->id_friend,
    ':id_group' => $decodedData->id_group,
    ':bool' => $applying,
    ':created' => $date,
    ':modified' => $date,

);

$stmt = $pdo->prepare('INSERT INTO Relation (id_user, id_friend, id_group, applying, created, modified) VALUES (:id_user, :id_friend, :id_group, :bool, :created, :modified)');
$stmt->execute($relation_prms);
$stmt->closeCursor();

?>


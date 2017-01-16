<?php
require_once('config.php');

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$id_user = $jsonArray['id_user'];
//applyingをfalseにして関係を有効にする
$id_friend = $jsonArray['id_friend'];
$id_relation = $jsonArray['id_relation'];
$applying = false;
$date = date('Y-m-d H:i:s');

$parameters = array(
    ':applying' => $applying,
    ':id_relation' => $id_relation,
    'modified' => $date,
);

$stmt = $pdo->prepare('UPDATE Relation SET applying = :applying, modified = :modified WHERE id_relation = :id_relation');
$stmt->execute($parameters);
$stmt->closeCursor();

//自分の側からRelationを作成する
$accept_prms = array(
    'id_user' => $id_friend,
    'id_friend' => $id_user,
    ':applying' => $applying,
    ':created' => $date,
    ':modified' => $date,
);

$stmt3 = $pdo->prepare('INSERT INTO Relation (id_user, id_friend, applying, created, modified) VALUES (:id_user, :id_friend, :applying, :created, :modified)');
$stmt3 -> execute($accept_prms);
$stmt3 -> closeCursor();

//POSTされたidからUserを検索して情報を返す
//アプリ側で挿入する
$stmt2 = $pdo->prepare("SELECT * FROM User WHERE id_user = '$id_friend'");
$stmt2->execute();
$all = $stmt2->fetchAll();

$user_data = array(
    'id_user' => $all[0]['id_user'],
    'email' => $all[0]['email'],
    'photoURL' => $all[0]['photoURL'],
    'created' => $all[0]['created'],
    'modified' => $all[0]['modified'],
);
$stmt2->closeCursor();

echo json_encode(array_values($user_data));

?>



<?php

require_once('config.php');

header("Content-Type: application/json; charset=utf-8");

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded);

//現在時刻を取得
$date = date('Y-m-d H:i:s');
error_log(serialize($jsonArray));

$keli_prms = array(

    ':id_job' => $jsonArray->id_job,
    ':keli_from_userid' => $jsonArray->keli_from_userid,
    ':keli_to_userid' => $jsonArray->keli_to_userid,
    ':created' => $date,

);

file_put_contents('keli_array.txt', serialize($keli_prms));
print_r($keli_prms);

$stmt = $pdo -> prepare('INSERT INTO Keli (id_job, keli_from_userid, keli_to_userid, created) VALUES (:id_job, :keli_from_userid, :keli_to_userid, :created)');
$stmt -> execute($keli_prms);
$id_keli = $pdo -> lastInsertId();
$stmt -> closeCursor();

$comment_prms = array(

    ':id_user' => $jsonArray->keli_from_userid,
    ':id_keli' => $id_keli,
    ':comment' => $jsonArray->comment,
    ':created' => $date,
);

print_r($comment_prms);

$stmt2 = $pdo->prepare('INSERT INTO Comments (id_user, id_keli, comment, created) VALUES (:id_user, :id_keli, :comment, :created)');
$stmt2 -> execute($comment_prms);
$stmt2 -> closeCursor();

?>
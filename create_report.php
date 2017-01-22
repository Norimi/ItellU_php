<?php

require_once('config.php');

header("Content-Type: application/json; charset=utf-8");

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded);

//現在時刻を取得
$date = date('Y-m-d H:i:s');
$done = false;

$report_prms = array(

    ':id_job' => $jsonArray->id_job,
    ':id_keli' => $jsonArray->id_keli,
    ':id_user' => $jsonArray->id_user,
    ':comment' => $jsonArray->report,
    ':done' => $done,
    ':created' => $date,
    ':modified' => $date,
    ':image' => $jsonArray->image,
);

$stmt = $pdo->prepare('INSERT INTO Report (id_job, id_keli, id_user, comment, done, created, modified, image) VALUES (:id_job, :id_keli, :id_user, :comment, :done, :created, :modified, :image)');
$stmt->execute($report_prms);
$id_relation = $pdo->lastInsertId();

echo json_encode($id_relation);

?>
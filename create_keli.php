<?php

require_once('config.php');

header("Content-Type: application/json; charset=utf-8");

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded);

//現在時刻を取得
$date = date('Y-m-d H:i:s');

//POSTされるデータによって友達とグループを分ける
//友達に蹴る時:keli_to_groupidが 0
//グループに蹴る時:keli_to_useridが ""

$keli_prms = array(

    ':id_job' => $jsonArray->id_job,
    ':keli_from_userid' => $jsonArray->keli_from_userid,
    ':keli_to_userid' => $jsonArray->keli_to_userid,
    ':keli_to_groupid' => $jsonArray->keli_to_groupid,
    ':id_keli_before' => $jsonArray->id_keli_before,
    ':created' => $date,
    ':modified' => $date,

);

$stmt = $pdo -> prepare('INSERT INTO Keli (id_job, keli_from_userid, keli_to_userid, keli_to_groupid, id_keli_before, created, modified) VALUES (:id_job, :keli_from_userid, :keli_to_userid, :keli_to_groupid, :id_keli_before, :created, :modified)');
$stmt -> execute($keli_prms);
$id_keli = $pdo -> lastInsertId();
$stmt -> closeCursor();

//グループにけられた時点でグループのjobとなり、Jobのデータにid_groupが入る
$job_prms = array(
    ':id_group' => $jsonArray->keli_to_groupid,
    ':modified' => $date,
    ':id_job' => $jsonArray->id_job,
);

//蹴られたJobをグループのJobとする->get_newest_kelisで検出される
$stmt3 = $pdo -> prepare('UPDATE Job SET id_group = :id_group, modified = :modified WHERE id_job = :id_job');
$stmt3 -> execute($job_prms);
$stmt3 -> closeCursor();

$comment_prms = array(

    ':id_user' => $jsonArray->keli_from_userid,
    ':id_keli' => $id_keli,
    ':comment' => $jsonArray->comment,
    ':created' => $date,
    ':modified' => $date,
);

$stmt2 = $pdo->prepare('INSERT INTO Comments (id_user, id_keli, comment, created, modified) VALUES (:id_user, :id_keli, :comment, :created, :modified)');
$stmt2 -> execute($comment_prms);
$stmt2 -> closeCursor();

?>
<?php

require_once('config.php');

header("Content-Type: application/json; charset=utf-8");

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded);

//現在時刻を取得
$date = date('Y-m-d H:i:s');
$done = false;

//jobが受けられたタイミングでもタイムライン表示のためkeliを作成する
$keli_to_userid = '';
$keli_to_groupid = 0;
print_r($keli_to_groupid);
$accepted = true;
$keli_prms = array(

    ':id_job' => $jsonArray->id_job,
    ':keli_from_userid' => $jsonArray->id_user,
    ':keli_to_userid' => $keli_to_userid,
    ':keli_to_groupid' => $keli_to_groupid,
    ':id_keli_before' => $jsonArray->id_keli,
    ':created' => $date,
    ':modified' => $date,
    ':accepted' => $accepted,

);

$stmt = $pdo->prepare('INSERT INTO Keli (id_job, keli_from_userid, keli_to_userid, keli_to_groupid, id_keli_before, created, modified, accepted) VALUES (:id_job, :keli_from_userid, :keli_to_userid, :keli_to_groupid, :id_keli_before, :created, :modified, :accepted)');
$stmt->execute($keli_prms);
$id_keli = $pdo->lastInsertId();
//ここで取得されたid_keliをReportに入れる

$report_prms = array(

    ':id_job' => $jsonArray->id_job,
    ':id_keli' => $id_keli,
    ':id_user' => $jsonArray->id_user,
    ':comment' => $jsonArray->report,
    ':done' => $done,
    ':created' => $date,
    ':modified' => $date,
    ':image' => $jsonArray->image,
);

$stmt = $pdo->prepare('INSERT INTO Report (id_job, id_keli, id_user, comment, done, created, modified, image) VALUES (:id_job, :id_keli, :id_user, :comment, :done, :created, :modified, :image)');
$stmt->execute($report_prms);
$id_report = $pdo->lastInsertId();

//該当id_jobのreceiver_id_userにid_userを入れる
$id_job = $report_prms[':id_job'];
$job_prms = array(

    ':receiver_id_user' => $jsonArray->id_user,
    ':modified' => $date,
    ':id_job' => $id_job,

);
$stmt2 = $pdo->prepare('UPDATE Job SET receiver_id_user = :receiver_id_user, modified = :modified WHERE id_job = :id_job');
$stmt2->execute($job_prms);

//該当keliのacceptedにtrueを入れる
$id_keli = $report_prms[':id_keli'];
$accepted = true;
$keli_prms = array(

    ':accepted' => $accepted,
    ':modified' => $date,
    ':id_keli' => $id_keli,

);

//$stmt3 = $pdo->prepare('UPDATE Keli SET accepted = :accepted, modified = :modified WHERE id_keli = :id_keli');
//$stmt3->execute($keli_prms);

echo json_encode($id_report);

?>
<?php

    require_once('config.php');

    header("Content-Type: application/json; charset=utf-8");

    $jsonString = file_get_contents('php://input');
    $jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $jsonArray = json_decode($jsonEncoded);

    //現在時刻を取得
    $date = date('Y-m-d H:i:s');
    error_log(serialize($jsonArray));

    $job_prms = array(

        ':id_user' => $jsonArray->id_user,
        ':title' => $jsonArray->title,
        ':description' => $jsonArray->job_description,
        ':created' => $date,
    );

    //error_log(serialize($parameters));

    $stmt = $pdo -> prepare('INSERT INTO Job (id_user, title, job_description, created) VALUES(:id_user, :title, :description, :created)');
    $stmt -> execute($job_prms);
    $id_job = $pdo->lastInsertId();
    $stmt->closeCursor();


    $keli_prms = array(

        ':id_job' => $id_job,
        ':id_user' => $jsonArray->id_user,
        ':id_friend' => $jsonArray->id_friend,
     //   ':id_group' => $jsonArray->id_group,
        ':created' => $date

    );

    $stmt2 = $pdo -> prepare('INSERT INTO Keli (id_job, keli_from_userid, keli_to_userid, created) VALUES(:id_job, :id_user, :id_friend, :created)');
    $stmt2 -> execute($keli_prms);
    $id_keli = $pdo -> lastInsertId();
    $stmt2 -> closeCursor();

    $comments_prms = array(

        ':id_keli' => $id_keli,
        ':id_user' => $jsonArray->id_user,
        ':comment' => $jsonArray->comment,
        ':date' => $date,

    );

    $stmt3 = $pdo -> prepare('INSERT INTO Comments (id_keli, id_user, comment, created) VALUES (:id_keli, :id_user, :comment, :date)');
    $stmt3 -> execute($comments_prms);
    $stmt3 -> closeCursor();

?>
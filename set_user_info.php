<?php

    require_once('config.php');

    header("Content-Type: application/json; charset=utf-8");
    //リクエストの body 部から生のデータを読み込む
    $jsonString = file_get_contents('php://input');
    //文字化け対策
    $jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $jsonArray = json_decode($jsonEncoded);

    //TODO:デバッグ用
    //file_put_contents('received_json_array.txt', serialize($jsonArray));
    //error_log(serialize($jsonArray));

    $parameters = array(

        ':uid' => $jsonArray->uid,
        ':name' => $jsonArray->name,
        ':email' => $jsonArray->email,
        ':photoURL' => $jsonArray->photoURL,
    );

    $stmt = $pdo -> prepare('INSERT INTO User (id_user, name, email, photoURL) VALUES(:uid, :name, :email, :photoURL)');
    $stmt->execute($parameters);
    $stmt->closeCursor()

?>


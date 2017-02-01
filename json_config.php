<?php

    if (
        !isset($_SERVER['HTTP_CONTENT_TYPE']) ||
    false === strpos($_SERVER['HTTP_CONTENT_TYPE'], 'application/json')
    ) {
        exit('JSON を送信してください');
    }

    //リクエストの body 部から生のデータを読み込む
    $jsonString = file_get_contents('php://input');
    //文字化け対策
    $jsonString = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $jsonArray = json_decode($jsonString, true);


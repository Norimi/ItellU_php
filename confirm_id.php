<?php
    require_once('config.php');

    $jsonString = file_get_contents('php://input');
    $jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $jsonArray = json_decode($jsonEncoded, true);

    $uid = $jsonArray['uid'];

    $stmt = $pdo->prepare("SELECT id_user FROM User WHERE id_user = '$uid'");
    $stmt->execute();
    $all = $stmt->fetchAll();

    $count = count($all);

    //isset, is_nullは空の配列を評価する
    if($count > 0){

        print_r($all);
        //idがすでに登録されており重ねて登録する必要のない場合
        //var_dump(http_response_code( 512 ));
        echo "<p>このユーザーIDはすでに登録されています。</p>" ;
        header('HTTP/1.1 512 Uid Already Registered', true);

    } else {

        //postされたidがサーバーにない場合（期待される処理)
        //データがない場合にrespose codeを返す
        //var_dump(http_response_code( 212 ));
        echo "<p>このユーザーIDは登録されていません。</p>" ;
        header('HTTP/1.1 212 Uid Not Confirmed', true);


    }

?>
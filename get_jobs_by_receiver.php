<?php
require_once('config.php');

//POSTされたユーザーのidに関連のあるJOBを取得する
//- Remote:所属するグループに紐づくJob
//- Remote:いちどでも蹴ったことのあるJob(Keliからid_jobを抽出)
//- Remote:receiver_id_userが自分のJob

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);
$uid = $jsonArray['id_receiver'];

$stmt = $pdo->prepare("SELECT * FROM Job WHERE receiver_id_user = '$uid'");
$stmt->execute();
$result_data = array();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $row_array["id_job"] = $row["id_job"];
    $row_array["id_group"] = $row["id_group"];
    $row_array["title"] = $row["title"];
    $row_array["job_description"] = $row["job_description"];
    $row_array["modified"] = $row["modified"];
    $row_array["created"] = $row["created"];
    $row_array["done"] = $row["done"];
    $row_array["receiver_id_user"] = $row["receiver_id_user"];

    array_push($result_data, $row_array);

}

echo json_encode(array_values($result_data));

?>

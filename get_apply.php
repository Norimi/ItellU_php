<?php
require_once('config.php');

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

//該当フィールドではPOSTされるidがRelationの中でid_friendとして扱われている
$id_user = $jsonArray['id_user'];
$applying = true;
$date = date('Y-m-d H:i:s');

$stmt = $pdo->prepare("SELECT id_user, id_relation FROM Relation WHERE id_friend = '$id_user' AND applying = '$applying'");
$stmt->execute();

$result_data = array();

//検索される申請側=id_userでUserデータを検索する
$stmt2 = $pdo->prepare("SELECT * FROM User WHERE id_user = :id_user");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    //こちらのid_userは申請側のid_user
    $row_array['id_user'] = $row['id_user'];
    $row_array['id_relation'] = $row['id_relation'];
    $parameter = array(
        ':id_user' => $row_array['id_user'],
    );
    $stmt2->execute($parameter);
    $newFrData = $stmt2->fetchAll();
    //検索結果は必ず1件
    $row_array['name'] = $newFrData[0]['name'];
    $row_array['profile'] = $newFrData[0]['profile'];
    $row_array['photoURL'] = $newFrData[0]['photoURL'];
    //以下データは必要に応じて送信する
    //$row_array['email'] = $newFrData[0]['email'];
    //$row_array['created'] = $newFrData[0]['created'];
    //$row_array['modified'] = $newFrData[0]['modified'];
    array_push($result_data, $row_array);
}

echo json_encode(array_values($result_data));

?>





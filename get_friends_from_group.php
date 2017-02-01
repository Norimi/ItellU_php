<?php
require_once('config.php');

//id_groupから友達を取得
//グループメンバーが大量になることを考慮してremoteでの操作とした

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$id_group = $jsonArray['id_group'];

$stmt = $pdo->prepare("SELECT id_user FROM Relation WHERE id_group = '$id_group' ORDER BY modified DESC");
$stmt->execute();
$all = $stmt->fetchAll();
$friend_ids = array_column($all, 'id_user');


$stmt = $pdo->prepare('SELECT * FROM User WHERE id_user IN("'.implode('","', $friend_ids).'")');
$stmt->execute();

$result_data = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $row_array['id_user'] = $row['id_user'];
    $row_array['name'] = $row['name'];
    $row_array['email'] = $row['email'];
    $row_array['photoURL'] = $row['photoURL'];
    $row_array['created'] = $row['created'];
    $row_array['modified'] = $row['modified'];

    array_push($result_data, $row_array);

}

echo json_encode(array_values($result_data));

?>
<?php
require_once('config.php');

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$uid = $jsonArray['uid'];
$newest_modified = $jsonArray['modified'];
$applying = false;

$stmt = $pdo->prepare("SELECT id_friend, modified FROM Relation WHERE id_user = '$uid' AND applying = '$applying' AND id_group = 0 AND modified > '$newest_modified' ORDER BY modified DESC");
$stmt->execute();
$all = $stmt->fetchAll();
$friend_ids = array_column($all, 'id_friend');
$remote_modified = $all[0]['modified'];

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
$all_result_data = array($remote_modified, $result_data);
echo json_encode(array_values($all_result_data));

?>
<?php
require_once('config.php');

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$uid = $jsonArray['uid'];
$newest_modified = $jsonArray['modified'];
$applying = false;

$stmt = $pdo->prepare("SELECT id_group, modified FROM Relation WHERE id_user = '$uid' AND applying = '$applying' AND id_friend = 0 AND modified > '$newest_modified' ORDER BY modified DESC");
$stmt->execute();
$all = $stmt->fetchAll();
$group_ids = array_column($all, 'id_group');
print_r($group_ids);
$remote_modified = $all[0]['modified'];

$stmt2 = $pdo->prepare('SELECT * FROM Groups WHERE id_group IN("'.implode('","', $group_ids).'")');
$stmt2->execute();

$result_data = array();
while($row = $stmt2->fetch(PDO::FETCH_ASSOC)){

    $row_array['id_group'] = $row['id_group'];
    $row_array['name'] = $row['name'];
    $row_array['description'] = $row['description'];
    $row_array['created'] = $row['created'];
    $row_array['modified'] = $row['modified'];

    array_push($result_data, $row_array);

}
$all_result_data = array($remote_modified, $result_data);
echo json_encode(array_values($all_result_data));

?>
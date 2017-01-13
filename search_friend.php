<?php
require_once('config.php');

//POSTされた名前でDBを検索する

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$name = $jsonArray['name'];

$stmt = $pdo->prepare('SELECT * FROM User WHERE name LIKE ?');
$stmt->execute(array(sprintf('%%%s%%', addcslashes($name, '\_%'))));


$result_users = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $row_array['id_user'] = $row['id_user'];
    $row_array['name'] = $row['name'];
    $row_array['profile'] = $row['profile'];
    $row_array['email'] = $row['email'];
    $row_array['photoURL'] = $row['photoURL'];
    $row_array['created'] = $row['created'];

    array_push($result_users, $row_array);

}

echo json_encode(array_values($result_users));

?>
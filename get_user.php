<?php
require_once('config.php');

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$uid = $jsonArray['uid'];
$stmt = $pdo->prepare("SELECT * FROM User WHERE id_user = '$uid'");
$stmt->execute();

$friends_data = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        $row_array['id_user'] = $row['id_user'];
        $row_array['name'] = $row['name'];
        $row_array['photoURL'] = $row['photoURL'];
        $row_array['created'] = $row['created'];
        $row_array['email'] = $row['email'];
        $row_array['modified'] = $row['modified'];

        array_push($friends_data, $row_array);

}

echo json_encode(array_values($friends_data));

?>
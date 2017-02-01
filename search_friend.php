<?php
require_once('config.php');

//POSTされた名前でDBを検索する

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$name = $jsonArray['name'];
$uid = $jsonArray['id_user'];
$stmt = $pdo->prepare('SELECT * FROM User WHERE name LIKE ?');
$stmt->execute(array(sprintf('%%%s%%', addcslashes($name, '\_%'))));

$result_users = array();

$stmt2 = $pdo->prepare("SELECT applying FROM Relation WHERE id_user = :id_user AND id_friend = :id_friend");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $row_array['id_user'] = $row['id_user'];
    $params = array(
        ':id_user' => $uid,
        ':id_friend' => $row['id_user'],
    );
    $row_array['name'] = $row['name'];
    $row_array['profile'] = $row['profile'];
    //$row_array['email'] = $row['email'];
    //$row_array['photoURL'] = $row['photoURL'];
    $row_array['created'] = $row['created'];

    //関係を調査する
    $stmt2->execute($params);
    $all = $stmt2->fetchAll();

    //以下statusを表すstring
    $applying = "applying";
    $friend = "friend";
    $no_relation = "no_relation";
    //trueだったら申請中、falseだったら友達、内容がなければ無関係です
    $count = count($all);
    if($count > 0){
        if($all[0]['applying'] == true){
            $row_array['status'] = $applying;
        } else {
            $row_array['status'] = $friend;
        }

    } else {
        $row_array['status'] = $no_relation;
    }

    array_push($result_users, $row_array);

}

echo json_encode(array_values($result_users));

?>
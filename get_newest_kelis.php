<?php
require_once('config.php');

//POSTされたユーザーのidに関連のあるKeliを取得する
//TODO:最新情報がないときでも検索クエリの負荷が高いので解決策を考える

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$uid = $jsonArray['uid'];
$newest_modified = $jsonArray['modified'];

//所属するグループを検索する
$stmt = $pdo->prepare("SELECT id_group FROM Relation WHERE id_user = '$uid'");
$stmt->execute();
$all = $stmt->fetchAll();
$ids_group = array_column($all, 'id_group');

//グループが持っているJobを検索する
$stmt2 = $pdo->prepare('SELECT id_job FROM Job WHERE id_group IN("'.implode('","', $ids_group).'")');
$stmt2->execute();
$all = $stmt2->fetchAll();
$ids_job_from_group = array_column($all, 'id_job');

//JOBの配列からKeliを検索
$stmt = $pdo->prepare('SELECT id_keli FROM Keli WHERE id_job IN("'.implode('","', $ids_job_from_group).'")');
$stmt->execute();
$all = $stmt->fetchAll();
$ids_kelis_from_group = array_column($all, 'id_keli');

//一度でも蹴ったことのあるKeliを全て取得
$stmt = $pdo->prepare("SELECT id_keli FROM Keli WHERE keli_from_userid = '$uid'");
$stmt->execute();
$all = $stmt->fetchAll();
$id_keli_from_user_id = array_column($all, 'id_keli');
//print_r($id_keli_from_user_id);

//そのKeliが関連しているすべてのJOBを取得
$stmt = $pdo->prepare('SELECT id_job FROM Keli WHERE id_keli IN("'.implode('","', $id_keli_from_user_id).'")');
$stmt->execute();
$all = $stmt->fetchAll();
$id_jobs_from_user_keli = array_column($all, 'id_job');

//そのJOBが関連している全てのKeliを取得
$stmt = $pdo->prepare('SELECT id_keli FROM Keli WHERE id_job IN("'.implode('","', $id_jobs_from_user_keli).'")');
$stmt->execute();
$all = $stmt->fetchAll();
$id_kelis_from_user_keli = array_column($all, 'id_keli');

//receiver_id_userが自分のJobを検索
$stmt = $pdo->prepare("SELECT id_job FROM Job WHERE receiver_id_user = '$uid'");
$stmt->execute();
$all = $stmt->fetchAll();
$jobs_from_receiver = array_column($all, 'id_job');

//receiver_id_userが自分のJobに紐づく全Keliを取得
$stmt = $pdo->prepare('SELECT id_keli FROM Keli WHERE id_job IN("'.implode('","', $jobs_from_receiver).'")');
$stmt->execute();
$all = $stmt->fetchAll();
$id_kelis_from_receiver = array_column($all, 'id_keli');

//$ids_kelis_from_group,$id_keli_from_user_id,$id_kelis_from_user_keli,$id_kelis_from_receiverを統合
$result_kelis = array_merge($ids_kelis_from_group, $id_keli_from_user_id, $id_kelis_from_user_keli, $id_kelis_from_receiver);
//print_r($result_kelis);
//全keliidからKeliを取得する
$stmt = $pdo->prepare('SELECT * FROM Keli WHERE id_keli IN("'.implode('","', $result_kelis).'") ORDER BY modified DESC');
$stmt->execute();

//結果からKeliのデータを得る
$keli_data = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    if($row['modified'] > $newest_modified){

        $row_array['id_keli'] = $row['id_keli'];
        $row_array['id_job'] = $row['id_job'];
        $row_array['keli_from_userid'] = $row['keli_from_userid'];
        $row_array['keli_to_userid'] = $row['keli_to_userid'];
        $row_array['created'] = $row['created'];
        $row_array['modified'] = $row['modified'];
        $row_array['accepted'] = $row['accepted'];
    }

    array_push($keli_data, $row_array);

}

echo json_encode(array_values($keli_data));

?>

<?php
require_once('config.php');

//POSTされたユーザーのidに関連のあるJOBを取得する
//- Remote:所属するグループに紐づくJob
//- Remote:いちどでも蹴ったことのあるJob(Keliからid_jobを抽出)
//- Remote:receiver_id_userが自分のJob

$jsonString = file_get_contents('php://input');
$jsonEncoded = mb_convert_encoding($jsonString, 'UTF8','ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$jsonArray = json_decode($jsonEncoded, true);

$uid = $jsonArray['uid'];
$newest_modified = $jsonArray['modified'];

//所属するグループを検索する
$stmt = $pdo->prepare("SELECT id_group FROM Relation WHERE id_user = '$uid'");
$stmt->execute();
$all = $stmt->fetchAll();
$related_group_id = array_column($all, 'id_group');


//所属するグループが持っているJOBを検索する
$stmt = $pdo->prepare('SELECT id_job FROM Job WHERE id_group IN ("'.implode('","', $related_group_id).'")');
$stmt->execute();
$all = $stmt->fetchAll();
$group_jobs = array_column($all, 'id_job');

//蹴ったことのあるKeliからid_jobを検出
$stmt = $pdo->prepare("SELECT id_job FROM Keli WHERE keli_from_userid = '$uid'");
$stmt->execute();
$all = $stmt->fetchAll();
$keried_jobs = array_column($all, 'id_job');

//receiver_idからJobを検索
$stmt = $pdo->prepare("SELECT id_job FROM Job WHERE receiver_id_user = '$uid'");
$stmt->execute();
$all = $stmt->fetchAll();
$received_jobs = array_column($all, 'id_job');

//得られたすべてのid_jobをマージ
//この時点で重複しているのも全て持っています
$jobs_ids = array_merge($group_jobs, $keried_jobs, $received_jobs);

//得られたidからJOBを検索5してmodified順に並べる
$stmt = $pdo->prepare('SELECT * FROM Job WHERE id_job IN ("'.implode('","', $jobs_ids).'") ORDER BY modified DESC');
$stmt->execute();
$allJobs = $stmt->fetchAll();

$newest_remote_modified = $allJobs[0]['modified'];
//echo($newest_modified);
//echo($newest_remote_modified);
if($newest_modified == $newest_remote_modified){
    //最新が更新されていない場合
    //何もせず抜ける
    return;
}

$result_data = array();

foreach($allJobs as $row){

    //0000-00-00 00:00:00となることはないようにする
    if($row['modified'] > $newest_modified && $row['modified']!= "0000-00-00 00:00:00"){

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
}
echo json_encode(array_values($result_data));

?>


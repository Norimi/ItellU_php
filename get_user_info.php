<?php
require_once('config.php');
$stmt = $pdo->query('SELECT * FROM User');
while($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $name = $row['name'];
    $email = $row['email'];
    $profile = $row['profile'];
    echo<<<EOF
    [$name]
    [$email]
    [$profile]
EOF;
}

?>
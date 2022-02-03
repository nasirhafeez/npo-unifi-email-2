<?php

require 'header.php';
include 'config.php';

$myVideoDir = '../assets/media';
$extension = 'mp4';
$videoFile = false;
$pseudoDir = scandir($myVideoDir);
$myitems = array();
foreach($pseudoDir as $item) {
  if ( $item != '..' && $item != '.' && !is_dir($item) ) {
    $ext = preg_replace('#^.*\.([a-zA-Z0-9]+)$#', '$1', $item);
    if ( $ext == $extension ) {
      $videoFile = $item;
      if ( $videoFile <> "" ) {
        array_push($myitems, $videoFile);
      }
    }
  }
}

$rand_item = array_rand($myitems, 1);
$video_name = $myitems[$rand_item];

$mac = $_SESSION["id"];
$apmac = $_SESSION["ap"];
$method = $_SESSION["method"];
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$phone = $_SESSION['phone'];
$email = $_SESSION['email'];
$last_updated = date("Y-m-d H:i:s");
$video_count = $_SESSION["video_count"] + 1;

$controlleruser = $_SERVER['CONTROLLER_USER'];
$controllerpassword = $_SERVER['CONTROLLER_PASSWORD'];
$controllerurl = $_SERVER['CONTROLLER_URL'];
$controllerversion = $_SERVER['CONTROLLER_VERSION'];
$duration = $_SERVER['DURATION'];

$debug = false;

$unifi_connection = new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
$set_debug_mode   = $unifi_connection->set_debug($debug);
$loginresults     = $unifi_connection->login();

$auth_result = $unifi_connection->authorize_guest($mac, $duration, null, null, null, $apmac);

mysqli_query($con, "
CREATE TABLE IF NOT EXISTS `$table_name` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `phone` varchar(16) NOT NULL,
    `email` varchar(45) NOT NULL,
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `mac` varchar(17) NOT NULL,
    `apmac` varchar(17) NOT NULL,
    `method` varchar(10) NOT NULL,
    `last_updated` datetime NOT NULL,
    `video_count` int(6) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `mac` (`mac`)
)");

if ($_SESSION['user_type'] == "new") {
    mysqli_query($con, "INSERT INTO `$table_name` (phone, email, first_name, last_name, mac, apmac, method, last_updated, video_count) VALUES ('$phone','$email','$fname','$lname','$mac', '$apmac', '$method', '$last_updated', '$video_count')");
} else {
    mysqli_query($con, "UPDATE `$table_name` SET last_updated = '$last_updated', video_count = '$video_count' WHERE mac = '$mac'");
}

mysqli_close($con);

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($business_name); ?> WiFi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="../assets/styles/bulma.min.css" />
    <link rel="stylesheet" href="../vendor/fortawesome/font-awesome/css/all.css" />
    <meta http-equiv="refresh" content="45;url=https://www.google.com" />
    <link rel="icon" type="image/png" href="../assets/images/favicomatic/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="../assets/images/favicomatic/favicon-16x16.png" sizes="16x16" />
    <link rel="stylesheet" href="../assets/styles/style.css" />
</head>

<body>
<div class="page">

    <div class="head">
        <br>
        <figure id="logo">
            <img src="../assets/images/logo.png">
        </figure>
    </div>

    <div class="main">
        <seection class="section">
            <div class="centered_text">
                <div id="countdown"></div>
            </div>
            <br>
            <div class="video_wrap">
                <video controls autoplay>
                    <source src="../assets/media/<?php echo htmlspecialchars($video_name); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </seection>
    </div>

<!--    <div class="main">-->
<!--        <seection class="section">-->
<!--            <div id="margin_zero" class="content has-text-centered is-size-6">Thanks, you are now </div>-->
<!--            <div id="margin_zero" class="content has-text-centered is-size-6">authorized on WiFi</div>-->
<!--        </seection>-->
<!--    </div>-->

</div>

<script type="text/javascript">
    var timeleft = 45;
    var downloadTimer = setInterval(function(){
        if(timeleft <= 0){
            clearInterval(downloadTimer);
        } else {
            document.getElementById("countdown").innerHTML = "Browsing will start immediately after the video in " + timeleft + "s";
        }
        timeleft -= 1;
    }, 1000);
</script>

</body>

</html>

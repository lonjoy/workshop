<?php
    $id  = $_GET['id'];
    $url = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.song.play&format=json&songid=$id&from=mixapp";
    echo file_get_contents($url);
?>
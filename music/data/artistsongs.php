<?php
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $limit = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    $limit = $limit * 20;
    $url = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.artist.getSongList&format=json&tinguid=$id&limits=$limit&order=2&from=mixapp";
    echo file_get_contents($url);
?>
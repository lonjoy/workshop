<?php
    $size = 20;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    $offset = $page * $size;
    $url = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.artist.get72HotArtist&format=jsonp&limit=20&from=mixapp&offset=$offset";
    echo file_get_contents($url);
?>
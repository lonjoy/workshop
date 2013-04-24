<?php
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $url = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.artist.getAlbumList&format=json&tinguid=$id&limits=20&order=2&from=mixapp";
    echo file_get_contents($url);
?>
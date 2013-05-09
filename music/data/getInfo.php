<?php
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $limit = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    $limit = $limit * 20;
    
    $urlAlbums = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.artist.getAlbumList&format=json&tinguid=$id&limits=20&order=2&from=mixapp";
    $urlSongs = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.artist.getSongList&format=json&tinguid=$id&limits=$limit&order=2&from=mixapp";
    $urlInfo = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.artist.getInfo&format=jsonp&tinguid=$id&from=mixapp";
    $dataInfo = file_get_contents($urlInfo);
    $dataAlbums = file_get_contents($urlAlbums);
    $dataSongs = file_get_contents($urlSongs);
    
    echo "{\"info\":$dataInfo,\"songs\":$dataSongs,\"albums\":$dataAlbums}";
?>
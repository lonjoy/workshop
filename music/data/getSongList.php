<?php
    $url = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.artist.get72HotArtist&format=jsonp&limit=20&from=mixapp";
    echo file_get_contents($url);
?>
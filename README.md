workshop
============

##目录说明

    .
    |--workshop
       |---common 基础库(项目依赖，包含Zepto、GMU、Backbone等)
       |---music  music项目目录

##准备材料

###Router
<table>
    <thead>
        <tr>
            <td>路由</td>
            <td>说明</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>''</td>
            <td>首页</td>
        </tr>
        <tr>
            <td>'#musichot'</td>
            <td>热门音乐榜</td>
        </tr>
        <tr>
            <td>'#song/:id'</td>
            <td>歌曲播放</td>
        </tr>
    </tbody>
</table>

###接口
<table>
    <thead>
        <tr>
            <td>API</td>
            <td>说明</td>
            <td>参数</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>/music/musichot.php</td>
            <td>获取热门音乐榜</td>
            <td>page(Int):页码,从0开始</td>
        </tr>
        <tr>
            <td>/music/category.json</td>
            <td>获取所有的音乐分类</td>
            <td>无</td>
        </tr>
        <tr>
            <td>/music/artisthot.php</td>
            <td>获取热门歌手</td>
            <td>无</td>
        </tr>
        <tr>
            <td>/music/play.php</td>
            <td>获取歌曲详情</td>
            <td>id(Int):歌曲id</td>
        </tr>
    </tbody>
</table>

##分工

所有人划分为三组，每一组的人做相同的部分，鼓励交流。

<table>
    <thead>
        <tr>
            <td>组别</td>
            <td>任务</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>第一部分</td>
            <td>完成首页的subview：category/artist/musichot</td>
        </tr>
        <tr>
            <td>第二部分</td>
            <td>完成热门音乐榜</td>
        </tr>
        <tr>
            <td>第三部分</td>
            <td>完成歌曲播放页(不含歌词展示及真实播放)</td>
        </tr>
    </tbody>
</table>


##开始动手
1. 运行fis，创建common、music两个项目。
2. 下载本项目代码，将common、music两个目录覆盖至第1步创建的项目。
3. 根据自己分到的任务，将相应的部分补充至项目里(对应的地方已有TODO标识)
4. 测试、确认。

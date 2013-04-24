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
            <td>#musichot</td>
            <td>热门音乐榜</td>
        </tr>
        <tr>
            <td>#song/:id</td>
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
            <td>page 页面，从0开始</td>
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
            <td>id 歌曲id</td>
        </tr>
    </tbody>
</table>

##开始动手
1. 运行fis，创建common、music两个项目。
2. fork本项目，将common、music两个目录覆盖至第1步创建的项目。
3. 根据自己分到的任务，将相应的部分补充至项目里(对应的地方已有TODO标识)
4. 测试无误后，提交代码并Pull到主干。
5. 工作人员完成代码合并，展示整体效果。
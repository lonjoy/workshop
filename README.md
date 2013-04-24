workshop
============

##目录说明

    .
    |--workshop
       |---common 基础库(项目依赖，包含Zepto、GMU、Backbone等)
       |---music  music项目目录

##准备材料

###Router

|路由|说明|
|''|首页|
|#musichot|热门音乐榜|
|#song/:id|歌曲播放|

###接口
|API|说明|参数|
|/music/musichot.php| 获取热门音乐榜 |page 页面，从0开始 |
|/music/category.json| 获取所有的音乐分类 |无|
|/music/artisthot.php| 获取热门歌手 |无|
|/music/play.php| 获取歌曲详情 |id 歌曲id|

##开始动手
1. 运行fis，创建common、music两个项目。
2. fork本项目，将common、music两个目录覆盖至第1步创建的项目。
3. 根据自己分到的任务，将相应的部分补充至项目里(对应的地方已有TODO标识)
4. 测试无误后，提交代码并Pull到主干。
5. 工作人员完成代码合并，展示整体效果。
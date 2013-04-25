
(function($) {

app.router.vs = app.router.extend({

    // 路由配置
    routes: {
          ''                         : 'index'

        , 'category'                 : 'category'
        , 'categorydetail/:id'       : 'categorydetail'
        
        ///TODO musichot
        ///TODO song

    }

    // 页面切换顺序配置
    ,pageOrder: [
          'index'

        , 'category'
        , 'categorydetail'
        
        ///TODO musichot

    ]

    // 位置记忆，默认为false，不进行位置记忆
    ,enablePositionRestore : true

    // 默认页面切换动画
    ,defaultPageTransition : 'slide'

    // 页面切换动画配置
    ,pageTransition: {

         'index-category'     : 'slide'


    }

    ,index: function(type) {
        
        this.doAction('index', {},
            //禁止发送
            {disable: true}
        );
    }

    ,category : function(){
        
        this.doAction('category', {},
            //禁止发送
            {disable: true}
        );
    }
    ,categorydetail : function(id){
        id = id || 0;
        this.doAction('categorydetail', {id:decodeURIComponent(id)},
            //禁止发送
            {disable: true}
        );
    }
 
    ///TODO musichot
    
    ///TODO song
    
    ,defaultRoute: function(defaultUrl) {
        Backbone.history.navigate('index', {trigger: true, replace: true});
    }

    /**
     * action处理逻辑
     * @{param} action {string} action名称
     * @{param} params {object} action参数
     * @{param} statOptions {object} 统计选项{disable:是否屏蔽统计,默认开启;param:{key: value,key: value}}]统计参数}
     */
    ,doAction: function(action, params, statOptions){

        app.router.prototype.doAction.apply(this, arguments);
    }

}); 

})(Zepto);





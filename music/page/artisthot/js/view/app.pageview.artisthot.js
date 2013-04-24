/**
 * 首页View类
 */
(function($) {

app.pageview.artisthot = app.pageview.extend({
    el: "#artisthot_page"

    ,init: function(options){
        var me = this;

        me.setup(new app.subview.artisthot_header(options, me));

        me.setup(new app.subview.artisthot_content(options, me));
        
        me.setup(new app.subview.shortcut({}, me));
    }
    
    ,registerEvents : function(){
        var me = this, ec = me.ec;
        ec.on("pagebeforechange", me.onpagebeforechange, me);
    }
    
    ,onpagebeforechange : function(params){
        var me = this, 
            from = params.from,
            to = params.to,
            param = params.params;
        
        if(to == me.ec){
            new app.subview.toolbar({title:"热门歌手",action:'artisthot'},me);
        }
        
    }
    
   

});

})(Zepto);



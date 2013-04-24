
(function($) {

app.pageview.musicnew = app.pageview.extend({
      el: "#musicnew_page"

    , init: function(options){
       var me = this;
        
        
        me.setup(new app.subview.musicnew_content(options, me));
        
        me.setup(new app.subview.shortcut({}, me));
    }
    
    , registerEvents : function(){
        var me = this, ec = me.ec;
        ec.on("pagebeforechange", me.onpagebeforechange, me);
    }
    
    , onpagebeforechange : function(params){
        var 
              me = this
            , from = params.from
            , to = params.to
            , param = params.params
            ;
        
        if(to == me.ec){
            new app.subview.toolbar({
                    title  : "新歌榜"
                  , action : 'musicnew'
            }, me);
        }    
        
    }

});

})(Zepto);



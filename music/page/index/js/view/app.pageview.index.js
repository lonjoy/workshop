
(function($) {

rocket.pageview.index = rocket.pageview.extend({
      el: "#index_page"

    , init: function(options){
        var me = this;


        me.setup(new rocket.subview.index_static(options, me));
        me.setup(new rocket.subview.index_content(options, me));

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
            new rocket.subview.toolbar({title:"百度音乐",action:'index'},me);
            
            $('#footer-index').show();
            $('#footer').hide();
        }else{
            $('#footer-index').hide();
            $('#footer').show();
        }
        
    }

    
});

})(Zepto);




(function($) {

rocket.subview.index_static = rocket.subview.extend({
      el: "#index_page_static"

    , init: function(options){
        var 
              me = this
            , id = options.id
            , subView
            ;


        me.setup(new rocket.subview.index_static_scroll(options, me));
        me.setup(new rocket.subview.index_static_nav(options, me));
    }

    

    ,registerEvents: function(){
        var me = this, ec = me.ec;
        ec.on("pagebeforechange", me.onpagebeforechange, me);
    }

    ,onpagebeforechange: function(params){
        var 
              me = this
            , from = params.from
            , to = params.to
            , param = params.params
            ;

        if(to == me.ec) {
            me.$el.show();
        }
    }

});

})(Zepto);



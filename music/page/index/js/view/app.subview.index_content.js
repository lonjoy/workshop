
(function($) {

rocket.subview.index_content = rocket.subview.extend({
      el: "#index_page_content"

    , init: function(options){
        var 
              me = this
            , id = options.id
            , subView
            ;


        me.setup(new rocket.subview.index_content_cover(options, me));
        
        ///TODO index_content_category
        ///TODO index_content_artisthot
        ///TODO index_content_musichot
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



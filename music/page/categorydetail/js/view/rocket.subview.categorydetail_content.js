
(function($) {

rocket.subview.categorydetail_content = rocket.subview.extend({
      el: "#categorydetail_page_content"

    , init: function(options){
        var 
              me = this
            , id = options.id
            , subView
            ;


        subView = new rocket.subview.categorydetail_content_detail(
            $.extend({}, options), 
            me
        );
        me.append(subView);

        me.registerSubpage(id, subView);
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
            if(!me.getSubpage(param.id)){
                var subView = new rocket.subview.categorydetail_content_detail(
                    $.extend({}, param), 
                    me
                );
                me.append(subView);
                me.registerSubpage(param.id, subView);
            }

            me.setCurrentSubpage(me.getSubpage(param.id));
            me.recycleSubpage();

            me.$el.show();
        }
    }

});

})(Zepto);



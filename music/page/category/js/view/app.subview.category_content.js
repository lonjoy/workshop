
(function($) {

rocket.subview.category_content = rocket.subview.extend({
    el: "#category_page_content"

    ,template: _.template(
        $('#template_category_content').text()
    )

    ,events: {
        'tap .list li.url' : 'categoryDetail'
    }

    ,init: function(options){
        var me = this;

        me.isFirstLoad = true;


        me.collection = new rocket.collection.category_music(null, options);

        me.showLoading(me.$el);
    }

    ,render: function(){
        var me = this;


        me.$el.append(
            me.template({
                category: me.collection.toJSON()
            })
        );
        
        me._bindTouchEvent.call(me);
        me.hideLoading();

        return me;
    }

    ,registerEvents: function(){
        var me = this, ec = me.ec;
        ec.on("pagebeforechange", me.onpagebeforechange, me);
        me.collection.on('reset', me.render, me);
    }

    ,onpagebeforechange: function(params){
        var me = this, 
            from = params.from,
            to = params.to,
            param = params.params;

        if(to == me.ec) {
            me.$el.show();
            if(me.isFirstLoad){
                me.collection.fetch({
                    success: function(){
                        me.isFirstLoad = false;
                    }
                });
            }
            
        }else{
            me.$el.hide();
        }
    }
    /**
     * °ó¶¨touchÊÂ¼þ
     *
     */
    , _bindTouchEvent : function(){
         var me = this;
        me.$el.find('li.url').highlight('active');
    
    }
    , categoryDetail : function(e){
         var 
              me     = this
            , el     = $(e.target).closest('li.url')
            , route  = 'categorydetail/<%= lable %>'
            ;
        
        route = _.template(route)({
            lable : encodeURIComponent(el.data('lable'))
        });
        
        Backbone.history.navigate(route, {trigger:true});    
    }


});

})(Zepto);



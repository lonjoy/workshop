
(function($) {

app.subview.albumdetail_content_detail = app.subview.extend({
    

      template: _.template(
        $('#template_albumdetail_content').text()
    )

    , className : 'albumdetail_content_detail'


    , init: function(options){
        var me = this;
        
        me.options = options;

        me.isFirstLoad = true;

        me.model = new app.model.albumdetail_music(null, options);

        me.showLoading(me.$el);
    }

    ,render: function(){
        var me = this;
        me.$el.append(
            me.template({
                albumdetail: me.model.toJSON()
            })
        );
        

        me.hideLoading();

        return me;
    }

    ,registerEvents: function(){
        var me = this, ec = me.ec;
        ec.on("subpagebeforechange", me.onsubpagebeforechange, me);
        me.model.on('change', me.render, me);
    }
    ,unregisterEvents: function(){
        var me = this, ec = me.ec;

        ec.off('subpagebeforechange', me.onsubpagebeforechange, me);
        me.model.off('change', me.render, me);

    }
    ,onsubpagebeforechange: function(params){
        var me = this, 
            from = params.from,
            to = params.to,
            param = params.params;

        if(to == me.ec) {
            me.$el.show();
            if(me.isFirstLoad){
                me.model.fetch({
                    success: function(){
                        me.isFirstLoad = false;
                    }
                });
            }
        }else{
            me.$el.hide();
        }
    }
    

    


});

})(Zepto);



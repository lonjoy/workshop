
(function($) {

rocket.subview.index_content_cover = rocket.subview.extend({
      el: "#index_page_cover"

    , template: _.template(
        $('#template_index_cover').text()
    )

    , events: {
          'tap .albums .cover li.url' : 'albumDetail'
        , 'tap .albums .items li.url' : 'albumDetail'

    }

    , init: function(options){
        var me = this;

        me.isFirstLoad = true;

        me.model = new rocket.model.index_music_recommendalbum(null, options);

        me.showLoading(me.$el);
        
        
    }

    , render: function(){
        var me = this;

        me.$el.append(
            me.template({
                album_list: _.values(me.model.toJSON())
            })
        );
        
        me.hideLoading();
        return me;
    }

    ,registerEvents: function(){
        var me = this, ec = me.ec;
        ec.on("pagebeforechange", me.onpagebeforechange, me);
        me.model.on('change', me.render, me);
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

            if(me.isFirstLoad){
                me.model.fetch({
                    success: function(){
                        me.isFirstLoad = false;
                    }
                });
            }
            
        }
    }

    
    
    , albumDetail : function(e){
        var 
              me     = this
            , el     = $(e.target).closest('li.url')
            , route  = 'albumdetail/<%= id %>/<%= name %>'
            ;
        
        route = _.template(route)({
              id   : encodeURIComponent(el.data('id'))
            , name : encodeURIComponent(el.data('name'))
        });
        
        Backbone.history.navigate(route, {trigger:true});
        
        e.preventDefault();
    }

    
    

});

})(Zepto);



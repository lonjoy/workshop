
(function($) {

app.subview.musicnew_content = app.subview.extend({
    
    el: "#musicnew_page_content"

    ,template: _.template(
        $('#template_musicnew_content').text()
    )
    
    ,template_item : _.template(
        $('#template_musicnew_content_item').text()
    )
    
    ,events: {
          'tap li.song'      : 'playMusic'
        , 'tap .load-more'   : 'loadMore'
    }

    ,init: function(options){
        var me = this;

        me.isFirstLoad = true;

        me.model = new app.model.musicnew_music(null, options);

        me.showLoading(me.$el);
    }

    ,render: function(){
        var me = this,item;
        
        item = me.template_item({
                      musicnew : me.model.toJSON()
                    , page     : parseInt(me.model.get('page'),10) //api返回的rank字段有误
                });
                
        if(me.model.get('page') == 0){
            me.$el.append(
                me.template({
                    item : item
                })
            );
            me.hideLoading();
            
        }else{
            $(item).insertBefore(me.$el.find('.list li.load-more'));
        }
        
        me._bindTouchEvent.call(me);
        return me;
    }

    ,registerEvents: function(){
        var me = this, ec = me.ec;
        ec.on("pagebeforechange", me.onpagebeforechange, me);
        me.model.on('change', me.render, me);
    }

    ,onpagebeforechange: function(params){
        var me = this, 
            from = params.from,
            to = params.to,
            param = params.params;
        
        if(to == me.ec) {
            me.$el.show();
            if(me.isFirstLoad){
                me.model.fetch({
                    data : {
                        page:me.model.get('page')
                    },
                    success: function(){
                        me.isFirstLoad = false;

                    }
                });
           }
            
        }
    }

    
    /**
     * 绑定更多时的事件
     *
     */
    , loadMore : function(e){
        var me = this,that = $(e.target),loadingMore = app.loadingMore(that);
        me.model.off('change');
        me.model.set({
              page      : me.model.get('page') + 1
        },{silent:true});
        //me.showLoading(me.$el); //防止白屏
        loadingMore.show();
        me.model.fetch({
            data : {
                page : me.model.get('page')
            },
            success: function(){
                me.render.call(me);
                loadingMore.hide();
            }
        });
    }
    
    /**
     * 绑定touch事件
     *
     */
    , _bindTouchEvent : function(){
         var me = this;
        me.$el.find('li.url').highlight('active');
    
    }
    
    , playMusic : function(e){
         var 
              me     = this
            , el     = $(e.target).closest('li.url')
            , route  = 'song/<%= id %>'
            ;
        
        route = _.template(route)({
            id : encodeURIComponent(el.data('songid'))
        });
        
        Backbone.history.navigate(route, {trigger:true});
        
        return false;
    }
   

});

})(Zepto);



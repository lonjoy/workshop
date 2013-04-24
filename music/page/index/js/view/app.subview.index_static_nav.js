
(function($) {

app.subview.index_static_nav = app.subview.extend({
      el: "#index_page_nav"

    , template: _.template(
        $('#template_index_nav').text()
    )

    , events: {
        'tap .navs .nav li.url' : 'gotoRouter'
    }

    , init: function(options){
        var me = this;

        me.isFirstLoad = true;

        me.render.call(me);
       
    }

    , render: function(){
        var me = this;

        me.$el.append(
            me.template({})
        ).show();
        
        me._initAnimate.call(me);
        
        return me;
    }
    
    
    , registerEvents: function(){
        var me = this, ec = me.ec;
        ec.on("pagebeforechange", me.onpagebeforechange, me);
    }
    
    , onpagebeforechange: function(params){
        var 
              me = this
            , from = params.from
            , to = params.to
            , param = params.params
            ;
            
        if(to == me.ec){
            me.$el.show();
        }
    }


    
    
    , _initAnimate : function(){
        var me = this;

        me._createSlider.call(me);

        me.$el.find('li.url').highlight('active');
        
    }
    
    , _createSlider : function(){
        var 
              me                   = this
            , outList              = me.$el.find('.outList')
            , identifyLI           = me.$el.find('.identify li')
            ;

        me.slider = outList.slider({
              showArr  : false
            , autoPlay : false
            , slideend : function(e,page){
                identifyLI.removeClass('on').eq(page).addClass('on');
            }
        });
        
        me.$el.find('.navs-opts .prev').tap(function(){
            outList.slider('pre');
        });
        me.$el.find('.navs-opts .next').tap(function(){
            outList.slider('next');
        });
    }
    
    , gotoRouter : function(e){
        var 
              me     = this
            , el     = $(e.target).closest('li.url')
            , route  = el.data('url')
            ;

        Backbone.history.navigate(route, {trigger:true});
    }

});

})(Zepto);



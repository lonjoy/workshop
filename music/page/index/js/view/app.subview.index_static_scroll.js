
(function($) {

rocket.subview.index_static_scroll = rocket.subview.extend({
      el: "#index_page_scroll"

    , template: _.template(
        $('#template_index_scroll').text()
    )


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
        
        
        me.initAnimation.call(me);
        
        return me;
    }
    
    , registerEvents: function(){
        var me = this, ec = me.ec;

        ec.on('pagebeforechange', me.onpagebeforechange, me);
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

    
    
    , initAnimation : function(){
        var me = this;
        me._createSlider.call(me);
    }
    
    , _createSlider : function(){
        var 
              me             = this
            , swipe          = me.$el.find('.swipe')
            , identify       = me.$el.find('.identify')
            ;
        me.slider = me.$el.find('.swipe ul').slider({
              loop     : true
            , showArr  : false
            , slideend : function(e,page){
                identify.find('li').removeClass('on').eq(page).addClass('on');
            }
        }); 
    }

});

})(Zepto);



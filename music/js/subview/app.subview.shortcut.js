
(function($) {

app.subview.shortcut = app.subview.extend({

      el: "#footer"

    , template: _.template(
        $('#t-shortcut').text()
    )

    , events: {}

    , init: function(options){
        var me = this;
        me.render.call(me);
    }

    , render: function(){
        var me = this;

        me.$el.html(
            me.template({
                options : me.options
            })
        );
        
       
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
        

        if(to == me.ec) {
            me.$el.show();
            $('#footer-index').hide();
        }
    }

});

})(Zepto);



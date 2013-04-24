
(function($) {

app.pageview.categorydetail = app.pageview.extend({
      el: "#categorydetail_page"

    , init: function(options){
        var me = this;

        me.setup(new app.subview.categorydetail_header(options, me));

        me.setup(new app.subview.categorydetail_content(options, me));
        
        me.setup(new app.subview.shortcut({}, me));
    }
    

});

})(Zepto);



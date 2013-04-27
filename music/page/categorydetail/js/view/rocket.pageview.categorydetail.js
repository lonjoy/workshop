
(function($) {

rocket.pageview.categorydetail = rocket.pageview.extend({
      el: "#categorydetail_page"

    , init: function(options){
        var me = this;

        me.setup(new rocket.subview.categorydetail_header(options, me));

        me.setup(new rocket.subview.categorydetail_content(options, me));
        
        me.setup(new rocket.subview.shortcut({}, me));
    }
    

});

})(Zepto);



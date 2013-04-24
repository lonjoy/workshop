
(function($) {

app.pageview.artistdetail = app.pageview.extend({
    el: "#artistdetail_page"

    ,init: function(options){
        var me = this;


        me.setup(new app.subview.artistdetail_content(options, me));
        
    }
});

})(Zepto);



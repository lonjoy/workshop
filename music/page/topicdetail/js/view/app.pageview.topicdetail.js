
(function($) {

app.pageview.topicdetail = app.pageview.extend({
      el: "#topicdetail_page"

    , init: function(options){
        var me = this;


        me.setup(new app.subview.topicdetail_content(options, me));

        me.setup(new app.subview.shortcut({}, me));
    }
    

});

})(Zepto);



(function($){

rocket.model.categorydetail_music = rocket.model.extend({

    initialize: function(attributes, options){
        var me = this;
        me.options = options;
    }

    ,url: function(){
        var me = this;
        
        return _.template('/music/categorydetail.php?tag=<%= tag %>&<%= time %>')({
              time : (new Date()).getTime()
            , tag  : me.options.id
        });
    }

    ,parse: function(resp, xhr){
        return resp.taginfo;
    }

});

})(Zepto);

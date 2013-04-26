(function($){

rocket.collection.category_music = rocket.collection.extend({

    initialize: function(models, options){
        var me = this;
    }

    ,url: function(){
        var me = this;
        
        return _.template('/music/category.json?<%= time %>')({
            time : (new Date()).getTime()
        });
        
    }

    ,parse: function(resp, xhr){
        return resp.content;
    }

});

})(Zepto);

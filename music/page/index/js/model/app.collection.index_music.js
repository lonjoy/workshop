(function($){

app.collection.index_music = app.collection.extend({

    initialize: function(models, options){
        var me = this;
        
        me.options = options;
    }

    ,url: function(){
        var me = this;
        
        return _.template('/music/ad.json?<%= time %>')({
            time : (new Date()).getTime()
        });
    }

    ,parse: function(resp, xhr){
        return resp.content;
    }

});

})(Zepto);

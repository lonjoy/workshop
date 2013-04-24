(function($){

app.model.topic_music = app.model.extend({

    initialize: function(models, options){
        var me = this;
        
        me.options = options;
    }
    
    , defaults : {
        page : 0
    }
    
    ,url: function(){
        return _.template('/music/topic.php?<%= date %>')({
            date : (new Date()).getTime()
        });
    }

    ,parse: function(resp, xhr){
        return resp.albumList;
    }

});

})(Zepto);

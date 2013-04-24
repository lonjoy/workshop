(function($){

app.model.artist_music = app.model.extend({

    initialize: function(models, options){
        var me = this;
        me.options = options;
    }
    
    , defaults : {
        page : 0
    }
    
    ,url: function(){
        var me = this;
        return _.template('/music/artist.php?<%= date %>')({

            date   : (new Date()).getTime()
        });
    }

    ,parse: function(resp, xhr){
        return resp.artist;
    }

});

})(Zepto);

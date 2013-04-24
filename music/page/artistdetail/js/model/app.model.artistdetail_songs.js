(function($){

app.model.artistdetail_songs = app.model.extend({

    initialize: function(models, options){
        var me = this;
        
        me.options = options;
    }

    ,url: function(){
        var me = this;
        
        return _.template('/music/artistsongs.php?<%= date %>')({
            date : (new Date()).getTime()
        });
    }

    ,parse: function(resp, xhr){
        return resp.songlist;
    }

});

})(Zepto);

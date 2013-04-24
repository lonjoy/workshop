(function($){

app.model.musicnew_music = app.model.extend({

    initialize: function(attributes, options){
        var me = this;
        me.options = options;
    }
    
    , defaults : {
        page : 0
    }
    
    , url: function(){
        var me = this;
        return _.template('/music/musicnew.php?<%= date %>')({

            date   : (new Date()).getTime()
        });
        
    }

    , parse: function(resp, xhr){
        return resp.song_list;
    }

});

})(Zepto);

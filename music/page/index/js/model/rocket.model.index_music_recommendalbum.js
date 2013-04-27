(function($){

rocket.model.index_music_recommendalbum = rocket.model.extend({

    initialize: function(attributes, options){
        var me = this;
        
        me.options = options;
    }

    ,url: function(){
        var me = this;
        
        return _.template('/music/recommendalbum.php?<%= time %>')({
            time : (new Date()).getTime()
        });
        
    }

    ,parse: function(resp, xhr){
        return resp.plaze_album_list.RM.album_list.list;
    }

});

})(Zepto);

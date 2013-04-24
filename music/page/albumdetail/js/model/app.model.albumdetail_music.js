(function($){

app.model.albumdetail_music = app.model.extend({

    initialize: function(attributes, options){
        var me = this;
        me.options = options;
    }

    ,url: function(){
        var me = this;
        
        return _.template('/music/albumdetail.php?id=<%= id %>&<%= date %>')({
              date : (new Date()).getTime()
            , id   : me.options.id
        });
    }

    ,parse: function(resp, xhr){
        return resp;
    }

});

})(Zepto);

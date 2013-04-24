(function($){

app.model.topicdetail_music = app.model.extend({

    initialize: function(attributes, options){
        var me = this;
        me.options = options;
    }

    ,url: function(){
        var me = this;
        
        return _.template('/music/topicdetail.php?code=<%= code %>&<%= date %>')({
              code : me.options.id
            , date : (new Date()).getTime()
        });
    }

    ,parse: function(resp, xhr){
        return resp;
    }

});

})(Zepto);

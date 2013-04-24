(function($){

app.model.artistdetail_info = app.model.extend({

    initialize: function(attributes, options){
        var me = this;
        me.options = options;
    }

    ,url: function(){
        var me = this;
        
        return _.template('/music/getInfo.php?<%= date %>')({
            date : (new Date()).getTime()
        });
    }

    ,parse: function(resp, xhr){
        return resp;
    }

});

})(Zepto);

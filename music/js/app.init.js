(function($) {

$.extend(app, {
    init: function() {

        app.$globalLoading = $('#wrapper .global-loading');
        app.$pageLoading = $('#wrapper .page-loading');

        new app.router.vs();
        Backbone.history.start();

        function scroll(e){
            $(document.body).height(600);

            setTimeout(function(){
                window.scrollTo(0, 0);
                $.later(function(){
                    $(document.body).height($(window).height());
                });
                app.isLoaded = true;
            }, 1000); 

        }

        $(function(e){
            scroll();
        });
        
        $(window).bind('orientationchange',function(){
            
            window.scrollTo(0, window.scrollY);
            
        });

    }
    
    /**
     * 列表页面"载入更多"伪进度条
     *
     */
    , loadingMore : function( el ){
        var el = el,ret;
        ret = {
            show : function(){
                el.html('<img src="/static/music/img/loadingmore.gif" />');
            }
            ,hide : function(){
                el.html('查看更多');
            }
        }
        
        
        return ret;
    }
});

})(Zepto);    


(function($) {

    app.pageanimation_fade = {};

    /**
     * 过门动画
     * @param currentEle 当前需要移走的元素
     * @param nextEle 需要移入的元素
     * @param dir 动画方向，0:无方向， 1:向左， 2:向右
     * @param callback 动画完成后的回调函数
     */
    app.pageanimation_fade.animate = function(currentEle, nextEle, dir, callback) {

        var $currentEle = currentEle && $(currentEle),
            $nextEle = nextEle && $(nextEle);

        if(currentEle != nextEle) {
            if(!currentEle){
                $nextEle.show();
            }
            else{
                $currentEle.hide();
                $.later(function(){
                    $nextEle.show();
                    // @note: 从非0值开始，避免全白页面让用户感觉闪眼
                    $nextEle.css({opacity: 0.05});
                    $nextEle.animate({opacity: 1}, 300, 'ease-in', callback);
                });
            }
        }

        return;
        
    };

})(Zepto);


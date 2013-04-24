<?php return array (
  'resource' => 
  array (
    '6fcea829' => ';/**
 * app根命名空间
 */
(function($) {

window.app = window.app || {};

})(Zepto);




',
    '11c77af2' => ';/**
 * View基类，控制展现逻辑，充当控制器的角色
 */
(function($) {

app.baseview = Backbone.View.extend({
    events: {}

    // 子类入口，子类可重写
    ,init: function(options) {}

    // 初始化函数
    ,initialize: function(options, parentView){
        var me = this;

        // 父级view
        me.parent = parentView || null;

        // 子视图列表
        me.children = {};

        // 设置id
        me.id = me.getId();

        // 当前子视图，deprecated
        // me.currentChild = null;

        // 子视图元素容器
        me.$childrenContainer = this.$el;

        // 页面事件中心
        me.ec = this.getRoot();

        // 子页面（subpage）管理相关
        me._subpages = [];
        me._currentSubpage = null;
        me.MAX_SUBPAGES = 3;

        // 保留关键字设置为对象属性
        me._config(options || {});

        // loading元素
        // 全局loading
        me.$globalLoading = app.$globalLoading;
        // 页面loading
        me.$pageLoading = app.$pageLoading;

        // 子类初始化方法
        me.init(options);

        // 事件注册
        me.registerEvents();
    }

    ,getId: function() {
        return this.cid;
    }

    ,_config: function(options) {
        var me = this, viewOptions = [];

        // @todo: 貌似houpeng实现的有问题
        for (var i = 0, len = viewOptions.length; i < len; i++) {
            var attr = viewOptions[i];
            if (typeof options[attr] != "undefined") {
                me[attr] = options[attr];
            }
        }
    }

    ,hasChild: function(view) {
        return _.include(this.children, view);
    }

    ,getChild: function(key) {
        return this.children[key];
    }

    ,notice: function(text) {
        var container = $("#notification");
        container.find("span").text(text);
        container.show();
        $.later(function(){
            container.animate({"opacity":0}, 500, "", function(){
                var $el = $(this);
                $el.hide();
                $el.css({"-webkit-transition": "none", "opacity":1});
            });
        }, 1500);
    }

    // 获取根view
    ,getRoot: function(){
        var me = this, p, c;
        p = c = me;

        while(p){
            c = p;
            p = p.parent;
        }
        return c;
    }

    // 展示loading
    ,showLoading: function(wrapper){
        var me = this;
        wrapper && $(wrapper).append(me.$pageLoading);
        me.$pageLoading.show();

        // 隐藏全局loading
        me.$globalLoading.hide();
    }

    // 隐藏loading
    ,hideLoading: function(time){
        var me = this;

        $.later(function(){
            me.$pageLoading.remove();
        }, time === undefined ? 300 : time);
    }

    // append到父节点
    ,append: function(view) {
        this._addSubview(view);
    }

    // prepend到父节点
    ,prepend: function(view) {
        this._addSubview(view, \'PREPEND\');
    }

    // setup到父节点
    ,setup: function(view) {
        this._addSubview(view, \'SETUP\');
    }

    /**
     * 添加子视图
     * @param {string} APPEND, PREPEND, SETUP. default: APPEND
     */
    ,_addSubview: function(view, type) {
        var me = this;
        if(view instanceof app.baseview) {
            me.children[view.id] = view;
            view.parent = me;

            switch(type){
                case \'SETUP\':
                    break;
                case \'PREPEND\':
                    me.$childrenContainer.prepend(view.$el);
                    break;
                default:
                    me.$childrenContainer.append(view.$el);
                    break;
            }
            // 默认不展示
            view.$el.hide();
        }
        else {
            throw new Error("app.view.append arguments must be an instance of app.view");
        }
    }

    ,destroy: function() {
        var me = this;
        // 递归销毁子视图
        for(var key in me.children) {
            me.children[key].destroy();
        }

        // unbind 已注册事件
        me.unregisterEvents();
        me.undelegateEvents();

        // 从DOM中删除该本元素
        this.$el.remove();

        // 从内存中删除引用
        me.el = me.$el = null;

        // 从父级中删除本view
        if(me.parent) {
            // me.parent.children = delete(me.parent.children[child.id]);
            delete me.parent.children[me.id];

            // todo: 从subpages里清除
        }
    }

    // 全局事件注册，子类重写之
    ,registerEvents: function(){}

    // 取消事件注册，子类重写之
    ,unregisterEvents: function(){}

    /**
     * 注册子页面
     * @param name 子页面名称，用以唯一标记子页面
     * @param subpage 子页面，app.subview实例
     */
    ,registerSubpage: function(name, subpage){
        var me = this;
        if(!me.getSubpage(name)){
            me._subpages.push({
                name: name,
                subpage: subpage
            });
        }
    }

    /**
     * 获取子页面
     * @param name 子页面名称，用以唯一标记子页面
     * @return app.subview实例或者undefined
     */
    ,getSubpage: function(name){
        var me = this, 
            p = me._subpages;

        for(var i=0, len=p.length; i<len; i++){
            if(p[i].name == name){
                return p[i].subpage;
            }
        }
        return;
    }

    /**
     * 设置当前子页面 
     */
    ,setCurrentSubpage: function(subpage){
        var me = this;
        if(subpage instanceof app.baseview){
            me._currentSubpage = subpage;
        }
        else{
            throw Error(\'error in method setCurrentSubpage: \'
                + \'subpage is not an instance of app.baseview\');
        }
    }

    /**
     * 回收子页面
     * @todo: 回收算法求精
     */
    ,recycleSubpage: function(){
        var me = this, 
            p = me._subpages,
            item;

        while(p.length > me.MAX_SUBPAGES){
            item = p.shift();

            // 不回收当前活动子页面
            if(item.subpage == me._currentSubpage){
                me._subpages.push(item); 
            }
            else{
                item.subpage.destroy();
            }
        }

    }

    /**
     * 调整高度，使用算法避免频繁调整，特别针对iOS5以下版本使用iScroll的情况
     * @note: 比如页面内有很多图片资源的情况
     * @todo: 是否可用$.debounce
     */
    ,refreshScrollerHeight: function(params){
        var me = this,
            now = (new Date()).getTime(),
            stack;

        if(!app.isLoaded){
            setTimeout(function(){
                me.refreshScrollerHeight();
            }, 200);
            return;
        }

        me.refreshRequestStack = me.refreshRequestStack || [];
        me.lastRefreshTime = me.lastRefreshTime || now; 
        stack = me.refreshRequestStack;

        if(now - me.lastRefreshTime < 1000 && me.isNotFirstRefresh){
            // 添加请求
            stack.push(1);
            return;
        }

        // 清空请求列表
        stack.length = 0;
        setTimeout(function(){
            me.refreshHeight && me.refreshHeight();
            me.lastRefreshTime = (new Date()).getTime();

            // 清理漏网之鱼
            setTimeout(function(){
                // 有刷新请求，但没有执行的，清理之
                if(stack.length > 0){
                    // console.log(\'clear refresh ...\');
                    stack.length = 0;
                    me.refreshHeight && me.refreshHeight();
                }
            }, 1000);
        }, 0);

        me.isNotFirstRefresh = true;
    }

});

})(Zepto);



',
    '507d9a88' => ';/**
 * Collection类
 */
(function($) {

app.collection = Backbone.Collection.extend({
    initialize: function(models, options){
        // 页面事件中心
        // this.ec = this.getRoot();
    }

    // 获取页面控制器
    // ,getRoot: function(){
    //     return app.view.prototype.getRoot.apply(this, arguments);
    // }

});

})(Zepto);


',
    '791c56ab' => ';
/**
 * Model类
 */
(function($) {

app.model = Backbone.Model.extend({
    initialize: function(attributes, options){

        // 页面事件中心
        // this.ec = this.getRoot();
    }

    // 获取页面控制器
    // ,getRoot: function(){
    //     return app.view.prototype.getRoot.apply(this, arguments);
    // }

});

})(Zepto);


',
    '07bd1804' => ';/**
 * pageview类，页面视图控制器，充当页面事件中心
 */
(function($) {

app.pageview = app.baseview.extend({

    // 初始化函数
    initialize: function(options, action){
        var me = this;

        // 页面对应action
        if(!action){
            throw Error(\'pageview creation: must supply non-empty action parameter\'); 
        }
        me.action = action;

        app.baseview.prototype.initialize.call(me, options, null);
    }

    ,savePos: function(){
        this._top = window.scrollY;
    }

    ,restorePos: function(){
        var me = this;
        // @note: iOS4需要延时
        setTimeout(function(){
            window.scrollTo(0, me._top || 0);
        }, 0);
    }

});

})(Zepto);




',
    'cc16d384' => ';/**
 * Router类，监听URL变化，并作转发
 * 产品线需继承app.router类
 */
(function($) {

app.router = Backbone.Router.extend({

    // 实例化时自动调用
    initialize: function() {
        // 保存的视图列表，对应不同页面
        this.views = {};

        // 记录控制器变化
        this.currentView = null;
        this.previousView = null;
    },

    /**
     * 路由配置
     * 按照Backbone.Router指定方式配置，例子如下，该部分产品线定义
     */
    routes: {
        /*
        "": "index",
        "index/:type": "index",
        "page/:src/:title": "page",
        "search/:word": "search",
        */
    },

    /** 
     * 页面切换顺序配置
     * 产品线按以下格式配置，使用action名称
     */
    pageOrder: [/*\'index\', \'search\', \'page\'*/],

    /**
     * 默认页面切换动画，合理选择配置
     * @note: slide比较适用于固高切换
     * @note: fade比较适用DOM树较小的两个页面切换
     * @note: simple性能最好，但效果最一般
     */
    defaultPageTransition: \'simple\',
    
    /**
     * 页面切换动画配置
     * @key {string} actionname-actionname，"-"号分隔的action名称串，不分先后，但支持精确设定
     * @value {string} animation name
     * @note: 以index和search为例，有两种可设定的值：index-search和search-index：
     *     1. 如果只设定了其中一个，则不分先后顺序同时生效。比如\'index-search\':\'fade\'，无论index->search还是search->index，切换动画总是fade
     *     2. 如果两个都设定了，则分别生效。比如\'index-search\':\'fade\'，\'search-index\':\'slide\'，那么index->search使用fade动画，search->index使用slide动画
     *     3. 如果两个都没有设定，则都是用默认动画
     */
    pageTransition: {
        // \'index-search\': \'fade\'
        // ,\'index-page\': \'slide\'
    },

    /**
     * Hander，对应action index的处理方法。产品线定义
     * 以下为例子
     */

    /*
    index: function(type) {
        this.doAction(\'index\', {
            type: decodeURIComponent(type)
        });
    },

    page: function(src, title) {
        this.doAction(\'page\', {
            src: decodeURIComponent(src),
            title: decodeURIComponent(title)
        });
    },

    search: function(word) {
        this.doAction(\'search\', {
            word: decodeURIComponent(word)
        });
    },
    */

    /**
     * action通用处理逻辑
     * @{param} action {string} action名称
     * @{param} params {object} action参数
     */
    doAction: function(action, params){
        var me = this, view = me.views[action];
        
        if(!view){
            view = me.views[action] 
                = new app.pageview[action](params, action); 
        } 
        
        // 切换视图控制器
        me.previousView = me.currentView;
        me.currentView = view;

        me.switchPage(
            me.previousView, 
            me.currentView, 
            params
        );
    },

    /**
     * 通用切换页面逻辑
     * @{param} from {app.pageview}
     * @{param} to {app.pageview}
     * @{param} params {object}
     */
    switchPage: function(from, to, params){
        var me = this;

        var dir = 0, order = me.pageOrder, 
            fromAction = from && from.action || null,
            toAction = to && to.action || null,
            fromIndex, toIndex;

        /**
         * 计算页面切换方向：0-无方向，1-向左，2-向右
         */
        if(fromAction !== null && null !== toAction && fromAction !== toAction){
            if(-1 != ( fromIndex = order.indexOf( fromAction ) )
                && -1 != ( toIndex = order.indexOf( toAction ) ) ){
                dir = fromIndex > toIndex ? 2 : 1;
            }
        }

        // console.log([fromAction, toAction, dir].join(\' | \'));

        // 记忆位置
        me.enablePositionRestore && from && (from.savePos());

        $.each(from == to ? [from] : [from, to], function(key, item){
            item && item.trigger(\'pagebeforechange subpagebeforechange\', {
                from: me.previousView, 
                to: me.currentView,
                params: params 
            });
        });
        
        me.doAnimation(
            from,
            to,
            dir,
            function(){
                /**
                 * 尽可能等切换稳定了再开始数据请求
                 * 延后一点用户感觉不出来，但能保证页面的稳定性
                 */

                // 恢复位置
                me.enablePositionRestore && to && (to.restorePos());

                $.each(from == to ? [from] : [from, to], function(key, item){
                    item && item.trigger(
                        \'pageafterchange subpageafterchange\', {
                            from: me.previousView, 
                            to: me.currentView,
                            params: params 
                        });
                });
            }
        );

    },

    /**
     * 选择相应切换动画并执行
     * @param fromView
     * @param toView
     * @param direction
     * @param callback
     */
    doAnimation: function(fromView, toView, direction, callback){

        var animation, me = this;

        // 根据action组合，选择不同切换动画方法
        animate = me._selectAnimation(
                fromView && fromView.action || null, 
                toView && toView.action || null
            ) || app[\'pageanimation_\' + me.defaultPageTransition].animate; 

        animate(
            fromView && fromView.el, 
            toView && toView.el, 
            direction,
            callback
        );

    },

    /**
     * 根据action组合选择相应切换动画
     * @param fromAction
     * @param toAction
     * @return 切换动画方法 or undefined
     */
    _selectAnimation: function(fromAction, toAction){

        if(null == fromAction || null == toAction){
            return;
        }

        var me = this,
            animateName;

        // key不分顺序，需要试探两种顺序的配置
        animateName = me.pageTransition[fromAction + \'-\' + toAction]
            || me.pageTransition[toAction + \'-\' + fromAction];

        return app[\'pageanimation_\' + animateName] 
            && app[\'pageanimation_\' + animateName].animate;

    }

}); 

})(Zepto);



',
    'c87b05d7' => ';/**
 * subview类，页面子视图控制器
 */
(function($) {

app.subview = app.baseview.extend({

    // 初始化函数
    initialize: function(options, parentView){
        if(parentView instanceof app.baseview){
            app.baseview.prototype.initialize.call(this, options, parentView);
        }
        else{
            throw Error(\'app.subview creation: must supply parentView, which is an instance of app.baseview\');
        }
    }

});

})(Zepto);




',
    '4f048737' => ';(function($) {

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
                    $nextEle.animate({opacity: 1}, 300, \'ease-in\', callback);
                });
            }
        }

        return;
        
    };

})(Zepto);

',
    'b0d09efb' => ';(function($) {

    app.pageanimation_simple = {};

    /**
     * 过门动画
     * @param currentEle 当前需要移走的元素
     * @param nextEle 需要移入的元素
     * @param dir 动画方向，0:无方向， 1:向左， 2:向右
     * @param callback 动画完成后的回调函数
     */
    app.pageanimation_simple.animate = function(currentEle, nextEle, dir, callback) {

        var $currentEle = currentEle && $(currentEle),
            $nextEle = nextEle && $(nextEle);

        if(currentEle != nextEle) {
            if(!currentEle){
                $nextEle.show();
                callback && callback();
            }
            else{
                $currentEle.hide();
                $.later(function(){
                    $nextEle.show();
                    callback && callback();
                });
            }
        }

        return;
        
    };

})(Zepto);


',
    'b1307b24' => ';(function($) {

    app.pageanimation_slide = {};

    function generateTransform(x, y, z) {
        return "translate" + (app.has3d ? "3d" : "") + "(" + x + "px, " + y + "px" + (app.has3d ? (", " + z + "px)") : ")");
    };

    /**
     * 过门动画
     * @param currentEle 当前需要移走的元素
     * @param nextEle 需要移入的元素
     * @param dir 动画方向，0:无方向， 1:向左， 2:向右
     * @param restore 是否恢复原位置
     * @param callback 动画完成后的回调函数
     */
    app.pageanimation_slide.animate = function(
        currentEle, nextEle, dir, 
        callback, restore) {

        if(dir === 0) {
            if(currentEle != nextEle) {
                // @note: 先隐藏当前，避免当前页面残留，确保切换效果
                currentEle && $(currentEle).hide();
                $.later(function(){
                    nextEle && $(nextEle).show();
                });
            }

            callback && callback();
            return;
        }

        // 由于多种动画混杂，必须进行位置恢复
        restore = true;

        // 准备位置
        nextEle = $(nextEle);
        currentEle = $(currentEle);
        
        var clientWidth = document.documentElement.clientWidth;

        currentEle.css({
            "-webkit-transition-property": "-webkit-transform",
            "-webkit-transform": generateTransform(0, 0, 0), 
            "-webkit-transition-duration": "0ms",
            "-webkit-transition-timing-function": "ease-out",
            "-webkit-transition-delay": "initial",
        });
        nextEle.css({
            "-webkit-transition-property": "-webkit-transform",
            "-webkit-transform": generateTransform((dir === 1 ? "" : "-") + clientWidth, 0, 0), 
            "-webkit-transition-duration": "0ms",
            "-webkit-transition-timing-function": "ease-out",
            "-webkit-transition-delay": "initial",
            "display": "block",
        });

        var that = this;
        setTimeout(function() {

            var ready = 0;

            function endNextTransition() {
                nextEle.off(\'webkitTransitionEnd\', arguments.callee);
                ready++;

                if(2 == ready){
                    endAllTransition();
                    callback && callback();
                }
            }

            function endCurrentTransition() {
                currentEle.off(\'webkitTransitionEnd\', arguments.callee);
                ready++;

                if(2 == ready){
                    endAllTransition();
                    callback && callback();
                }
            }

            nextEle.on(\'webkitTransitionEnd\', endNextTransition);
            currentEle.on(\'webkitTransitionEnd\', endCurrentTransition);

            function endAllTransition(){

                // 是否恢复原状，子页面切换使用
                if(restore){
                    currentEle.css({
                        "display": "none",
                        "-webkit-transform": generateTransform(0, 0, 0), 
                        "-webkit-transition-duration": "0ms"
                    });
                    nextEle.css({
                        "display": "block",
                        "-webkit-transform": generateTransform(0, 0, 0), 
                        "-webkit-transition-duration": "0ms",
                    });
                }
                else{
                    currentEle.css({
                        "display": "none",
                    });
                    nextEle.css({
                        "display": "block",
                    });
                }
            }

            // 开始动画
            nextEle.css({
                "-webkit-transform": generateTransform(0, 0, 0), 
                "-webkit-transition-duration": "350ms",
            });

            currentEle.css({
                "-webkit-transform": generateTransform((dir === 1 ? "-" : "") + clientWidth, 0, 0), 
                "-webkit-transition-duration": "350ms",
            });

        }, 0);
        
    };

})(Zepto);

',
  ),
);
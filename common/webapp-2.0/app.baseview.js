/**
 * View基类，控制展现逻辑，充当控制器的角色
 */
(function($) {

rocket.baseview = Backbone.View.extend({
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
        me.$globalLoading = rocket.$globalLoading;
        // 页面loading
        me.$pageLoading = rocket.$pageLoading;

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
        this._addSubview(view, 'PREPEND');
    }

    // setup到父节点
    ,setup: function(view) {
        this._addSubview(view, 'SETUP');
    }

    /**
     * 添加子视图
     * @param {string} APPEND, PREPEND, SETUP. default: APPEND
     */
    ,_addSubview: function(view, type) {
        var me = this;
        if(view instanceof rocket.baseview) {
            me.children[view.id] = view;
            view.parent = me;

            switch(type){
                case 'SETUP':
                    break;
                case 'PREPEND':
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
            throw new Error("rocket.view.append arguments must be an instance of rocket.view");
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
     * @param subpage 子页面，rocket.subview实例
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
     * @return rocket.subview实例或者undefined
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
        if(subpage instanceof rocket.baseview){
            me._currentSubpage = subpage;
        }
        else{
            throw Error('error in method setCurrentSubpage: '
                + 'subpage is not an instance of rocket.baseview');
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

        if(!rocket.isLoaded){
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
                    // console.log('clear refresh ...');
                    stack.length = 0;
                    me.refreshHeight && me.refreshHeight();
                }
            }, 1000);
        }, 0);

        me.isNotFirstRefresh = true;
    }

});

})(Zepto);




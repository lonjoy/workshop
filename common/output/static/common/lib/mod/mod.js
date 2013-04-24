(function(){
    var F = window.F || {'version' : 'mod_1.0.0', 'debug' : false};

//模块类
    function Module(name){
        // 模块名，在define时指定
        this.name = name;
        // 模块函数
        this.fn = null;
        // 模块对象
        this.exports = {};
        // 保存实例，用于单实例判断
        Module.cache[this.name] = this;
    }

// 模块实例
    Module.cache = {};
    Module.requiredPaths = {};
    Module.lazyLoadPaths = {};

//模块prototype定义
    Module.prototype = {
        //初始化，执行factory函数
        init : function(){
            if(!this.fn) {
                throw new Error('Module "' + this.name + '" not found!');
            }
            var result;
            if(result = this.fn.call(null, require, this.exports)) {
                this.exports = result;
            }
            return this;
        },
        //加载模块
        load : function(){
            var name = this.name;
            if(Module.lazyLoadPaths[name]) {
                this.init();
                delete Module.lazyLoadPaths[name];
            } else {
                Module.requiredPaths[this.name] = true;
                //todo 根据path把组件从localStorage中取出,目前默认app启动时会把所有组件已经加载好...
            }
            return this;
        }
    };
//实现模块的require方法
    function require(name){
        return get(name).load().exports;
    }

//根据名称和路径获取模块实例
    function get(name){
        if(Module.cache[name]) {
            return Module.cache[name];
        }
        return new Module(name);
    }

    /**
     * 指定一个或多个模块名，待模块加载完成后执行回调函数，并将模块对象依次传递给函数作为参数。
     * @function
     * @public
     * @name F.use
     * @grammar F.use(moduleName, callback)
     * @param {String|Array} names 模块名
     * @param {Function} fn 回调函数
     * @version 1.0
     */
    F.use = function(names, fn){
        if(typeof names === 'string') {
            names = [names];
        }
        var args = [];
        for(var i = 0, l = names.length; i < l; i++) {
            args[i] = require(names[i]);
        }
        if(fn) {
            fn.apply(null, args);
        }
    };
    /**
     * 声明一个模块。
     * 一个模块的名字需要符合以下规范：
     * <ol>
     * <li>框架基础模块名，是模块js文件路径截去前面"http://...lib/"和文件名"name.js";</li>
     * <li>用户模块名，需要以一个前缀来表示属于自己的模块，比如模块名"ps/common/name",其中"ps"表示该模块是自己的，同时在源文件配置文件中定义模块对应的存放路径；"common/name"部分为文件路径的目录名称;</li>
     * <li>每个模块对应的js文件路径为"模块根目录/模块层级目录/模块名目录/模块名.js"</li>
     * </ul>
     *
     * @function
     * @public
     * @name F.module
     * @grammar F.module(name, fn)
     * @param {String} name 模块名
     * @param {Function} fn 模块定义函数，有两个参数分别为"require","exports"。"require"是一个函数，用来引用其他模块；"exports"是一个对象，模块函数最终将模块的api挂载到exports这个对象上，作为模块对外的输出唯一对象。</li>
     * @version 1.0
     */
    F.module = function(name, fn){
        var mod = get(name);
        mod.fn = fn;
        if(Module.requiredPaths[name]) {
            mod.init();
        } else {
            Module.lazyLoadPaths[name] = true;
        }
    };

    var _data = {};
    /**
     * 存取数据对象
     *
     * @function
     * @public
     * @name F.context
     * @grammar F.context(key, value) 或 F.context(key) 或 F.context({key: value});
     * @param {String|Object}
        * @param {All}
        * @version 1.0
     * @example
     *
     * <code>
     *   //存值
     *   F.context('username', 'walter');
     *
     *   //存值
     *   F.context({
     *       'username': 'walter',
     *       'job': 'no'
     *   });
     *
     *   //取值
     *   F.context('username');
     * </code>

     */
    F.context = function(key, value){
        var length = arguments.length;
        if(length > 1) {
            _data[key] = value;
        } else if(length == 1) {
            if(typeof key == 'object') {
                for(var k in key) {
                    if(key.hasOwnProperty(k)) {
                        _data[k] = key[k];
                    }
                }
            } else {
                return _data[key];
            }
        }
    };

    'F' in window || (window.F = F);
})();
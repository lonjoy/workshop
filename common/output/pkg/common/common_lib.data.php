<?php return array (
  'resource' => 
  array (
    '4dae1057' => 'html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;outline:0;font-size:100%;font:inherit;vertical-align:baseline;-webkit-text-size-adjust:none;-webkit-tap-highlight-color:rgba(0,0,0,0);}html,body,form,fieldset,p,div,h1,h2,h3,h4,h5,h6{-webkit-text-size-adjust:none;-webkit-user-select:none;}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block;}body{font-family:arial,sans-serif;}ol,ul{list-style:none;}blockquote,q{quotes:none;}blockquote:before,blockquote:after,q:before,q:after{content:\'\';content:none;}ins{text-decoration:none;}del{text-decoration:line-through;}table{border-collapse:collapse;border-spacing:0;}',
    '69b48709' => ';/* Zepto v1.0-1-ga3cab6c - polyfill zepto detect event ajax form fx - zeptojs.com/license */


;(function(undefined){
  if (String.prototype.trim === undefined) // fix for iOS 3.2
    String.prototype.trim = function(){ return this.replace(/^\\s+|\\s+$/g, \'\') }

  // For iOS 3.x
  // from https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/reduce
  if (Array.prototype.reduce === undefined)
    Array.prototype.reduce = function(fun){
      if(this === void 0 || this === null) throw new TypeError()
      var t = Object(this), len = t.length >>> 0, k = 0, accumulator
      if(typeof fun != \'function\') throw new TypeError()
      if(len == 0 && arguments.length == 1) throw new TypeError()

      if(arguments.length >= 2)
       accumulator = arguments[1]
      else
        do{
          if(k in t){
            accumulator = t[k++]
            break
          }
          if(++k >= len) throw new TypeError()
        } while (true)

      while (k < len){
        if(k in t) accumulator = fun.call(undefined, accumulator, t[k], k, t)
        k++
      }
      return accumulator
    }

})()

var Zepto = (function() {
  var undefined, key, $, classList, emptyArray = [], slice = emptyArray.slice, filter = emptyArray.filter,
    document = window.document,
    elementDisplay = {}, classCache = {},
    getComputedStyle = document.defaultView.getComputedStyle,
    cssNumber = { \'column-count\': 1, \'columns\': 1, \'font-weight\': 1, \'line-height\': 1,\'opacity\': 1, \'z-index\': 1, \'zoom\': 1 },
    fragmentRE = /^\\s*<(\\w+|!)[^>]*>/,
    tagExpanderRE = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\\w:]+)[^>]*)\\/>/ig,
    rootNodeRE = /^(?:body|html)$/i,

    // special attributes that should be get/set via method calls
    methodAttributes = [\'val\', \'css\', \'html\', \'text\', \'data\', \'width\', \'height\', \'offset\'],

    adjacencyOperators = [ \'after\', \'prepend\', \'before\', \'append\' ],
    table = document.createElement(\'table\'),
    tableRow = document.createElement(\'tr\'),
    containers = {
      \'tr\': document.createElement(\'tbody\'),
      \'tbody\': table, \'thead\': table, \'tfoot\': table,
      \'td\': tableRow, \'th\': tableRow,
      \'*\': document.createElement(\'div\')
    },
    readyRE = /complete|loaded|interactive/,
    classSelectorRE = /^\\.([\\w-]+)$/,
    idSelectorRE = /^#([\\w-]*)$/,
    tagSelectorRE = /^[\\w-]+$/,
    class2type = {},
    toString = class2type.toString,
    zepto = {},
    camelize, uniq,
    tempParent = document.createElement(\'div\')

  zepto.matches = function(element, selector) {
    if (!element || element.nodeType !== 1) return false
    var matchesSelector = element.webkitMatchesSelector || element.mozMatchesSelector ||
                          element.oMatchesSelector || element.matchesSelector
    if (matchesSelector) return matchesSelector.call(element, selector)
    // fall back to performing a selector:
    var match, parent = element.parentNode, temp = !parent
    if (temp) (parent = tempParent).appendChild(element)
    match = ~zepto.qsa(parent, selector).indexOf(element)
    temp && tempParent.removeChild(element)
    return match
  }

  function type(obj) {
    return obj == null ? String(obj) :
      class2type[toString.call(obj)] || "object"
  }

  function isFunction(value) { return type(value) == "function" }
  function isWindow(obj)     { return obj != null && obj == obj.window }
  function isDocument(obj)   { return obj != null && obj.nodeType == obj.DOCUMENT_NODE }
  function isObject(obj)     { return type(obj) == "object" }
  function isPlainObject(obj) {
    return isObject(obj) && !isWindow(obj) && obj.__proto__ == Object.prototype
  }
  function isArray(value) { return value instanceof Array }
  function likeArray(obj) { return typeof obj.length == \'number\' }

  function compact(array) { return filter.call(array, function(item){ return item != null }) }
  function flatten(array) { return array.length > 0 ? $.fn.concat.apply([], array) : array }
  camelize = function(str){ return str.replace(/-+(.)?/g, function(match, chr){ return chr ? chr.toUpperCase() : \'\' }) }
  function dasherize(str) {
    return str.replace(/::/g, \'/\')
           .replace(/([A-Z]+)([A-Z][a-z])/g, \'$1_$2\')
           .replace(/([a-z\\d])([A-Z])/g, \'$1_$2\')
           .replace(/_/g, \'-\')
           .toLowerCase()
  }
  uniq = function(array){ return filter.call(array, function(item, idx){ return array.indexOf(item) == idx }) }

  function classRE(name) {
    return name in classCache ?
      classCache[name] : (classCache[name] = new RegExp(\'(^|\\\\s)\' + name + \'(\\\\s|$)\'))
  }

  function maybeAddPx(name, value) {
    return (typeof value == "number" && !cssNumber[dasherize(name)]) ? value + "px" : value
  }

  function defaultDisplay(nodeName) {
    var element, display
    if (!elementDisplay[nodeName]) {
      element = document.createElement(nodeName)
      document.body.appendChild(element)
      display = getComputedStyle(element, \'\').getPropertyValue("display")
      element.parentNode.removeChild(element)
      display == "none" && (display = "block")
      elementDisplay[nodeName] = display
    }
    return elementDisplay[nodeName]
  }

  function children(element) {
    return \'children\' in element ?
      slice.call(element.children) :
      $.map(element.childNodes, function(node){ if (node.nodeType == 1) return node })
  }

  // `$.zepto.fragment` takes a html string and an optional tag name
  // to generate DOM nodes nodes from the given html string.
  // The generated DOM nodes are returned as an array.
  // This function can be overriden in plugins for example to make
  // it compatible with browsers that don\'t support the DOM fully.
  zepto.fragment = function(html, name, properties) {
    if (html.replace) html = html.replace(tagExpanderRE, "<$1></$2>")
    if (name === undefined) name = fragmentRE.test(html) && RegExp.$1
    if (!(name in containers)) name = \'*\'

    var nodes, dom, container = containers[name]
    container.innerHTML = \'\' + html
    dom = $.each(slice.call(container.childNodes), function(){
      container.removeChild(this)
    })
    if (isPlainObject(properties)) {
      nodes = $(dom)
      $.each(properties, function(key, value) {
        if (methodAttributes.indexOf(key) > -1) nodes[key](value)
        else nodes.attr(key, value)
      })
    }
    return dom
  }

  // `$.zepto.Z` swaps out the prototype of the given `dom` array
  // of nodes with `$.fn` and thus supplying all the Zepto functions
  // to the array. Note that `__proto__` is not supported on Internet
  // Explorer. This method can be overriden in plugins.
  zepto.Z = function(dom, selector) {
    dom = dom || []
    dom.__proto__ = $.fn
    dom.selector = selector || \'\'
    return dom
  }

  // `$.zepto.isZ` should return `true` if the given object is a Zepto
  // collection. This method can be overriden in plugins.
  zepto.isZ = function(object) {
    return object instanceof zepto.Z
  }

  // `$.zepto.init` is Zepto\'s counterpart to jQuery\'s `$.fn.init` and
  // takes a CSS selector and an optional context (and handles various
  // special cases).
  // This method can be overriden in plugins.
  zepto.init = function(selector, context) {
    // If nothing given, return an empty Zepto collection
    if (!selector) return zepto.Z()
    // If a function is given, call it when the DOM is ready
    else if (isFunction(selector)) return $(document).ready(selector)
    // If a Zepto collection is given, juts return it
    else if (zepto.isZ(selector)) return selector
    else {
      var dom
      // normalize array if an array of nodes is given
      if (isArray(selector)) dom = compact(selector)
      // Wrap DOM nodes. If a plain object is given, duplicate it.
      else if (isObject(selector))
        dom = [isPlainObject(selector) ? $.extend({}, selector) : selector], selector = null
      // If it\'s a html fragment, create nodes from it
      else if (fragmentRE.test(selector))
        dom = zepto.fragment(selector.trim(), RegExp.$1, context), selector = null
      // If there\'s a context, create a collection on that context first, and select
      // nodes from there
      else if (context !== undefined) return $(context).find(selector)
      // And last but no least, if it\'s a CSS selector, use it to select nodes.
      else dom = zepto.qsa(document, selector)
      // create a new Zepto collection from the nodes found
      return zepto.Z(dom, selector)
    }
  }

  // `$` will be the base `Zepto` object. When calling this
  // function just call `$.zepto.init, which makes the implementation
  // details of selecting nodes and creating Zepto collections
  // patchable in plugins.
  $ = function(selector, context){
    return zepto.init(selector, context)
  }

  function extend(target, source, deep) {
    for (key in source)
      if (deep && (isPlainObject(source[key]) || isArray(source[key]))) {
        if (isPlainObject(source[key]) && !isPlainObject(target[key]))
          target[key] = {}
        if (isArray(source[key]) && !isArray(target[key]))
          target[key] = []
        extend(target[key], source[key], deep)
      }
      else if (source[key] !== undefined) target[key] = source[key]
  }

  // Copy all but undefined properties from one or more
  // objects to the `target` object.
  $.extend = function(target){
    var deep, args = slice.call(arguments, 1)
    if (typeof target == \'boolean\') {
      deep = target
      target = args.shift()
    }
    args.forEach(function(arg){ extend(target, arg, deep) })
    return target
  }

  // `$.zepto.qsa` is Zepto\'s CSS selector implementation which
  // uses `document.querySelectorAll` and optimizes for some special cases, like `#id`.
  // This method can be overriden in plugins.
  zepto.qsa = function(element, selector){
    var found
    return (isDocument(element) && idSelectorRE.test(selector)) ?
      ( (found = element.getElementById(RegExp.$1)) ? [found] : [] ) :
      (element.nodeType !== 1 && element.nodeType !== 9) ? [] :
      slice.call(
        classSelectorRE.test(selector) ? element.getElementsByClassName(RegExp.$1) :
        tagSelectorRE.test(selector) ? element.getElementsByTagName(selector) :
        element.querySelectorAll(selector)
      )
  }

  function filtered(nodes, selector) {
    return selector === undefined ? $(nodes) : $(nodes).filter(selector)
  }

  $.contains = function(parent, node) {
    return parent !== node && parent.contains(node)
  }

  function funcArg(context, arg, idx, payload) {
    return isFunction(arg) ? arg.call(context, idx, payload) : arg
  }

  function setAttribute(node, name, value) {
    value == null ? node.removeAttribute(name) : node.setAttribute(name, value)
  }

  // access className property while respecting SVGAnimatedString
  function className(node, value){
    var klass = node.className,
        svg   = klass && klass.baseVal !== undefined

    if (value === undefined) return svg ? klass.baseVal : klass
    svg ? (klass.baseVal = value) : (node.className = value)
  }

  // "true"  => true
  // "false" => false
  // "null"  => null
  // "42"    => 42
  // "42.5"  => 42.5
  // JSON    => parse if valid
  // String  => self
  function deserializeValue(value) {
    var num
    try {
      return value ?
        value == "true" ||
        ( value == "false" ? false :
          value == "null" ? null :
          !isNaN(num = Number(value)) ? num :
          /^[\\[\\{]/.test(value) ? $.parseJSON(value) :
          value )
        : value
    } catch(e) {
      return value
    }
  }

  $.type = type
  $.isFunction = isFunction
  $.isWindow = isWindow
  $.isArray = isArray
  $.isPlainObject = isPlainObject

  $.isEmptyObject = function(obj) {
    var name
    for (name in obj) return false
    return true
  }

  $.inArray = function(elem, array, i){
    return emptyArray.indexOf.call(array, elem, i)
  }

  $.camelCase = camelize
  $.trim = function(str) { return str.trim() }

  // plugin compatibility
  $.uuid = 0
  $.support = { }
  $.expr = { }

  $.map = function(elements, callback){
    var value, values = [], i, key
    if (likeArray(elements))
      for (i = 0; i < elements.length; i++) {
        value = callback(elements[i], i)
        if (value != null) values.push(value)
      }
    else
      for (key in elements) {
        value = callback(elements[key], key)
        if (value != null) values.push(value)
      }
    return flatten(values)
  }

  $.each = function(elements, callback){
    var i, key
    if (likeArray(elements)) {
      for (i = 0; i < elements.length; i++)
        if (callback.call(elements[i], i, elements[i]) === false) return elements
    } else {
      for (key in elements)
        if (callback.call(elements[key], key, elements[key]) === false) return elements
    }

    return elements
  }

  $.grep = function(elements, callback){
    return filter.call(elements, callback)
  }

  if (window.JSON) $.parseJSON = JSON.parse

  // Populate the class2type map
  $.each("Boolean Number String Function Array Date RegExp Object Error".split(" "), function(i, name) {
    class2type[ "[object " + name + "]" ] = name.toLowerCase()
  })

  // Define methods that will be available on all
  // Zepto collections
  $.fn = {
    // Because a collection acts like an array
    // copy over these useful array functions.
    forEach: emptyArray.forEach,
    reduce: emptyArray.reduce,
    push: emptyArray.push,
    sort: emptyArray.sort,
    indexOf: emptyArray.indexOf,
    concat: emptyArray.concat,

    // `map` and `slice` in the jQuery API work differently
    // from their array counterparts
    map: function(fn){
      return $($.map(this, function(el, i){ return fn.call(el, i, el) }))
    },
    slice: function(){
      return $(slice.apply(this, arguments))
    },

    ready: function(callback){
      if (readyRE.test(document.readyState)) callback($)
      else document.addEventListener(\'DOMContentLoaded\', function(){ callback($) }, false)
      return this
    },
    get: function(idx){
      return idx === undefined ? slice.call(this) : this[idx >= 0 ? idx : idx + this.length]
    },
    toArray: function(){ return this.get() },
    size: function(){
      return this.length
    },
    remove: function(){
      return this.each(function(){
        if (this.parentNode != null)
          this.parentNode.removeChild(this)
      })
    },
    each: function(callback){
      emptyArray.every.call(this, function(el, idx){
        return callback.call(el, idx, el) !== false
      })
      return this
    },
    filter: function(selector){
      if (isFunction(selector)) return this.not(this.not(selector))
      return $(filter.call(this, function(element){
        return zepto.matches(element, selector)
      }))
    },
    add: function(selector,context){
      return $(uniq(this.concat($(selector,context))))
    },
    is: function(selector){
      return this.length > 0 && zepto.matches(this[0], selector)
    },
    not: function(selector){
      var nodes=[]
      if (isFunction(selector) && selector.call !== undefined)
        this.each(function(idx){
          if (!selector.call(this,idx)) nodes.push(this)
        })
      else {
        var excludes = typeof selector == \'string\' ? this.filter(selector) :
          (likeArray(selector) && isFunction(selector.item)) ? slice.call(selector) : $(selector)
        this.forEach(function(el){
          if (excludes.indexOf(el) < 0) nodes.push(el)
        })
      }
      return $(nodes)
    },
    has: function(selector){
      return this.filter(function(){
        return isObject(selector) ?
          $.contains(this, selector) :
          $(this).find(selector).size()
      })
    },
    eq: function(idx){
      return idx === -1 ? this.slice(idx) : this.slice(idx, + idx + 1)
    },
    first: function(){
      var el = this[0]
      return el && !isObject(el) ? el : $(el)
    },
    last: function(){
      var el = this[this.length - 1]
      return el && !isObject(el) ? el : $(el)
    },
    find: function(selector){
      var result, $this = this
      if (typeof selector == \'object\')
        result = $(selector).filter(function(){
          var node = this
          return emptyArray.some.call($this, function(parent){
            return $.contains(parent, node)
          })
        })
      else if (this.length == 1) result = $(zepto.qsa(this[0], selector))
      else result = this.map(function(){ return zepto.qsa(this, selector) })
      return result
    },
    closest: function(selector, context){
      var node = this[0], collection = false
      if (typeof selector == \'object\') collection = $(selector)
      while (node && !(collection ? collection.indexOf(node) >= 0 : zepto.matches(node, selector)))
        node = node !== context && !isDocument(node) && node.parentNode
      return $(node)
    },
    parents: function(selector){
      var ancestors = [], nodes = this
      while (nodes.length > 0)
        nodes = $.map(nodes, function(node){
          if ((node = node.parentNode) && !isDocument(node) && ancestors.indexOf(node) < 0) {
            ancestors.push(node)
            return node
          }
        })
      return filtered(ancestors, selector)
    },
    parent: function(selector){
      return filtered(uniq(this.pluck(\'parentNode\')), selector)
    },
    children: function(selector){
      return filtered(this.map(function(){ return children(this) }), selector)
    },
    contents: function() {
      return this.map(function() { return slice.call(this.childNodes) })
    },
    siblings: function(selector){
      return filtered(this.map(function(i, el){
        return filter.call(children(el.parentNode), function(child){ return child!==el })
      }), selector)
    },
    empty: function(){
      return this.each(function(){ this.innerHTML = \'\' })
    },
    // `pluck` is borrowed from Prototype.js
    pluck: function(property){
      return $.map(this, function(el){ return el[property] })
    },
    show: function(){
      return this.each(function(){
        this.style.display == "none" && (this.style.display = null)
        if (getComputedStyle(this, \'\').getPropertyValue("display") == "none")
          this.style.display = defaultDisplay(this.nodeName)
      })
    },
    replaceWith: function(newContent){
      return this.before(newContent).remove()
    },
    wrap: function(structure){
      var func = isFunction(structure)
      if (this[0] && !func)
        var dom   = $(structure).get(0),
            clone = dom.parentNode || this.length > 1

      return this.each(function(index){
        $(this).wrapAll(
          func ? structure.call(this, index) :
            clone ? dom.cloneNode(true) : dom
        )
      })
    },
    wrapAll: function(structure){
      if (this[0]) {
        $(this[0]).before(structure = $(structure))
        var children
        // drill down to the inmost element
        while ((children = structure.children()).length) structure = children.first()
        $(structure).append(this)
      }
      return this
    },
    wrapInner: function(structure){
      var func = isFunction(structure)
      return this.each(function(index){
        var self = $(this), contents = self.contents(),
            dom  = func ? structure.call(this, index) : structure
        contents.length ? contents.wrapAll(dom) : self.append(dom)
      })
    },
    unwrap: function(){
      this.parent().each(function(){
        $(this).replaceWith($(this).children())
      })
      return this
    },
    clone: function(){
      return this.map(function(){ return this.cloneNode(true) })
    },
    hide: function(){
      return this.css("display", "none")
    },
    toggle: function(setting){
      return this.each(function(){
        var el = $(this)
        ;(setting === undefined ? el.css("display") == "none" : setting) ? el.show() : el.hide()
      })
    },
    prev: function(selector){ return $(this.pluck(\'previousElementSibling\')).filter(selector || \'*\') },
    next: function(selector){ return $(this.pluck(\'nextElementSibling\')).filter(selector || \'*\') },
    html: function(html){
      return html === undefined ?
        (this.length > 0 ? this[0].innerHTML : null) :
        this.each(function(idx){
          var originHtml = this.innerHTML
          $(this).empty().append( funcArg(this, html, idx, originHtml) )
        })
    },
    text: function(text){
      return text === undefined ?
        (this.length > 0 ? this[0].textContent : null) :
        this.each(function(){ this.textContent = text })
    },
    attr: function(name, value){
      var result
      return (typeof name == \'string\' && value === undefined) ?
        (this.length == 0 || this[0].nodeType !== 1 ? undefined :
          (name == \'value\' && this[0].nodeName == \'INPUT\') ? this.val() :
          (!(result = this[0].getAttribute(name)) && name in this[0]) ? this[0][name] : result
        ) :
        this.each(function(idx){
          if (this.nodeType !== 1) return
          if (isObject(name)) for (key in name) setAttribute(this, key, name[key])
          else setAttribute(this, name, funcArg(this, value, idx, this.getAttribute(name)))
        })
    },
    removeAttr: function(name){
      return this.each(function(){ this.nodeType === 1 && setAttribute(this, name) })
    },
    prop: function(name, value){
      return (value === undefined) ?
        (this[0] && this[0][name]) :
        this.each(function(idx){
          this[name] = funcArg(this, value, idx, this[name])
        })
    },
    data: function(name, value){
      var data = this.attr(\'data-\' + dasherize(name), value)
      return data !== null ? deserializeValue(data) : undefined
    },
    val: function(value){
      return (value === undefined) ?
        (this[0] && (this[0].multiple ?
           $(this[0]).find(\'option\').filter(function(o){ return this.selected }).pluck(\'value\') :
           this[0].value)
        ) :
        this.each(function(idx){
          this.value = funcArg(this, value, idx, this.value)
        })
    },
    offset: function(coordinates){
      if (coordinates) return this.each(function(index){
        var $this = $(this),
            coords = funcArg(this, coordinates, index, $this.offset()),
            parentOffset = $this.offsetParent().offset(),
            props = {
              top:  coords.top  - parentOffset.top,
              left: coords.left - parentOffset.left
            }

        if ($this.css(\'position\') == \'static\') props[\'position\'] = \'relative\'
        $this.css(props)
      })
      if (this.length==0) return null
      var obj = this[0].getBoundingClientRect()
      return {
        left: obj.left + window.pageXOffset,
        top: obj.top + window.pageYOffset,
        width: Math.round(obj.width),
        height: Math.round(obj.height)
      }
    },
    css: function(property, value){
      if (arguments.length < 2 && typeof property == \'string\')
        return this[0] && (this[0].style[camelize(property)] || getComputedStyle(this[0], \'\').getPropertyValue(property))

      var css = \'\'
      if (type(property) == \'string\') {
        if (!value && value !== 0)
          this.each(function(){ this.style.removeProperty(dasherize(property)) })
        else
          css = dasherize(property) + ":" + maybeAddPx(property, value)
      } else {
        for (key in property)
          if (!property[key] && property[key] !== 0)
            this.each(function(){ this.style.removeProperty(dasherize(key)) })
          else
            css += dasherize(key) + \':\' + maybeAddPx(key, property[key]) + \';\'
      }

      return this.each(function(){ this.style.cssText += \';\' + css })
    },
    index: function(element){
      return element ? this.indexOf($(element)[0]) : this.parent().children().indexOf(this[0])
    },
    hasClass: function(name){
      return emptyArray.some.call(this, function(el){
        return this.test(className(el))
      }, classRE(name))
    },
    addClass: function(name){
      return this.each(function(idx){
        classList = []
        var cls = className(this), newName = funcArg(this, name, idx, cls)
        newName.split(/\\s+/g).forEach(function(klass){
          if (!$(this).hasClass(klass)) classList.push(klass)
        }, this)
        classList.length && className(this, cls + (cls ? " " : "") + classList.join(" "))
      })
    },
    removeClass: function(name){
      return this.each(function(idx){
        if (name === undefined) return className(this, \'\')
        classList = className(this)
        funcArg(this, name, idx, classList).split(/\\s+/g).forEach(function(klass){
          classList = classList.replace(classRE(klass), " ")
        })
        className(this, classList.trim())
      })
    },
    toggleClass: function(name, when){
      return this.each(function(idx){
        var $this = $(this), names = funcArg(this, name, idx, className(this))
        names.split(/\\s+/g).forEach(function(klass){
          (when === undefined ? !$this.hasClass(klass) : when) ?
            $this.addClass(klass) : $this.removeClass(klass)
        })
      })
    },
    scrollTop: function(){
      if (!this.length) return
      return (\'scrollTop\' in this[0]) ? this[0].scrollTop : this[0].scrollY
    },
    position: function() {
      if (!this.length) return

      var elem = this[0],
        // Get *real* offsetParent
        offsetParent = this.offsetParent(),
        // Get correct offsets
        offset       = this.offset(),
        parentOffset = rootNodeRE.test(offsetParent[0].nodeName) ? { top: 0, left: 0 } : offsetParent.offset()

      // Subtract element margins
      // note: when an element has margin: auto the offsetLeft and marginLeft
      // are the same in Safari causing offset.left to incorrectly be 0
      offset.top  -= parseFloat( $(elem).css(\'margin-top\') ) || 0
      offset.left -= parseFloat( $(elem).css(\'margin-left\') ) || 0

      // Add offsetParent borders
      parentOffset.top  += parseFloat( $(offsetParent[0]).css(\'border-top-width\') ) || 0
      parentOffset.left += parseFloat( $(offsetParent[0]).css(\'border-left-width\') ) || 0

      // Subtract the two offsets
      return {
        top:  offset.top  - parentOffset.top,
        left: offset.left - parentOffset.left
      }
    },
    offsetParent: function() {
      return this.map(function(){
        var parent = this.offsetParent || document.body
        while (parent && !rootNodeRE.test(parent.nodeName) && $(parent).css("position") == "static")
          parent = parent.offsetParent
        return parent
      })
    }
  }

  // for now
  $.fn.detach = $.fn.remove

  // Generate the `width` and `height` functions
  ;[\'width\', \'height\'].forEach(function(dimension){
    $.fn[dimension] = function(value){
      var offset, el = this[0],
        Dimension = dimension.replace(/./, function(m){ return m[0].toUpperCase() })
      if (value === undefined) return isWindow(el) ? el[\'inner\' + Dimension] :
        isDocument(el) ? el.documentElement[\'offset\' + Dimension] :
        (offset = this.offset()) && offset[dimension]
      else return this.each(function(idx){
        el = $(this)
        el.css(dimension, funcArg(this, value, idx, el[dimension]()))
      })
    }
  })

  function traverseNode(node, fun) {
    fun(node)
    for (var key in node.childNodes) traverseNode(node.childNodes[key], fun)
  }

  // Generate the `after`, `prepend`, `before`, `append`,
  // `insertAfter`, `insertBefore`, `appendTo`, and `prependTo` methods.
  adjacencyOperators.forEach(function(operator, operatorIndex) {
    var inside = operatorIndex % 2 //=> prepend, append

    $.fn[operator] = function(){
      // arguments can be nodes, arrays of nodes, Zepto objects and HTML strings
      var argType, nodes = $.map(arguments, function(arg) {
            argType = type(arg)
            return argType == "object" || argType == "array" || arg == null ?
              arg : zepto.fragment(arg)
          }),
          parent, copyByClone = this.length > 1
      if (nodes.length < 1) return this

      return this.each(function(_, target){
        parent = inside ? target : target.parentNode

        // convert all methods to a "before" operation
        target = operatorIndex == 0 ? target.nextSibling :
                 operatorIndex == 1 ? target.firstChild :
                 operatorIndex == 2 ? target :
                 null

        nodes.forEach(function(node){
          if (copyByClone) node = node.cloneNode(true)
          else if (!parent) return $(node).remove()

          traverseNode(parent.insertBefore(node, target), function(el){
            if (el.nodeName != null && el.nodeName.toUpperCase() === \'SCRIPT\' &&
               (!el.type || el.type === \'text/javascript\') && !el.src)
              window[\'eval\'].call(window, el.innerHTML)
          })
        })
      })
    }

    // after    => insertAfter
    // prepend  => prependTo
    // before   => insertBefore
    // append   => appendTo
    $.fn[inside ? operator+\'To\' : \'insert\'+(operatorIndex ? \'Before\' : \'After\')] = function(html){
      $(html)[operator](this)
      return this
    }
  })

  zepto.Z.prototype = $.fn

  // Export internal API functions in the `$.zepto` namespace
  zepto.uniq = uniq
  zepto.deserializeValue = deserializeValue
  $.zepto = zepto

  return $
})()

window.Zepto = Zepto
\'$\' in window || (window.$ = Zepto)

;(function($){
  function detect(ua){
    var os = this.os = {}, browser = this.browser = {},
      webkit = ua.match(/WebKit\\/([\\d.]+)/),
      android = ua.match(/(Android)\\s+([\\d.]+)/),
      ipad = ua.match(/(iPad).*OS\\s([\\d_]+)/),
      iphone = !ipad && ua.match(/(iPhone\\sOS)\\s([\\d_]+)/),
      webos = ua.match(/(webOS|hpwOS)[\\s\\/]([\\d.]+)/),
      touchpad = webos && ua.match(/TouchPad/),
      kindle = ua.match(/Kindle\\/([\\d.]+)/),
      silk = ua.match(/Silk\\/([\\d._]+)/),
      blackberry = ua.match(/(BlackBerry).*Version\\/([\\d.]+)/),
      bb10 = ua.match(/(BB10).*Version\\/([\\d.]+)/),
      rimtabletos = ua.match(/(RIM\\sTablet\\sOS)\\s([\\d.]+)/),
      playbook = ua.match(/PlayBook/),
      chrome = ua.match(/Chrome\\/([\\d.]+)/) || ua.match(/CriOS\\/([\\d.]+)/),
      firefox = ua.match(/Firefox\\/([\\d.]+)/)

    // Todo: clean this up with a better OS/browser seperation:
    // - discern (more) between multiple browsers on android
    // - decide if kindle fire in silk mode is android or not
    // - Firefox on Android doesn\'t specify the Android version
    // - possibly devide in os, device and browser hashes

    if (browser.webkit = !!webkit) browser.version = webkit[1]

    if (android) os.android = true, os.version = android[2]
    if (iphone) os.ios = os.iphone = true, os.version = iphone[2].replace(/_/g, \'.\')
    if (ipad) os.ios = os.ipad = true, os.version = ipad[2].replace(/_/g, \'.\')
    if (webos) os.webos = true, os.version = webos[2]
    if (touchpad) os.touchpad = true
    if (blackberry) os.blackberry = true, os.version = blackberry[2]
    if (bb10) os.bb10 = true, os.version = bb10[2]
    if (rimtabletos) os.rimtabletos = true, os.version = rimtabletos[2]
    if (playbook) browser.playbook = true
    if (kindle) os.kindle = true, os.version = kindle[1]
    if (silk) browser.silk = true, browser.version = silk[1]
    if (!silk && os.android && ua.match(/Kindle Fire/)) browser.silk = true
    if (chrome) browser.chrome = true, browser.version = chrome[1]
    if (firefox) browser.firefox = true, browser.version = firefox[1]

    os.tablet = !!(ipad || playbook || (android && !ua.match(/Mobile/)) || (firefox && ua.match(/Tablet/)))
    os.phone  = !!(!os.tablet && (android || iphone || webos || blackberry || bb10 ||
      (chrome && ua.match(/Android/)) || (chrome && ua.match(/CriOS\\/([\\d.]+)/)) || (firefox && ua.match(/Mobile/))))
  }

  detect.call($, navigator.userAgent)
  // make available to unit tests
  $.__detect = detect

})(Zepto)

;(function($){
  var $$ = $.zepto.qsa, handlers = {}, _zid = 1, specialEvents={},
      hover = { mouseenter: \'mouseover\', mouseleave: \'mouseout\' }

  specialEvents.click = specialEvents.mousedown = specialEvents.mouseup = specialEvents.mousemove = \'MouseEvents\'

  function zid(element) {
    return element._zid || (element._zid = _zid++)
  }
  function findHandlers(element, event, fn, selector) {
    event = parse(event)
    if (event.ns) var matcher = matcherFor(event.ns)
    return (handlers[zid(element)] || []).filter(function(handler) {
      return handler
        && (!event.e  || handler.e == event.e)
        && (!event.ns || matcher.test(handler.ns))
        && (!fn       || zid(handler.fn) === zid(fn))
        && (!selector || handler.sel == selector)
    })
  }
  function parse(event) {
    var parts = (\'\' + event).split(\'.\')
    return {e: parts[0], ns: parts.slice(1).sort().join(\' \')}
  }
  function matcherFor(ns) {
    return new RegExp(\'(?:^| )\' + ns.replace(\' \', \' .* ?\') + \'(?: |$)\')
  }

  function eachEvent(events, fn, iterator){
    if ($.type(events) != "string") $.each(events, iterator)
    else events.split(/\\s/).forEach(function(type){ iterator(type, fn) })
  }

  function eventCapture(handler, captureSetting) {
    return handler.del &&
      (handler.e == \'focus\' || handler.e == \'blur\') ||
      !!captureSetting
  }

  function realEvent(type) {
    return hover[type] || type
  }

  function add(element, events, fn, selector, getDelegate, capture){
    var id = zid(element), set = (handlers[id] || (handlers[id] = []))
    eachEvent(events, fn, function(event, fn){
      var handler   = parse(event)
      handler.fn    = fn
      handler.sel   = selector
      // emulate mouseenter, mouseleave
      if (handler.e in hover) fn = function(e){
        var related = e.relatedTarget
        if (!related || (related !== this && !$.contains(this, related)))
          return handler.fn.apply(this, arguments)
      }
      handler.del   = getDelegate && getDelegate(fn, event)
      var callback  = handler.del || fn
      handler.proxy = function (e) {
        var result = callback.apply(element, [e].concat(e.data))
        if (result === false) e.preventDefault(), e.stopPropagation()
        return result
      }
      handler.i = set.length
      set.push(handler)
      element.addEventListener(realEvent(handler.e), handler.proxy, eventCapture(handler, capture))
    })
  }
  function remove(element, events, fn, selector, capture){
    var id = zid(element)
    eachEvent(events || \'\', fn, function(event, fn){
      findHandlers(element, event, fn, selector).forEach(function(handler){
        delete handlers[id][handler.i]
        element.removeEventListener(realEvent(handler.e), handler.proxy, eventCapture(handler, capture))
      })
    })
  }

  $.event = { add: add, remove: remove }

  $.proxy = function(fn, context) {
    if ($.isFunction(fn)) {
      var proxyFn = function(){ return fn.apply(context, arguments) }
      proxyFn._zid = zid(fn)
      return proxyFn
    } else if (typeof context == \'string\') {
      return $.proxy(fn[context], fn)
    } else {
      throw new TypeError("expected function")
    }
  }

  $.fn.bind = function(event, callback){
    return this.each(function(){
      add(this, event, callback)
    })
  }
  $.fn.unbind = function(event, callback){
    return this.each(function(){
      remove(this, event, callback)
    })
  }
  $.fn.one = function(event, callback){
    return this.each(function(i, element){
      add(this, event, callback, null, function(fn, type){
        return function(){
          var result = fn.apply(element, arguments)
          remove(element, type, fn)
          return result
        }
      })
    })
  }

  var returnTrue = function(){return true},
      returnFalse = function(){return false},
      ignoreProperties = /^([A-Z]|layer[XY]$)/,
      eventMethods = {
        preventDefault: \'isDefaultPrevented\',
        stopImmediatePropagation: \'isImmediatePropagationStopped\',
        stopPropagation: \'isPropagationStopped\'
      }
  function createProxy(event) {
    var key, proxy = { originalEvent: event }
    for (key in event)
      if (!ignoreProperties.test(key) && event[key] !== undefined) proxy[key] = event[key]

    $.each(eventMethods, function(name, predicate) {
      proxy[name] = function(){
        this[predicate] = returnTrue
        return event[name].apply(event, arguments)
      }
      proxy[predicate] = returnFalse
    })
    return proxy
  }

  // emulates the \'defaultPrevented\' property for browsers that have none
  function fix(event) {
    if (!(\'defaultPrevented\' in event)) {
      event.defaultPrevented = false
      var prevent = event.preventDefault
      event.preventDefault = function() {
        this.defaultPrevented = true
        prevent.call(this)
      }
    }
  }

  $.fn.delegate = function(selector, event, callback){
    return this.each(function(i, element){
      add(element, event, callback, selector, function(fn){
        return function(e){
          var evt, match = $(e.target).closest(selector, element).get(0)
          if (match) {
            evt = $.extend(createProxy(e), {currentTarget: match, liveFired: element})
            return fn.apply(match, [evt].concat([].slice.call(arguments, 1)))
          }
        }
      })
    })
  }
  $.fn.undelegate = function(selector, event, callback){
    return this.each(function(){
      remove(this, event, callback, selector)
    })
  }

  $.fn.live = function(event, callback){
    $(document.body).delegate(this.selector, event, callback)
    return this
  }
  $.fn.die = function(event, callback){
    $(document.body).undelegate(this.selector, event, callback)
    return this
  }

  $.fn.on = function(event, selector, callback){
    return !selector || $.isFunction(selector) ?
      this.bind(event, selector || callback) : this.delegate(selector, event, callback)
  }
  $.fn.off = function(event, selector, callback){
    return !selector || $.isFunction(selector) ?
      this.unbind(event, selector || callback) : this.undelegate(selector, event, callback)
  }

  $.fn.trigger = function(event, data){
    if (typeof event == \'string\' || $.isPlainObject(event)) event = $.Event(event)
    fix(event)
    event.data = data
    return this.each(function(){
      // items in the collection might not be DOM elements
      // (todo: possibly support events on plain old objects)
      if(\'dispatchEvent\' in this) this.dispatchEvent(event)
    })
  }

  // triggers event handlers on current element just as if an event occurred,
  // doesn\'t trigger an actual event, doesn\'t bubble
  $.fn.triggerHandler = function(event, data){
    var e, result
    this.each(function(i, element){
      e = createProxy(typeof event == \'string\' ? $.Event(event) : event)
      e.data = data
      e.target = element
      $.each(findHandlers(element, event.type || event), function(i, handler){
        result = handler.proxy(e)
        if (e.isImmediatePropagationStopped()) return false
      })
    })
    return result
  }

  // shortcut methods for `.bind(event, fn)` for each event type
  ;(\'focusin focusout load resize scroll unload click dblclick \'+
  \'mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave \'+
  \'change select keydown keypress keyup error\').split(\' \').forEach(function(event) {
    $.fn[event] = function(callback) {
      return callback ?
        this.bind(event, callback) :
        this.trigger(event)
    }
  })

  ;[\'focus\', \'blur\'].forEach(function(name) {
    $.fn[name] = function(callback) {
      if (callback) this.bind(name, callback)
      else this.each(function(){
        try { this[name]() }
        catch(e) {}
      })
      return this
    }
  })

  $.Event = function(type, props) {
    if (typeof type != \'string\') props = type, type = props.type
    var event = document.createEvent(specialEvents[type] || \'Events\'), bubbles = true
    if (props) for (var name in props) (name == \'bubbles\') ? (bubbles = !!props[name]) : (event[name] = props[name])
    event.initEvent(type, bubbles, true, null, null, null, null, null, null, null, null, null, null, null, null)
    event.isDefaultPrevented = function(){ return this.defaultPrevented }
    return event
  }

})(Zepto)

;(function($){
  var jsonpID = 0,
      document = window.document,
      key,
      name,
      rscript = /<script\\b[^<]*(?:(?!<\\/script>)<[^<]*)*<\\/script>/gi,
      scriptTypeRE = /^(?:text|application)\\/javascript/i,
      xmlTypeRE = /^(?:text|application)\\/xml/i,
      jsonType = \'application/json\',
      htmlType = \'text/html\',
      blankRE = /^\\s*$/

  // trigger a custom event and return false if it was cancelled
  function triggerAndReturn(context, eventName, data) {
    var event = $.Event(eventName)
    $(context).trigger(event, data)
    return !event.defaultPrevented
  }

  // trigger an Ajax "global" event
  function triggerGlobal(settings, context, eventName, data) {
    if (settings.global) return triggerAndReturn(context || document, eventName, data)
  }

  // Number of active Ajax requests
  $.active = 0

  function ajaxStart(settings) {
    if (settings.global && $.active++ === 0) triggerGlobal(settings, null, \'ajaxStart\')
  }
  function ajaxStop(settings) {
    if (settings.global && !(--$.active)) triggerGlobal(settings, null, \'ajaxStop\')
  }

  // triggers an extra global event "ajaxBeforeSend" that\'s like "ajaxSend" but cancelable
  function ajaxBeforeSend(xhr, settings) {
    var context = settings.context
    if (settings.beforeSend.call(context, xhr, settings) === false ||
        triggerGlobal(settings, context, \'ajaxBeforeSend\', [xhr, settings]) === false)
      return false

    triggerGlobal(settings, context, \'ajaxSend\', [xhr, settings])
  }
  function ajaxSuccess(data, xhr, settings) {
    var context = settings.context, status = \'success\'
    settings.success.call(context, data, status, xhr)
    triggerGlobal(settings, context, \'ajaxSuccess\', [xhr, settings, data])
    ajaxComplete(status, xhr, settings)
  }
  // type: "timeout", "error", "abort", "parsererror"
  function ajaxError(error, type, xhr, settings) {
    var context = settings.context
    settings.error.call(context, xhr, type, error)
    triggerGlobal(settings, context, \'ajaxError\', [xhr, settings, error])
    ajaxComplete(type, xhr, settings)
  }
  // status: "success", "notmodified", "error", "timeout", "abort", "parsererror"
  function ajaxComplete(status, xhr, settings) {
    var context = settings.context
    settings.complete.call(context, xhr, status)
    triggerGlobal(settings, context, \'ajaxComplete\', [xhr, settings])
    ajaxStop(settings)
  }

  // Empty function, used as default callback
  function empty() {}

  $.ajaxJSONP = function(options){
    if (!(\'type\' in options)) return $.ajax(options)

    var callbackName = \'jsonp\' + (++jsonpID),
      script = document.createElement(\'script\'),
      cleanup = function() {
        clearTimeout(abortTimeout)
        $(script).remove()
        delete window[callbackName]
      },
      abort = function(type){
        cleanup()
        // In case of manual abort or timeout, keep an empty function as callback
        // so that the SCRIPT tag that eventually loads won\'t result in an error.
        if (!type || type == \'timeout\') window[callbackName] = empty
        ajaxError(null, type || \'abort\', xhr, options)
      },
      xhr = { abort: abort }, abortTimeout

    if (ajaxBeforeSend(xhr, options) === false) {
      abort(\'abort\')
      return false
    }

    window[callbackName] = function(data){
      cleanup()
      ajaxSuccess(data, xhr, options)
    }

    script.onerror = function() { abort(\'error\') }

    script.src = options.url.replace(/=\\?/, \'=\' + callbackName)
    $(\'head\').append(script)

    if (options.timeout > 0) abortTimeout = setTimeout(function(){
      abort(\'timeout\')
    }, options.timeout)

    return xhr
  }

  $.ajaxSettings = {
    // Default type of request
    type: \'GET\',
    // Callback that is executed before request
    beforeSend: empty,
    // Callback that is executed if the request succeeds
    success: empty,
    // Callback that is executed the the server drops error
    error: empty,
    // Callback that is executed on request complete (both: error and success)
    complete: empty,
    // The context for the callbacks
    context: null,
    // Whether to trigger "global" Ajax events
    global: true,
    // Transport
    xhr: function () {
      return new window.XMLHttpRequest()
    },
    // MIME types mapping
    accepts: {
      script: \'text/javascript, application/javascript\',
      json:   jsonType,
      xml:    \'application/xml, text/xml\',
      html:   htmlType,
      text:   \'text/plain\'
    },
    // Whether the request is to another domain
    crossDomain: false,
    // Default timeout
    timeout: 0,
    // Whether data should be serialized to string
    processData: true,
    // Whether the browser should be allowed to cache GET responses
    cache: true,
  }

  function mimeToDataType(mime) {
    if (mime) mime = mime.split(\';\', 2)[0]
    return mime && ( mime == htmlType ? \'html\' :
      mime == jsonType ? \'json\' :
      scriptTypeRE.test(mime) ? \'script\' :
      xmlTypeRE.test(mime) && \'xml\' ) || \'text\'
  }

  function appendQuery(url, query) {
    return (url + \'&\' + query).replace(/[&?]{1,2}/, \'?\')
  }

  // serialize payload and append it to the URL for GET requests
  function serializeData(options) {
    if (options.processData && options.data && $.type(options.data) != "string")
      options.data = $.param(options.data, options.traditional)
    if (options.data && (!options.type || options.type.toUpperCase() == \'GET\'))
      options.url = appendQuery(options.url, options.data)
  }

  $.ajax = function(options){
    var settings = $.extend({}, options || {})
    for (key in $.ajaxSettings) if (settings[key] === undefined) settings[key] = $.ajaxSettings[key]

    ajaxStart(settings)

    if (!settings.crossDomain) settings.crossDomain = /^([\\w-]+:)?\\/\\/([^\\/]+)/.test(settings.url) &&
      RegExp.$2 != window.location.host

    if (!settings.url) settings.url = window.location.toString()
    serializeData(settings)
    if (settings.cache === false) settings.url = appendQuery(settings.url, \'_=\' + Date.now())

    var dataType = settings.dataType, hasPlaceholder = /=\\?/.test(settings.url)
    if (dataType == \'jsonp\' || hasPlaceholder) {
      if (!hasPlaceholder) settings.url = appendQuery(settings.url, \'callback=?\')
      return $.ajaxJSONP(settings)
    }

    var mime = settings.accepts[dataType],
        baseHeaders = { },
        protocol = /^([\\w-]+:)\\/\\//.test(settings.url) ? RegExp.$1 : window.location.protocol,
        xhr = settings.xhr(), abortTimeout

    if (!settings.crossDomain) baseHeaders[\'X-Requested-With\'] = \'XMLHttpRequest\'
    if (mime) {
      baseHeaders[\'Accept\'] = mime
      if (mime.indexOf(\',\') > -1) mime = mime.split(\',\', 2)[0]
      xhr.overrideMimeType && xhr.overrideMimeType(mime)
    }
    if (settings.contentType || (settings.contentType !== false && settings.data && settings.type.toUpperCase() != \'GET\'))
      baseHeaders[\'Content-Type\'] = (settings.contentType || \'application/x-www-form-urlencoded\')
    settings.headers = $.extend(baseHeaders, settings.headers || {})

    xhr.onreadystatechange = function(){
      if (xhr.readyState == 4) {
        xhr.onreadystatechange = empty;
        clearTimeout(abortTimeout)
        var result, error = false
        if ((xhr.status >= 200 && xhr.status < 300) || xhr.status == 304 || (xhr.status == 0 && protocol == \'file:\')) {
          dataType = dataType || mimeToDataType(xhr.getResponseHeader(\'content-type\'))
          result = xhr.responseText

          try {
            // http://perfectionkills.com/global-eval-what-are-the-options/
            if (dataType == \'script\')    (1,eval)(result)
            else if (dataType == \'xml\')  result = xhr.responseXML
            else if (dataType == \'json\') result = blankRE.test(result) ? null : $.parseJSON(result)
          } catch (e) { error = e }

          if (error) ajaxError(error, \'parsererror\', xhr, settings)
          else ajaxSuccess(result, xhr, settings)
        } else {
          ajaxError(null, xhr.status ? \'error\' : \'abort\', xhr, settings)
        }
      }
    }

    var async = \'async\' in settings ? settings.async : true
    xhr.open(settings.type, settings.url, async)

    for (name in settings.headers) xhr.setRequestHeader(name, settings.headers[name])

    if (ajaxBeforeSend(xhr, settings) === false) {
      xhr.abort()
      return false
    }

    if (settings.timeout > 0) abortTimeout = setTimeout(function(){
        xhr.onreadystatechange = empty
        xhr.abort()
        ajaxError(null, \'timeout\', xhr, settings)
      }, settings.timeout)

    // avoid sending empty string (#319)
    xhr.send(settings.data ? settings.data : null)
    return xhr
  }

  // handle optional data/success arguments
  function parseArguments(url, data, success, dataType) {
    var hasData = !$.isFunction(data)
    return {
      url:      url,
      data:     hasData  ? data : undefined,
      success:  !hasData ? data : $.isFunction(success) ? success : undefined,
      dataType: hasData  ? dataType || success : success
    }
  }

  $.get = function(url, data, success, dataType){
    return $.ajax(parseArguments.apply(null, arguments))
  }

  $.post = function(url, data, success, dataType){
    var options = parseArguments.apply(null, arguments)
    options.type = \'POST\'
    return $.ajax(options)
  }

  $.getJSON = function(url, data, success){
    var options = parseArguments.apply(null, arguments)
    options.dataType = \'json\'
    return $.ajax(options)
  }

  $.fn.load = function(url, data, success){
    if (!this.length) return this
    var self = this, parts = url.split(/\\s/), selector,
        options = parseArguments(url, data, success),
        callback = options.success
    if (parts.length > 1) options.url = parts[0], selector = parts[1]
    options.success = function(response){
      self.html(selector ?
        $(\'<div>\').html(response.replace(rscript, "")).find(selector)
        : response)
      callback && callback.apply(self, arguments)
    }
    $.ajax(options)
    return this
  }

  var escape = encodeURIComponent

  function serialize(params, obj, traditional, scope){
    var type, array = $.isArray(obj)
    $.each(obj, function(key, value) {
      type = $.type(value)
      if (scope) key = traditional ? scope : scope + \'[\' + (array ? \'\' : key) + \']\'
      // handle data in serializeArray() format
      if (!scope && array) params.add(value.name, value.value)
      // recurse into nested objects
      else if (type == "array" || (!traditional && type == "object"))
        serialize(params, value, traditional, key)
      else params.add(key, value)
    })
  }

  $.param = function(obj, traditional){
    var params = []
    params.add = function(k, v){ this.push(escape(k) + \'=\' + escape(v)) }
    serialize(params, obj, traditional)
    return params.join(\'&\').replace(/%20/g, \'+\')
  }
})(Zepto)

;(function ($) {
  $.fn.serializeArray = function () {
    var result = [], el
    $( Array.prototype.slice.call(this.get(0).elements) ).each(function () {
      el = $(this)
      var type = el.attr(\'type\')
      if (this.nodeName.toLowerCase() != \'fieldset\' &&
        !this.disabled && type != \'submit\' && type != \'reset\' && type != \'button\' &&
        ((type != \'radio\' && type != \'checkbox\') || this.checked))
        result.push({
          name: el.attr(\'name\'),
          value: el.val()
        })
    })
    return result
  }

  $.fn.serialize = function () {
    var result = []
    this.serializeArray().forEach(function (elm) {
      result.push( encodeURIComponent(elm.name) + \'=\' + encodeURIComponent(elm.value) )
    })
    return result.join(\'&\')
  }

  $.fn.submit = function (callback) {
    if (callback) this.bind(\'submit\', callback)
    else if (this.length) {
      var event = $.Event(\'submit\')
      this.eq(0).trigger(event)
      if (!event.defaultPrevented) this.get(0).submit()
    }
    return this
  }

})(Zepto)

;(function($, undefined){
  var prefix = \'\', eventPrefix, endEventName, endAnimationName,
    vendors = { Webkit: \'webkit\', Moz: \'\', O: \'o\', ms: \'MS\' },
    document = window.document, testEl = document.createElement(\'div\'),
    supportedTransforms = /^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i,
    transform,
    transitionProperty, transitionDuration, transitionTiming,
    animationName, animationDuration, animationTiming,
    cssReset = {}

  function dasherize(str) { return downcase(str.replace(/([a-z])([A-Z])/, \'$1-$2\')) }
  function downcase(str) { return str.toLowerCase() }
  function normalizeEvent(name) { return eventPrefix ? eventPrefix + name : downcase(name) }

  $.each(vendors, function(vendor, event){
    if (testEl.style[vendor + \'TransitionProperty\'] !== undefined) {
      prefix = \'-\' + downcase(vendor) + \'-\'
      eventPrefix = event
      return false
    }
  })

  transform = prefix + \'transform\'
  cssReset[transitionProperty = prefix + \'transition-property\'] =
  cssReset[transitionDuration = prefix + \'transition-duration\'] =
  cssReset[transitionTiming   = prefix + \'transition-timing-function\'] =
  cssReset[animationName      = prefix + \'animation-name\'] =
  cssReset[animationDuration  = prefix + \'animation-duration\'] =
  cssReset[animationTiming    = prefix + \'animation-timing-function\'] = \'\'

  $.fx = {
    off: (eventPrefix === undefined && testEl.style.transitionProperty === undefined),
    speeds: { _default: 400, fast: 200, slow: 600 },
    cssPrefix: prefix,
    transitionEnd: normalizeEvent(\'TransitionEnd\'),
    animationEnd: normalizeEvent(\'AnimationEnd\')
  }

  $.fn.animate = function(properties, duration, ease, callback){
    if ($.isPlainObject(duration))
      ease = duration.easing, callback = duration.complete, duration = duration.duration
    if (duration) duration = (typeof duration == \'number\' ? duration :
                    ($.fx.speeds[duration] || $.fx.speeds._default)) / 1000
    return this.anim(properties, duration, ease, callback)
  }

  $.fn.anim = function(properties, duration, ease, callback){
    var key, cssValues = {}, cssProperties, transforms = \'\',
        that = this, wrappedCallback, endEvent = $.fx.transitionEnd

    if (duration === undefined) duration = 0.4
    if ($.fx.off) duration = 0

    if (typeof properties == \'string\') {
      // keyframe animation
      cssValues[animationName] = properties
      cssValues[animationDuration] = duration + \'s\'
      cssValues[animationTiming] = (ease || \'linear\')
      endEvent = $.fx.animationEnd
    } else {
      cssProperties = []
      // CSS transitions
      for (key in properties)
        if (supportedTransforms.test(key)) transforms += key + \'(\' + properties[key] + \') \'
        else cssValues[key] = properties[key], cssProperties.push(dasherize(key))

      if (transforms) cssValues[transform] = transforms, cssProperties.push(transform)
      if (duration > 0 && typeof properties === \'object\') {
        cssValues[transitionProperty] = cssProperties.join(\', \')
        cssValues[transitionDuration] = duration + \'s\'
        cssValues[transitionTiming] = (ease || \'linear\')
      }
    }

    wrappedCallback = function(event){
      if (typeof event !== \'undefined\') {
        if (event.target !== event.currentTarget) return // makes sure the event didn\'t bubble from "below"
        $(event.target).unbind(endEvent, wrappedCallback)
      }
      $(this).css(cssReset)
      callback && callback.call(this)
    }
    if (duration > 0) this.bind(endEvent, wrappedCallback)

    // trigger page reflow so new elements can animate
    this.size() && this.get(0).clientLeft

    this.css(cssValues)

    if (duration <= 0) setTimeout(function() {
      that.each(function(){ wrappedCallback.call(this) })
    }, 0)

    return this
  }

  testEl = null
})(Zepto)
',
    '92b35679' => ';//     Underscore.js 1.4.4
//     http://underscorejs.org
//     (c) 2009-2013 Jeremy Ashkenas, DocumentCloud Inc.
//     Underscore may be freely distributed under the MIT license.

(function() {

  // Baseline setup
  // --------------

  // Establish the root object, `window` in the browser, or `global` on the server.
  var root = this;

  // Save the previous value of the `_` variable.
  var previousUnderscore = root._;

  // Establish the object that gets returned to break out of a loop iteration.
  var breaker = {};

  // Save bytes in the minified (but not gzipped) version:
  var ArrayProto = Array.prototype, ObjProto = Object.prototype, FuncProto = Function.prototype;

  // Create quick reference variables for speed access to core prototypes.
  var push             = ArrayProto.push,
      slice            = ArrayProto.slice,
      concat           = ArrayProto.concat,
      toString         = ObjProto.toString,
      hasOwnProperty   = ObjProto.hasOwnProperty;

  // All **ECMAScript 5** native function implementations that we hope to use
  // are declared here.
  var
    nativeForEach      = ArrayProto.forEach,
    nativeMap          = ArrayProto.map,
    nativeReduce       = ArrayProto.reduce,
    nativeReduceRight  = ArrayProto.reduceRight,
    nativeFilter       = ArrayProto.filter,
    nativeEvery        = ArrayProto.every,
    nativeSome         = ArrayProto.some,
    nativeIndexOf      = ArrayProto.indexOf,
    nativeLastIndexOf  = ArrayProto.lastIndexOf,
    nativeIsArray      = Array.isArray,
    nativeKeys         = Object.keys,
    nativeBind         = FuncProto.bind;

  // Create a safe reference to the Underscore object for use below.
  var _ = function(obj) {
    if (obj instanceof _) return obj;
    if (!(this instanceof _)) return new _(obj);
    this._wrapped = obj;
  };

  // Export the Underscore object for **Node.js**, with
  // backwards-compatibility for the old `require()` API. If we\'re in
  // the browser, add `_` as a global object via a string identifier,
  // for Closure Compiler "advanced" mode.
  if (typeof exports !== \'undefined\') {
    if (typeof module !== \'undefined\' && module.exports) {
      exports = module.exports = _;
    }
    exports._ = _;
  } else {
    root._ = _;
  }

  // Current version.
  _.VERSION = \'1.4.4\';

  // Collection Functions
  // --------------------

  // The cornerstone, an `each` implementation, aka `forEach`.
  // Handles objects with the built-in `forEach`, arrays, and raw objects.
  // Delegates to **ECMAScript 5**\'s native `forEach` if available.
  var each = _.each = _.forEach = function(obj, iterator, context) {
    if (obj == null) return;
    if (nativeForEach && obj.forEach === nativeForEach) {
      obj.forEach(iterator, context);
    } else if (obj.length === +obj.length) {
      for (var i = 0, l = obj.length; i < l; i++) {
        if (iterator.call(context, obj[i], i, obj) === breaker) return;
      }
    } else {
      for (var key in obj) {
        if (_.has(obj, key)) {
          if (iterator.call(context, obj[key], key, obj) === breaker) return;
        }
      }
    }
  };

  // Return the results of applying the iterator to each element.
  // Delegates to **ECMAScript 5**\'s native `map` if available.
  _.map = _.collect = function(obj, iterator, context) {
    var results = [];
    if (obj == null) return results;
    if (nativeMap && obj.map === nativeMap) return obj.map(iterator, context);
    each(obj, function(value, index, list) {
      results[results.length] = iterator.call(context, value, index, list);
    });
    return results;
  };

  var reduceError = \'Reduce of empty array with no initial value\';

  // **Reduce** builds up a single result from a list of values, aka `inject`,
  // or `foldl`. Delegates to **ECMAScript 5**\'s native `reduce` if available.
  _.reduce = _.foldl = _.inject = function(obj, iterator, memo, context) {
    var initial = arguments.length > 2;
    if (obj == null) obj = [];
    if (nativeReduce && obj.reduce === nativeReduce) {
      if (context) iterator = _.bind(iterator, context);
      return initial ? obj.reduce(iterator, memo) : obj.reduce(iterator);
    }
    each(obj, function(value, index, list) {
      if (!initial) {
        memo = value;
        initial = true;
      } else {
        memo = iterator.call(context, memo, value, index, list);
      }
    });
    if (!initial) throw new TypeError(reduceError);
    return memo;
  };

  // The right-associative version of reduce, also known as `foldr`.
  // Delegates to **ECMAScript 5**\'s native `reduceRight` if available.
  _.reduceRight = _.foldr = function(obj, iterator, memo, context) {
    var initial = arguments.length > 2;
    if (obj == null) obj = [];
    if (nativeReduceRight && obj.reduceRight === nativeReduceRight) {
      if (context) iterator = _.bind(iterator, context);
      return initial ? obj.reduceRight(iterator, memo) : obj.reduceRight(iterator);
    }
    var length = obj.length;
    if (length !== +length) {
      var keys = _.keys(obj);
      length = keys.length;
    }
    each(obj, function(value, index, list) {
      index = keys ? keys[--length] : --length;
      if (!initial) {
        memo = obj[index];
        initial = true;
      } else {
        memo = iterator.call(context, memo, obj[index], index, list);
      }
    });
    if (!initial) throw new TypeError(reduceError);
    return memo;
  };

  // Return the first value which passes a truth test. Aliased as `detect`.
  _.find = _.detect = function(obj, iterator, context) {
    var result;
    any(obj, function(value, index, list) {
      if (iterator.call(context, value, index, list)) {
        result = value;
        return true;
      }
    });
    return result;
  };

  // Return all the elements that pass a truth test.
  // Delegates to **ECMAScript 5**\'s native `filter` if available.
  // Aliased as `select`.
  _.filter = _.select = function(obj, iterator, context) {
    var results = [];
    if (obj == null) return results;
    if (nativeFilter && obj.filter === nativeFilter) return obj.filter(iterator, context);
    each(obj, function(value, index, list) {
      if (iterator.call(context, value, index, list)) results[results.length] = value;
    });
    return results;
  };

  // Return all the elements for which a truth test fails.
  _.reject = function(obj, iterator, context) {
    return _.filter(obj, function(value, index, list) {
      return !iterator.call(context, value, index, list);
    }, context);
  };

  // Determine whether all of the elements match a truth test.
  // Delegates to **ECMAScript 5**\'s native `every` if available.
  // Aliased as `all`.
  _.every = _.all = function(obj, iterator, context) {
    iterator || (iterator = _.identity);
    var result = true;
    if (obj == null) return result;
    if (nativeEvery && obj.every === nativeEvery) return obj.every(iterator, context);
    each(obj, function(value, index, list) {
      if (!(result = result && iterator.call(context, value, index, list))) return breaker;
    });
    return !!result;
  };

  // Determine if at least one element in the object matches a truth test.
  // Delegates to **ECMAScript 5**\'s native `some` if available.
  // Aliased as `any`.
  var any = _.some = _.any = function(obj, iterator, context) {
    iterator || (iterator = _.identity);
    var result = false;
    if (obj == null) return result;
    if (nativeSome && obj.some === nativeSome) return obj.some(iterator, context);
    each(obj, function(value, index, list) {
      if (result || (result = iterator.call(context, value, index, list))) return breaker;
    });
    return !!result;
  };

  // Determine if the array or object contains a given value (using `===`).
  // Aliased as `include`.
  _.contains = _.include = function(obj, target) {
    if (obj == null) return false;
    if (nativeIndexOf && obj.indexOf === nativeIndexOf) return obj.indexOf(target) != -1;
    return any(obj, function(value) {
      return value === target;
    });
  };

  // Invoke a method (with arguments) on every item in a collection.
  _.invoke = function(obj, method) {
    var args = slice.call(arguments, 2);
    var isFunc = _.isFunction(method);
    return _.map(obj, function(value) {
      return (isFunc ? method : value[method]).apply(value, args);
    });
  };

  // Convenience version of a common use case of `map`: fetching a property.
  _.pluck = function(obj, key) {
    return _.map(obj, function(value){ return value[key]; });
  };

  // Convenience version of a common use case of `filter`: selecting only objects
  // containing specific `key:value` pairs.
  _.where = function(obj, attrs, first) {
    if (_.isEmpty(attrs)) return first ? null : [];
    return _[first ? \'find\' : \'filter\'](obj, function(value) {
      for (var key in attrs) {
        if (attrs[key] !== value[key]) return false;
      }
      return true;
    });
  };

  // Convenience version of a common use case of `find`: getting the first object
  // containing specific `key:value` pairs.
  _.findWhere = function(obj, attrs) {
    return _.where(obj, attrs, true);
  };

  // Return the maximum element or (element-based computation).
  // Can\'t optimize arrays of integers longer than 65,535 elements.
  // See: https://bugs.webkit.org/show_bug.cgi?id=80797
  _.max = function(obj, iterator, context) {
    if (!iterator && _.isArray(obj) && obj[0] === +obj[0] && obj.length < 65535) {
      return Math.max.apply(Math, obj);
    }
    if (!iterator && _.isEmpty(obj)) return -Infinity;
    var result = {computed : -Infinity, value: -Infinity};
    each(obj, function(value, index, list) {
      var computed = iterator ? iterator.call(context, value, index, list) : value;
      computed >= result.computed && (result = {value : value, computed : computed});
    });
    return result.value;
  };

  // Return the minimum element (or element-based computation).
  _.min = function(obj, iterator, context) {
    if (!iterator && _.isArray(obj) && obj[0] === +obj[0] && obj.length < 65535) {
      return Math.min.apply(Math, obj);
    }
    if (!iterator && _.isEmpty(obj)) return Infinity;
    var result = {computed : Infinity, value: Infinity};
    each(obj, function(value, index, list) {
      var computed = iterator ? iterator.call(context, value, index, list) : value;
      computed < result.computed && (result = {value : value, computed : computed});
    });
    return result.value;
  };

  // Shuffle an array.
  _.shuffle = function(obj) {
    var rand;
    var index = 0;
    var shuffled = [];
    each(obj, function(value) {
      rand = _.random(index++);
      shuffled[index - 1] = shuffled[rand];
      shuffled[rand] = value;
    });
    return shuffled;
  };

  // An internal function to generate lookup iterators.
  var lookupIterator = function(value) {
    return _.isFunction(value) ? value : function(obj){ return obj[value]; };
  };

  // Sort the object\'s values by a criterion produced by an iterator.
  _.sortBy = function(obj, value, context) {
    var iterator = lookupIterator(value);
    return _.pluck(_.map(obj, function(value, index, list) {
      return {
        value : value,
        index : index,
        criteria : iterator.call(context, value, index, list)
      };
    }).sort(function(left, right) {
      var a = left.criteria;
      var b = right.criteria;
      if (a !== b) {
        if (a > b || a === void 0) return 1;
        if (a < b || b === void 0) return -1;
      }
      return left.index < right.index ? -1 : 1;
    }), \'value\');
  };

  // An internal function used for aggregate "group by" operations.
  var group = function(obj, value, context, behavior) {
    var result = {};
    var iterator = lookupIterator(value || _.identity);
    each(obj, function(value, index) {
      var key = iterator.call(context, value, index, obj);
      behavior(result, key, value);
    });
    return result;
  };

  // Groups the object\'s values by a criterion. Pass either a string attribute
  // to group by, or a function that returns the criterion.
  _.groupBy = function(obj, value, context) {
    return group(obj, value, context, function(result, key, value) {
      (_.has(result, key) ? result[key] : (result[key] = [])).push(value);
    });
  };

  // Counts instances of an object that group by a certain criterion. Pass
  // either a string attribute to count by, or a function that returns the
  // criterion.
  _.countBy = function(obj, value, context) {
    return group(obj, value, context, function(result, key) {
      if (!_.has(result, key)) result[key] = 0;
      result[key]++;
    });
  };

  // Use a comparator function to figure out the smallest index at which
  // an object should be inserted so as to maintain order. Uses binary search.
  _.sortedIndex = function(array, obj, iterator, context) {
    iterator = iterator == null ? _.identity : lookupIterator(iterator);
    var value = iterator.call(context, obj);
    var low = 0, high = array.length;
    while (low < high) {
      var mid = (low + high) >>> 1;
      iterator.call(context, array[mid]) < value ? low = mid + 1 : high = mid;
    }
    return low;
  };

  // Safely convert anything iterable into a real, live array.
  _.toArray = function(obj) {
    if (!obj) return [];
    if (_.isArray(obj)) return slice.call(obj);
    if (obj.length === +obj.length) return _.map(obj, _.identity);
    return _.values(obj);
  };

  // Return the number of elements in an object.
  _.size = function(obj) {
    if (obj == null) return 0;
    return (obj.length === +obj.length) ? obj.length : _.keys(obj).length;
  };

  // Array Functions
  // ---------------

  // Get the first element of an array. Passing **n** will return the first N
  // values in the array. Aliased as `head` and `take`. The **guard** check
  // allows it to work with `_.map`.
  _.first = _.head = _.take = function(array, n, guard) {
    if (array == null) return void 0;
    return (n != null) && !guard ? slice.call(array, 0, n) : array[0];
  };

  // Returns everything but the last entry of the array. Especially useful on
  // the arguments object. Passing **n** will return all the values in
  // the array, excluding the last N. The **guard** check allows it to work with
  // `_.map`.
  _.initial = function(array, n, guard) {
    return slice.call(array, 0, array.length - ((n == null) || guard ? 1 : n));
  };

  // Get the last element of an array. Passing **n** will return the last N
  // values in the array. The **guard** check allows it to work with `_.map`.
  _.last = function(array, n, guard) {
    if (array == null) return void 0;
    if ((n != null) && !guard) {
      return slice.call(array, Math.max(array.length - n, 0));
    } else {
      return array[array.length - 1];
    }
  };

  // Returns everything but the first entry of the array. Aliased as `tail` and `drop`.
  // Especially useful on the arguments object. Passing an **n** will return
  // the rest N values in the array. The **guard**
  // check allows it to work with `_.map`.
  _.rest = _.tail = _.drop = function(array, n, guard) {
    return slice.call(array, (n == null) || guard ? 1 : n);
  };

  // Trim out all falsy values from an array.
  _.compact = function(array) {
    return _.filter(array, _.identity);
  };

  // Internal implementation of a recursive `flatten` function.
  var flatten = function(input, shallow, output) {
    each(input, function(value) {
      if (_.isArray(value)) {
        shallow ? push.apply(output, value) : flatten(value, shallow, output);
      } else {
        output.push(value);
      }
    });
    return output;
  };

  // Return a completely flattened version of an array.
  _.flatten = function(array, shallow) {
    return flatten(array, shallow, []);
  };

  // Return a version of the array that does not contain the specified value(s).
  _.without = function(array) {
    return _.difference(array, slice.call(arguments, 1));
  };

  // Produce a duplicate-free version of the array. If the array has already
  // been sorted, you have the option of using a faster algorithm.
  // Aliased as `unique`.
  _.uniq = _.unique = function(array, isSorted, iterator, context) {
    if (_.isFunction(isSorted)) {
      context = iterator;
      iterator = isSorted;
      isSorted = false;
    }
    var initial = iterator ? _.map(array, iterator, context) : array;
    var results = [];
    var seen = [];
    each(initial, function(value, index) {
      if (isSorted ? (!index || seen[seen.length - 1] !== value) : !_.contains(seen, value)) {
        seen.push(value);
        results.push(array[index]);
      }
    });
    return results;
  };

  // Produce an array that contains the union: each distinct element from all of
  // the passed-in arrays.
  _.union = function() {
    return _.uniq(concat.apply(ArrayProto, arguments));
  };

  // Produce an array that contains every item shared between all the
  // passed-in arrays.
  _.intersection = function(array) {
    var rest = slice.call(arguments, 1);
    return _.filter(_.uniq(array), function(item) {
      return _.every(rest, function(other) {
        return _.indexOf(other, item) >= 0;
      });
    });
  };

  // Take the difference between one array and a number of other arrays.
  // Only the elements present in just the first array will remain.
  _.difference = function(array) {
    var rest = concat.apply(ArrayProto, slice.call(arguments, 1));
    return _.filter(array, function(value){ return !_.contains(rest, value); });
  };

  // Zip together multiple lists into a single array -- elements that share
  // an index go together.
  _.zip = function() {
    var args = slice.call(arguments);
    var length = _.max(_.pluck(args, \'length\'));
    var results = new Array(length);
    for (var i = 0; i < length; i++) {
      results[i] = _.pluck(args, "" + i);
    }
    return results;
  };

  // Converts lists into objects. Pass either a single array of `[key, value]`
  // pairs, or two parallel arrays of the same length -- one of keys, and one of
  // the corresponding values.
  _.object = function(list, values) {
    if (list == null) return {};
    var result = {};
    for (var i = 0, l = list.length; i < l; i++) {
      if (values) {
        result[list[i]] = values[i];
      } else {
        result[list[i][0]] = list[i][1];
      }
    }
    return result;
  };

  // If the browser doesn\'t supply us with indexOf (I\'m looking at you, **MSIE**),
  // we need this function. Return the position of the first occurrence of an
  // item in an array, or -1 if the item is not included in the array.
  // Delegates to **ECMAScript 5**\'s native `indexOf` if available.
  // If the array is large and already in sort order, pass `true`
  // for **isSorted** to use binary search.
  _.indexOf = function(array, item, isSorted) {
    if (array == null) return -1;
    var i = 0, l = array.length;
    if (isSorted) {
      if (typeof isSorted == \'number\') {
        i = (isSorted < 0 ? Math.max(0, l + isSorted) : isSorted);
      } else {
        i = _.sortedIndex(array, item);
        return array[i] === item ? i : -1;
      }
    }
    if (nativeIndexOf && array.indexOf === nativeIndexOf) return array.indexOf(item, isSorted);
    for (; i < l; i++) if (array[i] === item) return i;
    return -1;
  };

  // Delegates to **ECMAScript 5**\'s native `lastIndexOf` if available.
  _.lastIndexOf = function(array, item, from) {
    if (array == null) return -1;
    var hasIndex = from != null;
    if (nativeLastIndexOf && array.lastIndexOf === nativeLastIndexOf) {
      return hasIndex ? array.lastIndexOf(item, from) : array.lastIndexOf(item);
    }
    var i = (hasIndex ? from : array.length);
    while (i--) if (array[i] === item) return i;
    return -1;
  };

  // Generate an integer Array containing an arithmetic progression. A port of
  // the native Python `range()` function. See
  // [the Python documentation](http://docs.python.org/library/functions.html#range).
  _.range = function(start, stop, step) {
    if (arguments.length <= 1) {
      stop = start || 0;
      start = 0;
    }
    step = arguments[2] || 1;

    var len = Math.max(Math.ceil((stop - start) / step), 0);
    var idx = 0;
    var range = new Array(len);

    while(idx < len) {
      range[idx++] = start;
      start += step;
    }

    return range;
  };

  // Function (ahem) Functions
  // ------------------

  // Create a function bound to a given object (assigning `this`, and arguments,
  // optionally). Delegates to **ECMAScript 5**\'s native `Function.bind` if
  // available.
  _.bind = function(func, context) {
    if (func.bind === nativeBind && nativeBind) return nativeBind.apply(func, slice.call(arguments, 1));
    var args = slice.call(arguments, 2);
    return function() {
      return func.apply(context, args.concat(slice.call(arguments)));
    };
  };

  // Partially apply a function by creating a version that has had some of its
  // arguments pre-filled, without changing its dynamic `this` context.
  _.partial = function(func) {
    var args = slice.call(arguments, 1);
    return function() {
      return func.apply(this, args.concat(slice.call(arguments)));
    };
  };

  // Bind all of an object\'s methods to that object. Useful for ensuring that
  // all callbacks defined on an object belong to it.
  _.bindAll = function(obj) {
    var funcs = slice.call(arguments, 1);
    if (funcs.length === 0) funcs = _.functions(obj);
    each(funcs, function(f) { obj[f] = _.bind(obj[f], obj); });
    return obj;
  };

  // Memoize an expensive function by storing its results.
  _.memoize = function(func, hasher) {
    var memo = {};
    hasher || (hasher = _.identity);
    return function() {
      var key = hasher.apply(this, arguments);
      return _.has(memo, key) ? memo[key] : (memo[key] = func.apply(this, arguments));
    };
  };

  // Delays a function for the given number of milliseconds, and then calls
  // it with the arguments supplied.
  _.delay = function(func, wait) {
    var args = slice.call(arguments, 2);
    return setTimeout(function(){ return func.apply(null, args); }, wait);
  };

  // Defers a function, scheduling it to run after the current call stack has
  // cleared.
  _.defer = function(func) {
    return _.delay.apply(_, [func, 1].concat(slice.call(arguments, 1)));
  };

  // Returns a function, that, when invoked, will only be triggered at most once
  // during a given window of time.
  _.throttle = function(func, wait) {
    var context, args, timeout, result;
    var previous = 0;
    var later = function() {
      previous = new Date;
      timeout = null;
      result = func.apply(context, args);
    };
    return function() {
      var now = new Date;
      var remaining = wait - (now - previous);
      context = this;
      args = arguments;
      if (remaining <= 0) {
        clearTimeout(timeout);
        timeout = null;
        previous = now;
        result = func.apply(context, args);
      } else if (!timeout) {
        timeout = setTimeout(later, remaining);
      }
      return result;
    };
  };

  // Returns a function, that, as long as it continues to be invoked, will not
  // be triggered. The function will be called after it stops being called for
  // N milliseconds. If `immediate` is passed, trigger the function on the
  // leading edge, instead of the trailing.
  _.debounce = function(func, wait, immediate) {
    var timeout, result;
    return function() {
      var context = this, args = arguments;
      var later = function() {
        timeout = null;
        if (!immediate) result = func.apply(context, args);
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) result = func.apply(context, args);
      return result;
    };
  };

  // Returns a function that will be executed at most one time, no matter how
  // often you call it. Useful for lazy initialization.
  _.once = function(func) {
    var ran = false, memo;
    return function() {
      if (ran) return memo;
      ran = true;
      memo = func.apply(this, arguments);
      func = null;
      return memo;
    };
  };

  // Returns the first function passed as an argument to the second,
  // allowing you to adjust arguments, run code before and after, and
  // conditionally execute the original function.
  _.wrap = function(func, wrapper) {
    return function() {
      var args = [func];
      push.apply(args, arguments);
      return wrapper.apply(this, args);
    };
  };

  // Returns a function that is the composition of a list of functions, each
  // consuming the return value of the function that follows.
  _.compose = function() {
    var funcs = arguments;
    return function() {
      var args = arguments;
      for (var i = funcs.length - 1; i >= 0; i--) {
        args = [funcs[i].apply(this, args)];
      }
      return args[0];
    };
  };

  // Returns a function that will only be executed after being called N times.
  _.after = function(times, func) {
    if (times <= 0) return func();
    return function() {
      if (--times < 1) {
        return func.apply(this, arguments);
      }
    };
  };

  // Object Functions
  // ----------------

  // Retrieve the names of an object\'s properties.
  // Delegates to **ECMAScript 5**\'s native `Object.keys`
  _.keys = nativeKeys || function(obj) {
    if (obj !== Object(obj)) throw new TypeError(\'Invalid object\');
    var keys = [];
    for (var key in obj) if (_.has(obj, key)) keys[keys.length] = key;
    return keys;
  };

  // Retrieve the values of an object\'s properties.
  _.values = function(obj) {
    var values = [];
    for (var key in obj) if (_.has(obj, key)) values.push(obj[key]);
    return values;
  };

  // Convert an object into a list of `[key, value]` pairs.
  _.pairs = function(obj) {
    var pairs = [];
    for (var key in obj) if (_.has(obj, key)) pairs.push([key, obj[key]]);
    return pairs;
  };

  // Invert the keys and values of an object. The values must be serializable.
  _.invert = function(obj) {
    var result = {};
    for (var key in obj) if (_.has(obj, key)) result[obj[key]] = key;
    return result;
  };

  // Return a sorted list of the function names available on the object.
  // Aliased as `methods`
  _.functions = _.methods = function(obj) {
    var names = [];
    for (var key in obj) {
      if (_.isFunction(obj[key])) names.push(key);
    }
    return names.sort();
  };

  // Extend a given object with all the properties in passed-in object(s).
  _.extend = function(obj) {
    each(slice.call(arguments, 1), function(source) {
      if (source) {
        for (var prop in source) {
          obj[prop] = source[prop];
        }
      }
    });
    return obj;
  };

  // Return a copy of the object only containing the whitelisted properties.
  _.pick = function(obj) {
    var copy = {};
    var keys = concat.apply(ArrayProto, slice.call(arguments, 1));
    each(keys, function(key) {
      if (key in obj) copy[key] = obj[key];
    });
    return copy;
  };

   // Return a copy of the object without the blacklisted properties.
  _.omit = function(obj) {
    var copy = {};
    var keys = concat.apply(ArrayProto, slice.call(arguments, 1));
    for (var key in obj) {
      if (!_.contains(keys, key)) copy[key] = obj[key];
    }
    return copy;
  };

  // Fill in a given object with default properties.
  _.defaults = function(obj) {
    each(slice.call(arguments, 1), function(source) {
      if (source) {
        for (var prop in source) {
          if (obj[prop] == null) obj[prop] = source[prop];
        }
      }
    });
    return obj;
  };

  // Create a (shallow-cloned) duplicate of an object.
  _.clone = function(obj) {
    if (!_.isObject(obj)) return obj;
    return _.isArray(obj) ? obj.slice() : _.extend({}, obj);
  };

  // Invokes interceptor with the obj, and then returns obj.
  // The primary purpose of this method is to "tap into" a method chain, in
  // order to perform operations on intermediate results within the chain.
  _.tap = function(obj, interceptor) {
    interceptor(obj);
    return obj;
  };

  // Internal recursive comparison function for `isEqual`.
  var eq = function(a, b, aStack, bStack) {
    // Identical objects are equal. `0 === -0`, but they aren\'t identical.
    // See the Harmony `egal` proposal: http://wiki.ecmascript.org/doku.php?id=harmony:egal.
    if (a === b) return a !== 0 || 1 / a == 1 / b;
    // A strict comparison is necessary because `null == undefined`.
    if (a == null || b == null) return a === b;
    // Unwrap any wrapped objects.
    if (a instanceof _) a = a._wrapped;
    if (b instanceof _) b = b._wrapped;
    // Compare `[[Class]]` names.
    var className = toString.call(a);
    if (className != toString.call(b)) return false;
    switch (className) {
      // Strings, numbers, dates, and booleans are compared by value.
      case \'[object String]\':
        // Primitives and their corresponding object wrappers are equivalent; thus, `"5"` is
        // equivalent to `new String("5")`.
        return a == String(b);
      case \'[object Number]\':
        // `NaN`s are equivalent, but non-reflexive. An `egal` comparison is performed for
        // other numeric values.
        return a != +a ? b != +b : (a == 0 ? 1 / a == 1 / b : a == +b);
      case \'[object Date]\':
      case \'[object Boolean]\':
        // Coerce dates and booleans to numeric primitive values. Dates are compared by their
        // millisecond representations. Note that invalid dates with millisecond representations
        // of `NaN` are not equivalent.
        return +a == +b;
      // RegExps are compared by their source patterns and flags.
      case \'[object RegExp]\':
        return a.source == b.source &&
               a.global == b.global &&
               a.multiline == b.multiline &&
               a.ignoreCase == b.ignoreCase;
    }
    if (typeof a != \'object\' || typeof b != \'object\') return false;
    // Assume equality for cyclic structures. The algorithm for detecting cyclic
    // structures is adapted from ES 5.1 section 15.12.3, abstract operation `JO`.
    var length = aStack.length;
    while (length--) {
      // Linear search. Performance is inversely proportional to the number of
      // unique nested structures.
      if (aStack[length] == a) return bStack[length] == b;
    }
    // Add the first object to the stack of traversed objects.
    aStack.push(a);
    bStack.push(b);
    var size = 0, result = true;
    // Recursively compare objects and arrays.
    if (className == \'[object Array]\') {
      // Compare array lengths to determine if a deep comparison is necessary.
      size = a.length;
      result = size == b.length;
      if (result) {
        // Deep compare the contents, ignoring non-numeric properties.
        while (size--) {
          if (!(result = eq(a[size], b[size], aStack, bStack))) break;
        }
      }
    } else {
      // Objects with different constructors are not equivalent, but `Object`s
      // from different frames are.
      var aCtor = a.constructor, bCtor = b.constructor;
      if (aCtor !== bCtor && !(_.isFunction(aCtor) && (aCtor instanceof aCtor) &&
                               _.isFunction(bCtor) && (bCtor instanceof bCtor))) {
        return false;
      }
      // Deep compare objects.
      for (var key in a) {
        if (_.has(a, key)) {
          // Count the expected number of properties.
          size++;
          // Deep compare each member.
          if (!(result = _.has(b, key) && eq(a[key], b[key], aStack, bStack))) break;
        }
      }
      // Ensure that both objects contain the same number of properties.
      if (result) {
        for (key in b) {
          if (_.has(b, key) && !(size--)) break;
        }
        result = !size;
      }
    }
    // Remove the first object from the stack of traversed objects.
    aStack.pop();
    bStack.pop();
    return result;
  };

  // Perform a deep comparison to check if two objects are equal.
  _.isEqual = function(a, b) {
    return eq(a, b, [], []);
  };

  // Is a given array, string, or object empty?
  // An "empty" object has no enumerable own-properties.
  _.isEmpty = function(obj) {
    if (obj == null) return true;
    if (_.isArray(obj) || _.isString(obj)) return obj.length === 0;
    for (var key in obj) if (_.has(obj, key)) return false;
    return true;
  };

  // Is a given value a DOM element?
  _.isElement = function(obj) {
    return !!(obj && obj.nodeType === 1);
  };

  // Is a given value an array?
  // Delegates to ECMA5\'s native Array.isArray
  _.isArray = nativeIsArray || function(obj) {
    return toString.call(obj) == \'[object Array]\';
  };

  // Is a given variable an object?
  _.isObject = function(obj) {
    return obj === Object(obj);
  };

  // Add some isType methods: isArguments, isFunction, isString, isNumber, isDate, isRegExp.
  each([\'Arguments\', \'Function\', \'String\', \'Number\', \'Date\', \'RegExp\'], function(name) {
    _[\'is\' + name] = function(obj) {
      return toString.call(obj) == \'[object \' + name + \']\';
    };
  });

  // Define a fallback version of the method in browsers (ahem, IE), where
  // there isn\'t any inspectable "Arguments" type.
  if (!_.isArguments(arguments)) {
    _.isArguments = function(obj) {
      return !!(obj && _.has(obj, \'callee\'));
    };
  }

  // Optimize `isFunction` if appropriate.
  if (typeof (/./) !== \'function\') {
    _.isFunction = function(obj) {
      return typeof obj === \'function\';
    };
  }

  // Is a given object a finite number?
  _.isFinite = function(obj) {
    return isFinite(obj) && !isNaN(parseFloat(obj));
  };

  // Is the given value `NaN`? (NaN is the only number which does not equal itself).
  _.isNaN = function(obj) {
    return _.isNumber(obj) && obj != +obj;
  };

  // Is a given value a boolean?
  _.isBoolean = function(obj) {
    return obj === true || obj === false || toString.call(obj) == \'[object Boolean]\';
  };

  // Is a given value equal to null?
  _.isNull = function(obj) {
    return obj === null;
  };

  // Is a given variable undefined?
  _.isUndefined = function(obj) {
    return obj === void 0;
  };

  // Shortcut function for checking if an object has a given property directly
  // on itself (in other words, not on a prototype).
  _.has = function(obj, key) {
    return hasOwnProperty.call(obj, key);
  };

  // Utility Functions
  // -----------------

  // Run Underscore.js in *noConflict* mode, returning the `_` variable to its
  // previous owner. Returns a reference to the Underscore object.
  _.noConflict = function() {
    root._ = previousUnderscore;
    return this;
  };

  // Keep the identity function around for default iterators.
  _.identity = function(value) {
    return value;
  };

  // Run a function **n** times.
  _.times = function(n, iterator, context) {
    var accum = Array(n);
    for (var i = 0; i < n; i++) accum[i] = iterator.call(context, i);
    return accum;
  };

  // Return a random integer between min and max (inclusive).
  _.random = function(min, max) {
    if (max == null) {
      max = min;
      min = 0;
    }
    return min + Math.floor(Math.random() * (max - min + 1));
  };

  // List of HTML entities for escaping.
  var entityMap = {
    escape: {
      \'&\': \'&amp;\',
      \'<\': \'&lt;\',
      \'>\': \'&gt;\',
      \'"\': \'&quot;\',
      "\'": \'&#x27;\',
      \'/\': \'&#x2F;\'
    }
  };
  entityMap.unescape = _.invert(entityMap.escape);

  // Regexes containing the keys and values listed immediately above.
  var entityRegexes = {
    escape:   new RegExp(\'[\' + _.keys(entityMap.escape).join(\'\') + \']\', \'g\'),
    unescape: new RegExp(\'(\' + _.keys(entityMap.unescape).join(\'|\') + \')\', \'g\')
  };

  // Functions for escaping and unescaping strings to/from HTML interpolation.
  _.each([\'escape\', \'unescape\'], function(method) {
    _[method] = function(string) {
      if (string == null) return \'\';
      return (\'\' + string).replace(entityRegexes[method], function(match) {
        return entityMap[method][match];
      });
    };
  });

  // If the value of the named property is a function then invoke it;
  // otherwise, return it.
  _.result = function(object, property) {
    if (object == null) return null;
    var value = object[property];
    return _.isFunction(value) ? value.call(object) : value;
  };

  // Add your own custom functions to the Underscore object.
  _.mixin = function(obj) {
    each(_.functions(obj), function(name){
      var func = _[name] = obj[name];
      _.prototype[name] = function() {
        var args = [this._wrapped];
        push.apply(args, arguments);
        return result.call(this, func.apply(_, args));
      };
    });
  };

  // Generate a unique integer id (unique within the entire client session).
  // Useful for temporary DOM ids.
  var idCounter = 0;
  _.uniqueId = function(prefix) {
    var id = ++idCounter + \'\';
    return prefix ? prefix + id : id;
  };

  // By default, Underscore uses ERB-style template delimiters, change the
  // following template settings to use alternative delimiters.
  _.templateSettings = {
    evaluate    : /<%([\\s\\S]+?)%>/g,
    interpolate : /<%=([\\s\\S]+?)%>/g,
    escape      : /<%-([\\s\\S]+?)%>/g
  };

  // When customizing `templateSettings`, if you don\'t want to define an
  // interpolation, evaluation or escaping regex, we need one that is
  // guaranteed not to match.
  var noMatch = /(.)^/;

  // Certain characters need to be escaped so that they can be put into a
  // string literal.
  var escapes = {
    "\'":      "\'",
    \'\\\\\':     \'\\\\\',
    \'\\r\':     \'r\',
    \'\\n\':     \'n\',
    \'\\t\':     \'t\',
    \'\\u2028\': \'u2028\',
    \'\\u2029\': \'u2029\'
  };

  var escaper = /\\\\|\'|\\r|\\n|\\t|\\u2028|\\u2029/g;

  // JavaScript micro-templating, similar to John Resig\'s implementation.
  // Underscore templating handles arbitrary delimiters, preserves whitespace,
  // and correctly escapes quotes within interpolated code.
  _.template = function(text, data, settings) {
    var render;
    settings = _.defaults({}, settings, _.templateSettings);

    // Combine delimiters into one regular expression via alternation.
    var matcher = new RegExp([
      (settings.escape || noMatch).source,
      (settings.interpolate || noMatch).source,
      (settings.evaluate || noMatch).source
    ].join(\'|\') + \'|$\', \'g\');

    // Compile the template source, escaping string literals appropriately.
    var index = 0;
    var source = "__p+=\'";
    text.replace(matcher, function(match, escape, interpolate, evaluate, offset) {
      source += text.slice(index, offset)
        .replace(escaper, function(match) { return \'\\\\\' + escapes[match]; });

      if (escape) {
        source += "\'+\\n((__t=(" + escape + "))==null?\'\':_.escape(__t))+\\n\'";
      }
      if (interpolate) {
        source += "\'+\\n((__t=(" + interpolate + "))==null?\'\':__t)+\\n\'";
      }
      if (evaluate) {
        source += "\';\\n" + evaluate + "\\n__p+=\'";
      }
      index = offset + match.length;
      return match;
    });
    source += "\';\\n";

    // If a variable is not specified, place data values in local scope.
    if (!settings.variable) source = \'with(obj||{}){\\n\' + source + \'}\\n\';

    source = "var __t,__p=\'\',__j=Array.prototype.join," +
      "print=function(){__p+=__j.call(arguments,\'\');};\\n" +
      source + "return __p;\\n";

    try {
      render = new Function(settings.variable || \'obj\', \'_\', source);
    } catch (e) {
      e.source = source;
      throw e;
    }

    if (data) return render(data, _);
    var template = function(data) {
      return render.call(this, data, _);
    };

    // Provide the compiled function source as a convenience for precompilation.
    template.source = \'function(\' + (settings.variable || \'obj\') + \'){\\n\' + source + \'}\';

    return template;
  };

  // Add a "chain" function, which will delegate to the wrapper.
  _.chain = function(obj) {
    return _(obj).chain();
  };

  // OOP
  // ---------------
  // If Underscore is called as a function, it returns a wrapped object that
  // can be used OO-style. This wrapper holds altered versions of all the
  // underscore functions. Wrapped objects may be chained.

  // Helper function to continue chaining intermediate results.
  var result = function(obj) {
    return this._chain ? _(obj).chain() : obj;
  };

  // Add all of the Underscore functions to the wrapper object.
  _.mixin(_);

  // Add all mutator Array functions to the wrapper.
  each([\'pop\', \'push\', \'reverse\', \'shift\', \'sort\', \'splice\', \'unshift\'], function(name) {
    var method = ArrayProto[name];
    _.prototype[name] = function() {
      var obj = this._wrapped;
      method.apply(obj, arguments);
      if ((name == \'shift\' || name == \'splice\') && obj.length === 0) delete obj[0];
      return result.call(this, obj);
    };
  });

  // Add all accessor Array functions to the wrapper.
  each([\'concat\', \'join\', \'slice\'], function(name) {
    var method = ArrayProto[name];
    _.prototype[name] = function() {
      return result.call(this, method.apply(this._wrapped, arguments));
    };
  });

  _.extend(_.prototype, {

    // Start chaining a wrapped Underscore object.
    chain: function() {
      this._chain = true;
      return this;
    },

    // Extracts the result from a wrapped and chained object.
    value: function() {
      return this._wrapped;
    }

  });

}).call(this);
',
    '229d4a80' => ';//     Backbone.js 0.9.10

//     (c) 2010-2013 Jeremy Ashkenas, DocumentCloud Inc.
//     Backbone may be freely distributed under the MIT license.
//     For all details and documentation:
//     http://backbonejs.org

(function(){

  // Initial Setup
  // -------------

  // Save a reference to the global object (`window` in the browser, `exports`
  // on the server).
  var root = this;

  // Save the previous value of the `Backbone` variable, so that it can be
  // restored later on, if `noConflict` is used.
  var previousBackbone = root.Backbone;

  // Create a local reference to array methods.
  var array = [];
  var push = array.push;
  var slice = array.slice;
  var splice = array.splice;

  // The top-level namespace. All public Backbone classes and modules will
  // be attached to this. Exported for both CommonJS and the browser.
  var Backbone;
  if (typeof exports !== \'undefined\') {
    Backbone = exports;
  } else {
    Backbone = root.Backbone = {};
  }

  // Current version of the library. Keep in sync with `package.json`.
  Backbone.VERSION = \'0.9.10\';

  // Require Underscore, if we\'re on the server, and it\'s not already present.
  var _ = root._;
  if (!_ && (typeof require !== \'undefined\')) _ = require(\'/static/common/ui/underscore/underscore.js\');

  // For Backbone\'s purposes, jQuery, Zepto, or Ender owns the `$` variable.
  Backbone.$ = root.jQuery || root.Zepto || root.ender;

  // Runs Backbone.js in *noConflict* mode, returning the `Backbone` variable
  // to its previous owner. Returns a reference to this Backbone object.
  Backbone.noConflict = function() {
    root.Backbone = previousBackbone;
    return this;
  };

  // Turn on `emulateHTTP` to support legacy HTTP servers. Setting this option
  // will fake `"PUT"` and `"DELETE"` requests via the `_method` parameter and
  // set a `X-Http-Method-Override` header.
  Backbone.emulateHTTP = false;

  // Turn on `emulateJSON` to support legacy servers that can\'t deal with direct
  // `application/json` requests ... will encode the body as
  // `application/x-www-form-urlencoded` instead and will send the model in a
  // form param named `model`.
  Backbone.emulateJSON = false;

  // Backbone.Events
  // ---------------

  // Regular expression used to split event strings.
  var eventSplitter = /\\s+/;

  // Implement fancy features of the Events API such as multiple event
  // names `"change blur"` and jQuery-style event maps `{change: action}`
  // in terms of the existing API.
  var eventsApi = function(obj, action, name, rest) {
    if (!name) return true;
    if (typeof name === \'object\') {
      for (var key in name) {
        obj[action].apply(obj, [key, name[key]].concat(rest));
      }
    } else if (eventSplitter.test(name)) {
      var names = name.split(eventSplitter);
      for (var i = 0, l = names.length; i < l; i++) {
        obj[action].apply(obj, [names[i]].concat(rest));
      }
    } else {
      return true;
    }
  };

  // Optimized internal dispatch function for triggering events. Tries to
  // keep the usual cases speedy (most Backbone events have 3 arguments).
  var triggerEvents = function(events, args) {
    var ev, i = -1, l = events.length, a1 = args[0], a2 = args[1], a3 = args[2];
    switch (args.length) {
    case 0: while (++i < l) (ev = events[i]).callback.call(ev.ctx);
    return;
    case 1: while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1);
    return;
    case 2: while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1, a2);
    return;
    case 3: while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1, a2, a3);
    return;
    default: while (++i < l) (ev = events[i]).callback.apply(ev.ctx, args);
    }
  };

  // A module that can be mixed in to *any object* in order to provide it with
  // custom events. You may bind with `on` or remove with `off` callback
  // functions to an event; `trigger`-ing an event fires all callbacks in
  // succession.
  //
  //     var object = {};
  //     _.extend(object, Backbone.Events);
  //     object.on(\'expand\', function(){ alert(\'expanded\'); });
  //     object.trigger(\'expand\');
  //
  var Events = Backbone.Events = {

    // Bind one or more space separated events, or an events map,
    // to a `callback` function. Passing `"all"` will bind the callback to
    // all events fired.
    on: function(name, callback, context) {
      if (!eventsApi(this, \'on\', name, [callback, context]) || !callback) return this;
      this._events || (this._events = {});
      var events = this._events[name] || (this._events[name] = []);
      events.push({callback: callback, context: context, ctx: context || this});
      return this;
    },

    // Bind events to only be triggered a single time. After the first time
    // the callback is invoked, it will be removed.
    once: function(name, callback, context) {
      if (!eventsApi(this, \'once\', name, [callback, context]) || !callback) return this;
      var self = this;
      var once = _.once(function() {
        self.off(name, once);
        callback.apply(this, arguments);
      });
      once._callback = callback;
      return this.on(name, once, context);
    },

    // Remove one or many callbacks. If `context` is null, removes all
    // callbacks with that function. If `callback` is null, removes all
    // callbacks for the event. If `name` is null, removes all bound
    // callbacks for all events.
    off: function(name, callback, context) {
      var retain, ev, events, names, i, l, j, k;
      if (!this._events || !eventsApi(this, \'off\', name, [callback, context])) return this;
      if (!name && !callback && !context) {
        this._events = {};
        return this;
      }

      names = name ? [name] : _.keys(this._events);
      for (i = 0, l = names.length; i < l; i++) {
        name = names[i];
        if (events = this._events[name]) {
          this._events[name] = retain = [];
          if (callback || context) {
            for (j = 0, k = events.length; j < k; j++) {
              ev = events[j];
              if ((callback && callback !== ev.callback &&
                               callback !== ev.callback._callback) ||
                  (context && context !== ev.context)) {
                retain.push(ev);
              }
            }
          }
          if (!retain.length) delete this._events[name];
        }
      }

      return this;
    },

    // Trigger one or many events, firing all bound callbacks. Callbacks are
    // passed the same arguments as `trigger` is, apart from the event name
    // (unless you\'re listening on `"all"`, which will cause your callback to
    // receive the true name of the event as the first argument).
    trigger: function(name) {
      if (!this._events) return this;
      var args = slice.call(arguments, 1);
      if (!eventsApi(this, \'trigger\', name, args)) return this;
      var events = this._events[name];
      var allEvents = this._events.all;
      if (events) triggerEvents(events, args);
      if (allEvents) triggerEvents(allEvents, arguments);
      return this;
    },

    // Tell this object to stop listening to either specific events ... or
    // to every object it\'s currently listening to.
    stopListening: function(obj, name, callback) {
      var listeners = this._listeners;
      if (!listeners) return this;
      if (obj) {
        obj.off(name, typeof name === \'object\' ? this : callback, this);
        if (!name && !callback) delete listeners[obj._listenerId];
      } else {
        if (typeof name === \'object\') callback = this;
        for (var id in listeners) {
          listeners[id].off(name, callback, this);
        }
        this._listeners = {};
      }
      return this;
    }
  };

  var listenMethods = {listenTo: \'on\', listenToOnce: \'once\'};

  // An inversion-of-control versions of `on` and `once`. Tell *this* object to listen to
  // an event in another object ... keeping track of what it\'s listening to.
  _.each(listenMethods, function(implementation, method) {
    Events[method] = function(obj, name, callback) {
      var listeners = this._listeners || (this._listeners = {});
      var id = obj._listenerId || (obj._listenerId = _.uniqueId(\'l\'));
      listeners[id] = obj;
      obj[implementation](name, typeof name === \'object\' ? this : callback, this);
      return this;
    };
  });

  // Aliases for backwards compatibility.
  Events.bind   = Events.on;
  Events.unbind = Events.off;

  // Allow the `Backbone` object to serve as a global event bus, for folks who
  // want global "pubsub" in a convenient place.
  _.extend(Backbone, Events);

  // Backbone.Model
  // --------------

  // Create a new model, with defined attributes. A client id (`cid`)
  // is automatically generated and assigned for you.
  var Model = Backbone.Model = function(attributes, options) {
    var defaults;
    var attrs = attributes || {};
    this.cid = _.uniqueId(\'c\');
    this.attributes = {};
    if (options && options.collection) this.collection = options.collection;
    if (options && options.parse) attrs = this.parse(attrs, options) || {};
    if (defaults = _.result(this, \'defaults\')) {
      attrs = _.defaults({}, attrs, defaults);
    }
    this.set(attrs, options);
    this.changed = {};
    this.initialize.apply(this, arguments);
  };

  // Attach all inheritable methods to the Model prototype.
  _.extend(Model.prototype, Events, {

    // A hash of attributes whose current and previous value differ.
    changed: null,

    // The value returned during the last failed validation.
    validationError: null,

    // The default name for the JSON `id` attribute is `"id"`. MongoDB and
    // CouchDB users may want to set this to `"_id"`.
    idAttribute: \'id\',

    // Initialize is an empty function by default. Override it with your own
    // initialization logic.
    initialize: function(){},

    // Return a copy of the model\'s `attributes` object.
    toJSON: function(options) {
      return _.clone(this.attributes);
    },

    // Proxy `Backbone.sync` by default.
    sync: function() {
      return Backbone.sync.apply(this, arguments);
    },

    // Get the value of an attribute.
    get: function(attr) {
      return this.attributes[attr];
    },

    // Get the HTML-escaped value of an attribute.
    escape: function(attr) {
      return _.escape(this.get(attr));
    },

    // Returns `true` if the attribute contains a value that is not null
    // or undefined.
    has: function(attr) {
      return this.get(attr) != null;
    },

    // ----------------------------------------------------------------------

    // Set a hash of model attributes on the object, firing `"change"` unless
    // you choose to silence it.
    set: function(key, val, options) {
      var attr, attrs, unset, changes, silent, changing, prev, current;
      if (key == null) return this;

      // Handle both `"key", value` and `{key: value}` -style arguments.
      if (typeof key === \'object\') {
        attrs = key;
        options = val;
      } else {
        (attrs = {})[key] = val;
      }

      options || (options = {});

      // Run validation.
      if (!this._validate(attrs, options)) return false;

      // Extract attributes and options.
      unset           = options.unset;
      silent          = options.silent;
      changes         = [];
      changing        = this._changing;
      this._changing  = true;

      if (!changing) {
        this._previousAttributes = _.clone(this.attributes);
        this.changed = {};
      }
      current = this.attributes, prev = this._previousAttributes;

      // Check for changes of `id`.
      if (this.idAttribute in attrs) this.id = attrs[this.idAttribute];

      // For each `set` attribute, update or delete the current value.
      for (attr in attrs) {
        val = attrs[attr];
        if (!_.isEqual(current[attr], val)) changes.push(attr);
        if (!_.isEqual(prev[attr], val)) {
          this.changed[attr] = val;
        } else {
          delete this.changed[attr];
        }
        unset ? delete current[attr] : current[attr] = val;
      }

      // Trigger all relevant attribute changes.
      if (!silent) {
        if (changes.length) this._pending = true;
        for (var i = 0, l = changes.length; i < l; i++) {
          this.trigger(\'change:\' + changes[i], this, current[changes[i]], options);
        }
      }

      if (changing) return this;
      if (!silent) {
        while (this._pending) {
          this._pending = false;
          this.trigger(\'change\', this, options);
        }
      }
      this._pending = false;
      this._changing = false;
      return this;
    },

    // Remove an attribute from the model, firing `"change"` unless you choose
    // to silence it. `unset` is a noop if the attribute doesn\'t exist.
    unset: function(attr, options) {
      return this.set(attr, void 0, _.extend({}, options, {unset: true}));
    },

    // Clear all attributes on the model, firing `"change"` unless you choose
    // to silence it.
    clear: function(options) {
      var attrs = {};
      for (var key in this.attributes) attrs[key] = void 0;
      return this.set(attrs, _.extend({}, options, {unset: true}));
    },

    // Determine if the model has changed since the last `"change"` event.
    // If you specify an attribute name, determine if that attribute has changed.
    hasChanged: function(attr) {
      if (attr == null) return !_.isEmpty(this.changed);
      return _.has(this.changed, attr);
    },

    // Return an object containing all the attributes that have changed, or
    // false if there are no changed attributes. Useful for determining what
    // parts of a view need to be updated and/or what attributes need to be
    // persisted to the server. Unset attributes will be set to undefined.
    // You can also pass an attributes object to diff against the model,
    // determining if there *would be* a change.
    changedAttributes: function(diff) {
      if (!diff) return this.hasChanged() ? _.clone(this.changed) : false;
      var val, changed = false;
      var old = this._changing ? this._previousAttributes : this.attributes;
      for (var attr in diff) {
        if (_.isEqual(old[attr], (val = diff[attr]))) continue;
        (changed || (changed = {}))[attr] = val;
      }
      return changed;
    },

    // Get the previous value of an attribute, recorded at the time the last
    // `"change"` event was fired.
    previous: function(attr) {
      if (attr == null || !this._previousAttributes) return null;
      return this._previousAttributes[attr];
    },

    // Get all of the attributes of the model at the time of the previous
    // `"change"` event.
    previousAttributes: function() {
      return _.clone(this._previousAttributes);
    },

    // ---------------------------------------------------------------------

    // Fetch the model from the server. If the server\'s representation of the
    // model differs from its current attributes, they will be overriden,
    // triggering a `"change"` event.
    fetch: function(options) {
      options = options ? _.clone(options) : {};
      if (options.parse === void 0) options.parse = true;
      var success = options.success;
      options.success = function(model, resp, options) {
        if (!model.set(model.parse(resp, options), options)) return false;
        if (success) success(model, resp, options);
      };
      return this.sync(\'read\', this, options);
    },

    // Set a hash of model attributes, and sync the model to the server.
    // If the server returns an attributes hash that differs, the model\'s
    // state will be `set` again.
    save: function(key, val, options) {
      var attrs, success, method, xhr, attributes = this.attributes;

      // Handle both `"key", value` and `{key: value}` -style arguments.
      if (key == null || typeof key === \'object\') {
        attrs = key;
        options = val;
      } else {
        (attrs = {})[key] = val;
      }

      // If we\'re not waiting and attributes exist, save acts as `set(attr).save(null, opts)`.
      if (attrs && (!options || !options.wait) && !this.set(attrs, options)) return false;

      options = _.extend({validate: true}, options);

      // Do not persist invalid models.
      if (!this._validate(attrs, options)) return false;

      // Set temporary attributes if `{wait: true}`.
      if (attrs && options.wait) {
        this.attributes = _.extend({}, attributes, attrs);
      }

      // After a successful server-side save, the client is (optionally)
      // updated with the server-side state.
      if (options.parse === void 0) options.parse = true;
      success = options.success;
      options.success = function(model, resp, options) {
        // Ensure attributes are restored during synchronous saves.
        model.attributes = attributes;
        var serverAttrs = model.parse(resp, options);
        if (options.wait) serverAttrs = _.extend(attrs || {}, serverAttrs);
        if (_.isObject(serverAttrs) && !model.set(serverAttrs, options)) {
          return false;
        }
        if (success) success(model, resp, options);
      };

      // Finish configuring and sending the Ajax request.
      method = this.isNew() ? \'create\' : (options.patch ? \'patch\' : \'update\');
      if (method === \'patch\') options.attrs = attrs;
      xhr = this.sync(method, this, options);

      // Restore attributes.
      if (attrs && options.wait) this.attributes = attributes;

      return xhr;
    },

    // Destroy this model on the server if it was already persisted.
    // Optimistically removes the model from its collection, if it has one.
    // If `wait: true` is passed, waits for the server to respond before removal.
    destroy: function(options) {
      options = options ? _.clone(options) : {};
      var model = this;
      var success = options.success;

      var destroy = function() {
        model.trigger(\'destroy\', model, model.collection, options);
      };

      options.success = function(model, resp, options) {
        if (options.wait || model.isNew()) destroy();
        if (success) success(model, resp, options);
      };

      if (this.isNew()) {
        options.success(this, null, options);
        return false;
      }

      var xhr = this.sync(\'delete\', this, options);
      if (!options.wait) destroy();
      return xhr;
    },

    // Default URL for the model\'s representation on the server -- if you\'re
    // using Backbone\'s restful methods, override this to change the endpoint
    // that will be called.
    url: function() {
      var base = _.result(this, \'urlRoot\') || _.result(this.collection, \'url\') || urlError();
      if (this.isNew()) return base;
      return base + (base.charAt(base.length - 1) === \'/\' ? \'\' : \'/\') + encodeURIComponent(this.id);
    },

    // **parse** converts a response into the hash of attributes to be `set` on
    // the model. The default implementation is just to pass the response along.
    parse: function(resp, options) {
      return resp;
    },

    // Create a new model with identical attributes to this one.
    clone: function() {
      return new this.constructor(this.attributes);
    },

    // A model is new if it has never been saved to the server, and lacks an id.
    isNew: function() {
      return this.id == null;
    },

    // Check if the model is currently in a valid state.
    isValid: function(options) {
      return !this.validate || !this.validate(this.attributes, options);
    },

    // Run validation against the next complete set of model attributes,
    // returning `true` if all is well. Otherwise, fire an
    // `"invalid"` event and call the invalid callback, if specified.
    _validate: function(attrs, options) {
      if (!options.validate || !this.validate) return true;
      attrs = _.extend({}, this.attributes, attrs);
      var error = this.validationError = this.validate(attrs, options) || null;
      if (!error) return true;
      this.trigger(\'invalid\', this, error, options || {});
      return false;
    }

  });

  // Backbone.Collection
  // -------------------

  // Provides a standard collection class for our sets of models, ordered
  // or unordered. If a `comparator` is specified, the Collection will maintain
  // its models in sort order, as they\'re added and removed.
  var Collection = Backbone.Collection = function(models, options) {
    options || (options = {});
    if (options.model) this.model = options.model;
    if (options.comparator !== void 0) this.comparator = options.comparator;
    this._reset();
    this.initialize.apply(this, arguments);
    if (models) this.reset(models, _.extend({silent: true}, options));
  };

  // Define the Collection\'s inheritable methods.
  _.extend(Collection.prototype, Events, {

    // The default model for a collection is just a **Backbone.Model**.
    // This should be overridden in most cases.
    model: Model,

    // Initialize is an empty function by default. Override it with your own
    // initialization logic.
    initialize: function(){},

    // The JSON representation of a Collection is an array of the
    // models\' attributes.
    toJSON: function(options) {
      return this.map(function(model){ return model.toJSON(options); });
    },

    // Proxy `Backbone.sync` by default.
    sync: function() {
      return Backbone.sync.apply(this, arguments);
    },

    // Add a model, or list of models to the set.
    add: function(models, options) {
      models = _.isArray(models) ? models.slice() : [models];
      options || (options = {});
      var i, l, model, attrs, existing, doSort, add, at, sort, sortAttr;
      add = [];
      at = options.at;
      sort = this.comparator && (at == null) && options.sort !== false;
      sortAttr = _.isString(this.comparator) ? this.comparator : null;

      // Turn bare objects into model references, and prevent invalid models
      // from being added.
      for (i = 0, l = models.length; i < l; i++) {
        if (!(model = this._prepareModel(attrs = models[i], options))) {
          this.trigger(\'invalid\', this, attrs, options);
          continue;
        }

        // If a duplicate is found, prevent it from being added and
        // optionally merge it into the existing model.
        if (existing = this.get(model)) {
          if (options.merge) {
            existing.set(attrs === model ? model.attributes : attrs, options);
            if (sort && !doSort && existing.hasChanged(sortAttr)) doSort = true;
          }
          continue;
        }

        // This is a new model, push it to the `add` list.
        add.push(model);

        // Listen to added models\' events, and index models for lookup by
        // `id` and by `cid`.
        model.on(\'all\', this._onModelEvent, this);
        this._byId[model.cid] = model;
        if (model.id != null) this._byId[model.id] = model;
      }

      // See if sorting is needed, update `length` and splice in new models.
      if (add.length) {
        if (sort) doSort = true;
        this.length += add.length;
        if (at != null) {
          splice.apply(this.models, [at, 0].concat(add));
        } else {
          push.apply(this.models, add);
        }
      }

      // Silently sort the collection if appropriate.
      if (doSort) this.sort({silent: true});

      if (options.silent) return this;

      // Trigger `add` events.
      for (i = 0, l = add.length; i < l; i++) {
        (model = add[i]).trigger(\'add\', model, this, options);
      }

      // Trigger `sort` if the collection was sorted.
      if (doSort) this.trigger(\'sort\', this, options);

      return this;
    },

    // Remove a model, or a list of models from the set.
    remove: function(models, options) {
      models = _.isArray(models) ? models.slice() : [models];
      options || (options = {});
      var i, l, index, model;
      for (i = 0, l = models.length; i < l; i++) {
        model = this.get(models[i]);
        if (!model) continue;
        delete this._byId[model.id];
        delete this._byId[model.cid];
        index = this.indexOf(model);
        this.models.splice(index, 1);
        this.length--;
        if (!options.silent) {
          options.index = index;
          model.trigger(\'remove\', model, this, options);
        }
        this._removeReference(model);
      }
      return this;
    },

    // Add a model to the end of the collection.
    push: function(model, options) {
      model = this._prepareModel(model, options);
      this.add(model, _.extend({at: this.length}, options));
      return model;
    },

    // Remove a model from the end of the collection.
    pop: function(options) {
      var model = this.at(this.length - 1);
      this.remove(model, options);
      return model;
    },

    // Add a model to the beginning of the collection.
    unshift: function(model, options) {
      model = this._prepareModel(model, options);
      this.add(model, _.extend({at: 0}, options));
      return model;
    },

    // Remove a model from the beginning of the collection.
    shift: function(options) {
      var model = this.at(0);
      this.remove(model, options);
      return model;
    },

    // Slice out a sub-array of models from the collection.
    slice: function(begin, end) {
      return this.models.slice(begin, end);
    },

    // Get a model from the set by id.
    get: function(obj) {
      if (obj == null) return void 0;
      this._idAttr || (this._idAttr = this.model.prototype.idAttribute);
      return this._byId[obj.id || obj.cid || obj[this._idAttr] || obj];
    },

    // Get the model at the given index.
    at: function(index) {
      return this.models[index];
    },

    // Return models with matching attributes. Useful for simple cases of
    // `filter`.
    where: function(attrs, first) {
      if (_.isEmpty(attrs)) return first ? void 0 : [];
      return this[first ? \'find\' : \'filter\'](function(model) {
        for (var key in attrs) {
          if (attrs[key] !== model.get(key)) return false;
        }
        return true;
      });
    },

    // Return the first model with matching attributes. Useful for simple cases
    // of `find`.
    findWhere: function(attrs) {
      return this.where(attrs, true);
    },

    // Force the collection to re-sort itself. You don\'t need to call this under
    // normal circumstances, as the set will maintain sort order as each item
    // is added.
    sort: function(options) {
      if (!this.comparator) {
        throw new Error(\'Cannot sort a set without a comparator\');
      }
      options || (options = {});

      // Run sort based on type of `comparator`.
      if (_.isString(this.comparator) || this.comparator.length === 1) {
        this.models = this.sortBy(this.comparator, this);
      } else {
        this.models.sort(_.bind(this.comparator, this));
      }

      if (!options.silent) this.trigger(\'sort\', this, options);
      return this;
    },

    // Pluck an attribute from each model in the collection.
    pluck: function(attr) {
      return _.invoke(this.models, \'get\', attr);
    },

    // Smartly update a collection with a change set of models, adding,
    // removing, and merging as necessary.
    update: function(models, options) {
      options = _.extend({add: true, merge: true, remove: true}, options);
      if (options.parse) models = this.parse(models, options);
      var model, i, l, existing;
      var add = [], remove = [], modelMap = {};

      // Allow a single model (or no argument) to be passed.
      if (!_.isArray(models)) models = models ? [models] : [];

      // Proxy to `add` for this case, no need to iterate...
      if (options.add && !options.remove) return this.add(models, options);

      // Determine which models to add and merge, and which to remove.
      for (i = 0, l = models.length; i < l; i++) {
        model = models[i];
        existing = this.get(model);
        if (options.remove && existing) modelMap[existing.cid] = true;
        if ((options.add && !existing) || (options.merge && existing)) {
          add.push(model);
        }
      }
      if (options.remove) {
        for (i = 0, l = this.models.length; i < l; i++) {
          model = this.models[i];
          if (!modelMap[model.cid]) remove.push(model);
        }
      }

      // Remove models (if applicable) before we add and merge the rest.
      if (remove.length) this.remove(remove, options);
      if (add.length) this.add(add, options);
      return this;
    },

    // When you have more items than you want to add or remove individually,
    // you can reset the entire set with a new list of models, without firing
    // any `add` or `remove` events. Fires `reset` when finished.
    reset: function(models, options) {
      options || (options = {});
      if (options.parse) models = this.parse(models, options);
      for (var i = 0, l = this.models.length; i < l; i++) {
        this._removeReference(this.models[i]);
      }
      options.previousModels = this.models;
      this._reset();
      if (models) this.add(models, _.extend({silent: true}, options));
      if (!options.silent) this.trigger(\'reset\', this, options);
      return this;
    },

    // Fetch the default set of models for this collection, resetting the
    // collection when they arrive. If `update: true` is passed, the response
    // data will be passed through the `update` method instead of `reset`.
    fetch: function(options) {
      options = options ? _.clone(options) : {};
      if (options.parse === void 0) options.parse = true;
      var success = options.success;
      options.success = function(collection, resp, options) {
        var method = options.update ? \'update\' : \'reset\';
        collection[method](resp, options);
        if (success) success(collection, resp, options);
      };
      return this.sync(\'read\', this, options);
    },

    // Create a new instance of a model in this collection. Add the model to the
    // collection immediately, unless `wait: true` is passed, in which case we
    // wait for the server to agree.
    create: function(model, options) {
      options = options ? _.clone(options) : {};
      if (!(model = this._prepareModel(model, options))) return false;
      if (!options.wait) this.add(model, options);
      var collection = this;
      var success = options.success;
      options.success = function(model, resp, options) {
        if (options.wait) collection.add(model, options);
        if (success) success(model, resp, options);
      };
      model.save(null, options);
      return model;
    },

    // **parse** converts a response into a list of models to be added to the
    // collection. The default implementation is just to pass it through.
    parse: function(resp, options) {
      return resp;
    },

    // Create a new collection with an identical list of models as this one.
    clone: function() {
      return new this.constructor(this.models);
    },

    // Reset all internal state. Called when the collection is reset.
    _reset: function() {
      this.length = 0;
      this.models = [];
      this._byId  = {};
    },

    // Prepare a model or hash of attributes to be added to this collection.
    _prepareModel: function(attrs, options) {
      if (attrs instanceof Model) {
        if (!attrs.collection) attrs.collection = this;
        return attrs;
      }
      options || (options = {});
      options.collection = this;
      var model = new this.model(attrs, options);
      if (!model._validate(attrs, options)) return false;
      return model;
    },

    // Internal method to remove a model\'s ties to a collection.
    _removeReference: function(model) {
      if (this === model.collection) delete model.collection;
      model.off(\'all\', this._onModelEvent, this);
    },

    // Internal method called every time a model in the set fires an event.
    // Sets need to update their indexes when models change ids. All other
    // events simply proxy through. "add" and "remove" events that originate
    // in other collections are ignored.
    _onModelEvent: function(event, model, collection, options) {
      if ((event === \'add\' || event === \'remove\') && collection !== this) return;
      if (event === \'destroy\') this.remove(model, options);
      if (model && event === \'change:\' + model.idAttribute) {
        delete this._byId[model.previous(model.idAttribute)];
        if (model.id != null) this._byId[model.id] = model;
      }
      this.trigger.apply(this, arguments);
    },

    sortedIndex: function (model, value, context) {
      value || (value = this.comparator);
      var iterator = _.isFunction(value) ? value : function(model) {
        return model.get(value);
      };
      return _.sortedIndex(this.models, model, iterator, context);
    }

  });

  // Underscore methods that we want to implement on the Collection.
  var methods = [\'forEach\', \'each\', \'map\', \'collect\', \'reduce\', \'foldl\',
    \'inject\', \'reduceRight\', \'foldr\', \'find\', \'detect\', \'filter\', \'select\',
    \'reject\', \'every\', \'all\', \'some\', \'any\', \'include\', \'contains\', \'invoke\',
    \'max\', \'min\', \'toArray\', \'size\', \'first\', \'head\', \'take\', \'initial\', \'rest\',
    \'tail\', \'drop\', \'last\', \'without\', \'indexOf\', \'shuffle\', \'lastIndexOf\',
    \'isEmpty\', \'chain\'];

  // Mix in each Underscore method as a proxy to `Collection#models`.
  _.each(methods, function(method) {
    Collection.prototype[method] = function() {
      var args = slice.call(arguments);
      args.unshift(this.models);
      return _[method].apply(_, args);
    };
  });

  // Underscore methods that take a property name as an argument.
  var attributeMethods = [\'groupBy\', \'countBy\', \'sortBy\'];

  // Use attributes instead of properties.
  _.each(attributeMethods, function(method) {
    Collection.prototype[method] = function(value, context) {
      var iterator = _.isFunction(value) ? value : function(model) {
        return model.get(value);
      };
      return _[method](this.models, iterator, context);
    };
  });

  // Backbone.Router
  // ---------------

  // Routers map faux-URLs to actions, and fire events when routes are
  // matched. Creating a new one sets its `routes` hash, if not set statically.
  var Router = Backbone.Router = function(options) {
    options || (options = {});
    if (options.routes) this.routes = options.routes;
    this._bindRoutes();
    this.initialize.apply(this, arguments);
  };

  // Cached regular expressions for matching named param parts and splatted
  // parts of route strings.
  var optionalParam = /\\((.*?)\\)/g;
  var namedParam    = /(\\(\\?)?:\\w+/g;
  var splatParam    = /\\*\\w+/g;
  var escapeRegExp  = /[\\-{}\\[\\]+?.,\\\\\\^$|#\\s]/g;

  // Set up all inheritable **Backbone.Router** properties and methods.
  _.extend(Router.prototype, Events, {

    // Initialize is an empty function by default. Override it with your own
    // initialization logic.
    initialize: function(){},

    // Manually bind a single named route to a callback. For example:
    //
    //     this.route(\'search/:query/p:num\', \'search\', function(query, num) {
    //       ...
    //     });
    //
    route: function(route, name, callback) {
      if (!_.isRegExp(route)) route = this._routeToRegExp(route);
      if (!callback) callback = this[name];
      Backbone.history.route(route, _.bind(function(fragment) {
        var args = this._extractParameters(route, fragment);
        callback && callback.apply(this, args);
        this.trigger.apply(this, [\'route:\' + name].concat(args));
        this.trigger(\'route\', name, args);
        Backbone.history.trigger(\'route\', this, name, args);
      }, this));
      return this;
    },

    // Simple proxy to `Backbone.history` to save a fragment into the history.
    navigate: function(fragment, options) {
      Backbone.history.navigate(fragment, options);
      return this;
    },

    // Bind all defined routes to `Backbone.history`. We have to reverse the
    // order of the routes here to support behavior where the most general
    // routes can be defined at the bottom of the route map.
    _bindRoutes: function() {
      if (!this.routes) return;
      var route, routes = _.keys(this.routes);
      while ((route = routes.pop()) != null) {
        this.route(route, this.routes[route]);
      }
    },

    // Convert a route string into a regular expression, suitable for matching
    // against the current location hash.
    _routeToRegExp: function(route) {
      route = route.replace(escapeRegExp, \'\\\\$&\')
                   .replace(optionalParam, \'(?:$1)?\')
                   .replace(namedParam, function(match, optional){
                     return optional ? match : \'([^\\/]+)\';
                   })
                   .replace(splatParam, \'(.*?)\');
      return new RegExp(\'^\' + route + \'$\');
    },

    // Given a route, and a URL fragment that it matches, return the array of
    // extracted parameters.
    _extractParameters: function(route, fragment) {
      return route.exec(fragment).slice(1);
    }

  });

  // Backbone.History
  // ----------------

  // Handles cross-browser history management, based on URL fragments. If the
  // browser does not support `onhashchange`, falls back to polling.
  var History = Backbone.History = function() {
    this.handlers = [];
    _.bindAll(this, \'checkUrl\');

    // Ensure that `History` can be used outside of the browser.
    if (typeof window !== \'undefined\') {
      this.location = window.location;
      this.history = window.history;
    }
  };

  // Cached regex for stripping a leading hash/slash and trailing space.
  var routeStripper = /^[#\\/]|\\s+$/g;

  // Cached regex for stripping leading and trailing slashes.
  var rootStripper = /^\\/+|\\/+$/g;

  // Cached regex for detecting MSIE.
  var isExplorer = /msie [\\w.]+/;

  // Cached regex for removing a trailing slash.
  var trailingSlash = /\\/$/;

  // Has the history handling already been started?
  History.started = false;

  // Set up all inheritable **Backbone.History** properties and methods.
  _.extend(History.prototype, Events, {

    // The default interval to poll for hash changes, if necessary, is
    // twenty times a second.
    interval: 50,

    // Gets the true hash value. Cannot use location.hash directly due to bug
    // in Firefox where location.hash will always be decoded.
    getHash: function(window) {
      var match = (window || this).location.href.match(/#(.*)$/);
      return match ? match[1] : \'\';
    },

    // Get the cross-browser normalized URL fragment, either from the URL,
    // the hash, or the override.
    getFragment: function(fragment, forcePushState) {
      if (fragment == null) {
        if (this._hasPushState || !this._wantsHashChange || forcePushState) {
          fragment = this.location.pathname;
          var root = this.root.replace(trailingSlash, \'\');
          if (!fragment.indexOf(root)) fragment = fragment.substr(root.length);
        } else {
          fragment = this.getHash();
        }
      }
      return fragment.replace(routeStripper, \'\');
    },

    // Start the hash change handling, returning `true` if the current URL matches
    // an existing route, and `false` otherwise.
    start: function(options) {
      if (History.started) throw new Error("Backbone.history has already been started");
      History.started = true;

      // Figure out the initial configuration. Do we need an iframe?
      // Is pushState desired ... is it available?
      this.options          = _.extend({}, {root: \'/\'}, this.options, options);
      this.root             = this.options.root;
      this._wantsHashChange = this.options.hashChange !== false;
      this._wantsPushState  = !!this.options.pushState;
      this._hasPushState    = !!(this.options.pushState && this.history && this.history.pushState);
      var fragment          = this.getFragment();
      var docMode           = document.documentMode;
      var oldIE             = (isExplorer.exec(navigator.userAgent.toLowerCase()) && (!docMode || docMode <= 7));

      // Normalize root to always include a leading and trailing slash.
      this.root = (\'/\' + this.root + \'/\').replace(rootStripper, \'/\');

      if (oldIE && this._wantsHashChange) {
        this.iframe = Backbone.$(\'<iframe src="javascript:0" tabindex="-1" />\').hide().appendTo(\'body\')[0].contentWindow;
        this.navigate(fragment);
      }

      // Depending on whether we\'re using pushState or hashes, and whether
      // \'onhashchange\' is supported, determine how we check the URL state.
      if (this._hasPushState) {
        Backbone.$(window).on(\'popstate\', this.checkUrl);
      } else if (this._wantsHashChange && (\'onhashchange\' in window) && !oldIE) {
        Backbone.$(window).on(\'hashchange\', this.checkUrl);
      } else if (this._wantsHashChange) {
        this._checkUrlInterval = setInterval(this.checkUrl, this.interval);
      }

      // Determine if we need to change the base url, for a pushState link
      // opened by a non-pushState browser.
      this.fragment = fragment;
      var loc = this.location;
      var atRoot = loc.pathname.replace(/[^\\/]$/, \'$&/\') === this.root;

      // If we\'ve started off with a route from a `pushState`-enabled browser,
      // but we\'re currently in a browser that doesn\'t support it...
      if (this._wantsHashChange && this._wantsPushState && !this._hasPushState && !atRoot) {
        this.fragment = this.getFragment(null, true);
        this.location.replace(this.root + this.location.search + \'#\' + this.fragment);
        // Return immediately as browser will do redirect to new url
        return true;

      // Or if we\'ve started out with a hash-based route, but we\'re currently
      // in a browser where it could be `pushState`-based instead...
      } else if (this._wantsPushState && this._hasPushState && atRoot && loc.hash) {
        this.fragment = this.getHash().replace(routeStripper, \'\');
        this.history.replaceState({}, document.title, this.root + this.fragment + loc.search);
      }

      if (!this.options.silent) return this.loadUrl();
    },

    // Disable Backbone.history, perhaps temporarily. Not useful in a real app,
    // but possibly useful for unit testing Routers.
    stop: function() {
      Backbone.$(window).off(\'popstate\', this.checkUrl).off(\'hashchange\', this.checkUrl);
      clearInterval(this._checkUrlInterval);
      History.started = false;
    },

    // Add a route to be tested when the fragment changes. Routes added later
    // may override previous routes.
    route: function(route, callback) {
      this.handlers.unshift({route: route, callback: callback});
    },

    // Checks the current URL to see if it has changed, and if it has,
    // calls `loadUrl`, normalizing across the hidden iframe.
    checkUrl: function(e) {
      var current = this.getFragment();
      if (current === this.fragment && this.iframe) {
        current = this.getFragment(this.getHash(this.iframe));
      }
      if (current === this.fragment) return false;
      if (this.iframe) this.navigate(current);
      this.loadUrl() || this.loadUrl(this.getHash());
    },

    // Attempt to load the current URL fragment. If a route succeeds with a
    // match, returns `true`. If no defined routes matches the fragment,
    // returns `false`.
    loadUrl: function(fragmentOverride) {
      var fragment = this.fragment = this.getFragment(fragmentOverride);
      var matched = _.any(this.handlers, function(handler) {
        if (handler.route.test(fragment)) {
          handler.callback(fragment);
          return true;
        }
      });
      return matched;
    },

    // Save a fragment into the hash history, or replace the URL state if the
    // \'replace\' option is passed. You are responsible for properly URL-encoding
    // the fragment in advance.
    //
    // The options object can contain `trigger: true` if you wish to have the
    // route callback be fired (not usually desirable), or `replace: true`, if
    // you wish to modify the current URL without adding an entry to the history.
    navigate: function(fragment, options) {
      if (!History.started) return false;
      if (!options || options === true) options = {trigger: options};
      fragment = this.getFragment(fragment || \'\');
      if (this.fragment === fragment) return;
      this.fragment = fragment;
      var url = this.root + fragment;

      // If pushState is available, we use it to set the fragment as a real URL.
      if (this._hasPushState) {
        this.history[options.replace ? \'replaceState\' : \'pushState\']({}, document.title, url);

      // If hash changes haven\'t been explicitly disabled, update the hash
      // fragment to store history.
      } else if (this._wantsHashChange) {
        this._updateHash(this.location, fragment, options.replace);
        if (this.iframe && (fragment !== this.getFragment(this.getHash(this.iframe)))) {
          // Opening and closing the iframe tricks IE7 and earlier to push a
          // history entry on hash-tag change.  When replace is true, we don\'t
          // want this.
          if(!options.replace) this.iframe.document.open().close();
          this._updateHash(this.iframe.location, fragment, options.replace);
        }

      // If you\'ve told us that you explicitly don\'t want fallback hashchange-
      // based history, then `navigate` becomes a page refresh.
      } else {
        return this.location.assign(url);
      }
      if (options.trigger) this.loadUrl(fragment);
    },

    // Update the hash location, either replacing the current entry, or adding
    // a new one to the browser history.
    _updateHash: function(location, fragment, replace) {
      if (replace) {
        var href = location.href.replace(/(javascript:|#).*$/, \'\');
        location.replace(href + \'#\' + fragment);
      } else {
        // Some browsers require that `hash` contains a leading #.
        location.hash = \'#\' + fragment;
      }
    }

  });

  // Create the default Backbone.history.
  Backbone.history = new History;

  // Backbone.View
  // -------------

  // Creating a Backbone.View creates its initial element outside of the DOM,
  // if an existing element is not provided...
  var View = Backbone.View = function(options) {
    this.cid = _.uniqueId(\'view\');
    this._configure(options || {});
    this._ensureElement();
    this.initialize.apply(this, arguments);
    this.delegateEvents();
  };

  // Cached regex to split keys for `delegate`.
  var delegateEventSplitter = /^(\\S+)\\s*(.*)$/;

  // List of view options to be merged as properties.
  var viewOptions = [\'model\', \'collection\', \'el\', \'id\', \'attributes\', \'className\', \'tagName\', \'events\'];

  // Set up all inheritable **Backbone.View** properties and methods.
  _.extend(View.prototype, Events, {

    // The default `tagName` of a View\'s element is `"div"`.
    tagName: \'div\',

    // jQuery delegate for element lookup, scoped to DOM elements within the
    // current view. This should be prefered to global lookups where possible.
    $: function(selector) {
      return this.$el.find(selector);
    },

    // Initialize is an empty function by default. Override it with your own
    // initialization logic.
    initialize: function(){},

    // **render** is the core function that your view should override, in order
    // to populate its element (`this.el`), with the appropriate HTML. The
    // convention is for **render** to always return `this`.
    render: function() {
      return this;
    },

    // Remove this view by taking the element out of the DOM, and removing any
    // applicable Backbone.Events listeners.
    remove: function() {
      this.$el.remove();
      this.stopListening();
      return this;
    },

    // Change the view\'s element (`this.el` property), including event
    // re-delegation.
    setElement: function(element, delegate) {
      if (this.$el) this.undelegateEvents();
      this.$el = element instanceof Backbone.$ ? element : Backbone.$(element);
      this.el = this.$el[0];
      if (delegate !== false) this.delegateEvents();
      return this;
    },

    // Set callbacks, where `this.events` is a hash of
    //
    // *{"event selector": "callback"}*
    //
    //     {
    //       \'mousedown .title\':  \'edit\',
    //       \'click .button\':     \'save\'
    //       \'click .open\':       function(e) { ... }
    //     }
    //
    // pairs. Callbacks will be bound to the view, with `this` set properly.
    // Uses event delegation for efficiency.
    // Omitting the selector binds the event to `this.el`.
    // This only works for delegate-able events: not `focus`, `blur`, and
    // not `change`, `submit`, and `reset` in Internet Explorer.
    delegateEvents: function(events) {
      if (!(events || (events = _.result(this, \'events\')))) return this;
      this.undelegateEvents();
      for (var key in events) {
        var method = events[key];
        if (!_.isFunction(method)) method = this[events[key]];
        if (!method) throw new Error(\'Method "\' + events[key] + \'" does not exist\');
        var match = key.match(delegateEventSplitter);
        var eventName = match[1], selector = match[2];
        method = _.bind(method, this);
        eventName += \'.delegateEvents\' + this.cid;
        if (selector === \'\') {
          this.$el.on(eventName, method);
        } else {
          this.$el.on(eventName, selector, method);
        }
      }
      return this;
    },

    // Clears all callbacks previously bound to the view with `delegateEvents`.
    // You usually don\'t need to use this, but may wish to if you have multiple
    // Backbone views attached to the same DOM element.
    undelegateEvents: function() {
      this.$el.off(\'.delegateEvents\' + this.cid);
      return this;
    },

    // Performs the initial configuration of a View with a set of options.
    // Keys with special meaning *(model, collection, id, className)*, are
    // attached directly to the view.
    _configure: function(options) {
      if (this.options) options = _.extend({}, _.result(this, \'options\'), options);
      _.extend(this, _.pick(options, viewOptions));
      this.options = options;
    },

    // Ensure that the View has a DOM element to render into.
    // If `this.el` is a string, pass it through `$()`, take the first
    // matching element, and re-assign it to `el`. Otherwise, create
    // an element from the `id`, `className` and `tagName` properties.
    _ensureElement: function() {
      if (!this.el) {
        var attrs = _.extend({}, _.result(this, \'attributes\'));
        if (this.id) attrs.id = _.result(this, \'id\');
        if (this.className) attrs[\'class\'] = _.result(this, \'className\');
        var $el = Backbone.$(\'<\' + _.result(this, \'tagName\') + \'>\').attr(attrs);
        this.setElement($el, false);
      } else {
        this.setElement(_.result(this, \'el\'), false);
      }
    }

  });

  // Backbone.sync
  // -------------

  // Map from CRUD to HTTP for our default `Backbone.sync` implementation.
  var methodMap = {
    \'create\': \'POST\',
    \'update\': \'PUT\',
    \'patch\':  \'PATCH\',
    \'delete\': \'DELETE\',
    \'read\':   \'GET\'
  };

  // Override this function to change the manner in which Backbone persists
  // models to the server. You will be passed the type of request, and the
  // model in question. By default, makes a RESTful Ajax request
  // to the model\'s `url()`. Some possible customizations could be:
  //
  // * Use `setTimeout` to batch rapid-fire updates into a single request.
  // * Send up the models as XML instead of JSON.
  // * Persist models via WebSockets instead of Ajax.
  //
  // Turn on `Backbone.emulateHTTP` in order to send `PUT` and `DELETE` requests
  // as `POST`, with a `_method` parameter containing the true HTTP method,
  // as well as all requests with the body as `application/x-www-form-urlencoded`
  // instead of `application/json` with the model in a param named `model`.
  // Useful when interfacing with server-side languages like **PHP** that make
  // it difficult to read the body of `PUT` requests.
  Backbone.sync = function(method, model, options) {
    var type = methodMap[method];

    // Default options, unless specified.
    _.defaults(options || (options = {}), {
      emulateHTTP: Backbone.emulateHTTP,
      emulateJSON: Backbone.emulateJSON
    });

    // Default JSON-request options.
    var params = {type: type, dataType: \'json\'};

    // Ensure that we have a URL.
    if (!options.url) {
      params.url = _.result(model, \'url\') || urlError();
    }

    // Ensure that we have the appropriate request data.
    if (options.data == null && model && (method === \'create\' || method === \'update\' || method === \'patch\')) {
      params.contentType = \'application/json\';
      params.data = JSON.stringify(options.attrs || model.toJSON(options));
    }

    // For older servers, emulate JSON by encoding the request into an HTML-form.
    if (options.emulateJSON) {
      params.contentType = \'application/x-www-form-urlencoded\';
      params.data = params.data ? {model: params.data} : {};
    }

    // For older servers, emulate HTTP by mimicking the HTTP method with `_method`
    // And an `X-HTTP-Method-Override` header.
    if (options.emulateHTTP && (type === \'PUT\' || type === \'DELETE\' || type === \'PATCH\')) {
      params.type = \'POST\';
      if (options.emulateJSON) params.data._method = type;
      var beforeSend = options.beforeSend;
      options.beforeSend = function(xhr) {
        xhr.setRequestHeader(\'X-HTTP-Method-Override\', type);
        if (beforeSend) return beforeSend.apply(this, arguments);
      };
    }

    // Don\'t process data on a non-GET request.
    if (params.type !== \'GET\' && !options.emulateJSON) {
      params.processData = false;
    }

    var success = options.success;
    options.success = function(resp) {
      if (success) success(model, resp, options);
      model.trigger(\'sync\', model, resp, options);
    };

    var error = options.error;
    options.error = function(xhr) {
      if (error) error(model, xhr, options);
      model.trigger(\'error\', model, xhr, options);
    };

    // Make the request, allowing the user to override any Ajax options.
    var xhr = options.xhr = Backbone.ajax(_.extend(params, options));
    model.trigger(\'request\', model, xhr, options);
    return xhr;
  };

  // Set the default implementation of `Backbone.ajax` to proxy through to `$`.
  Backbone.ajax = function() {
    return Backbone.$.ajax.apply(Backbone.$, arguments);
  };

  // Helpers
  // -------

  // Helper function to correctly set up the prototype chain, for subclasses.
  // Similar to `goog.inherits`, but uses a hash of prototype properties and
  // class properties to be extended.
  var extend = function(protoProps, staticProps) {
    var parent = this;
    var child;

    // The constructor function for the new subclass is either defined by you
    // (the "constructor" property in your `extend` definition), or defaulted
    // by us to simply call the parent\'s constructor.
    if (protoProps && _.has(protoProps, \'constructor\')) {
      child = protoProps.constructor;
    } else {
      child = function(){ return parent.apply(this, arguments); };
    }

    // Add static properties to the constructor function, if supplied.
    _.extend(child, parent, staticProps);

    // Set the prototype chain to inherit from `parent`, without calling
    // `parent`\'s constructor function.
    var Surrogate = function(){ this.constructor = child; };
    Surrogate.prototype = parent.prototype;
    child.prototype = new Surrogate;

    // Add prototype properties (instance properties) to the subclass,
    // if supplied.
    if (protoProps) _.extend(child.prototype, protoProps);

    // Set a convenience property in case the parent\'s prototype is needed
    // later.
    child.__super__ = parent.prototype;

    return child;
  };

  // Set up inheritance for the model, collection, router, view and history.
  Model.extend = Collection.extend = Router.extend = View.extend = History.extend = extend;

  // Throw an error when a URL is needed, and none is supplied.
  var urlError = function() {
    throw new Error(\'A "url" property or function must be specified\');
  };

}).call(this);
',
    '53f04fe6' => ';/**
 * @name Extend
 * @file 对Zepto做了些扩展，以下所有JS都依赖与此文件
 * @desc 对Zepto一些扩展，组件必须依赖
 * @import core/zepto.js
 */


//     Zepto.js
//     (c) 2010-2012 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

// The following code is heavily inspired by jQuery\'s $.fn.data()

;(function($) {
    var data = {}, dataAttr = $.fn.data, camelize = $.zepto.camelize,
        exp = $.expando = \'Zepto\' + (+new Date())

    // Get value from node:
    // 1. first try key as given,
    // 2. then try camelized key,
    // 3. fall back to reading "data-*" attribute.
    function getData(node, name) {
        var id = node[exp], store = id && data[id]
        if (name === undefined) return store || setData(node)
        else {
            if (store) {
                if (name in store) return store[name]
                var camelName = camelize(name)
                if (camelName in store) return store[camelName]
            }
            return dataAttr.call($(node), name)
        }
    }

    // Store value under camelized key on node
    function setData(node, name, value) {
        var id = node[exp] || (node[exp] = ++$.uuid),
            store = data[id] || (data[id] = attributeData(node))
        if (name !== undefined) store[camelize(name)] = value
        return store
    }

    // Read all "data-*" attributes from a node
    function attributeData(node) {
        var store = {}
        $.each(node.attributes, function(i, attr){
            if (attr.name.indexOf(\'data-\') == 0)
                store[camelize(attr.name.replace(\'data-\', \'\'))] = attr.value
        })
        return store
    }

    $.fn.data = function(name, value) {
        return value === undefined ?
            // set multiple values via object
            $.isPlainObject(name) ?
                this.each(function(i, node){
                    $.each(name, function(key, value){ setData(node, key, value) })
                }) :
                // get value from first element
                this.length == 0 ? undefined : getData(this[0], name) :
            // set value on all elements
            this.each(function(){ setData(this, name, value) })
    }

    $.fn.removeData = function(names) {
        if (typeof names == \'string\') names = names.split(/\\s+/)
        return this.each(function(){
            var id = this[exp], store = id && data[id]
            if (store) $.each(names, function(){ delete store[camelize(this)] })
        })
    }
})(Zepto);

(function($){
    var rootNodeRE = /^(?:body|html)$/i;
    $.extend($.fn, {
        offsetParent: function() {
            return $($.map(this, function(el){
                var parent = el.offsetParent || document.body
                while (parent && !rootNodeRE.test(parent.nodeName) && $(parent).css("position") == "static")
                    parent = parent.offsetParent
                return parent
            }));
        },
        scrollTop: function(){
            if (!this.length) return
            return (\'scrollTop\' in this[0]) ? this[0].scrollTop : this[0].scrollY
        }
    });
    $.extend($, {
        contains: function(parent, node) {
            /**
             * modified by chenluyang
             * @reason ios4 safari下，无法判断包含文字节点的情况
             * @original return parent !== node && parent.contains(node)
             */
            return parent.compareDocumentPosition
                ? !!(parent.compareDocumentPosition(node) & 16)
                : parent !== node && parent.contains(node)
        }
    });
})(Zepto);


//Core.js
;(function($) {
    //扩展在Zepto静态类上
    $.extend($, {
        /**
         * @grammar $.toString(obj)  ⇒ string
         * @name $.toString
         * @desc toString转化
         */
        toString: function(obj) {
            return Object.prototype.toString.call(obj);
        },

        /**
         * @desc 从集合中截取部分数据，这里说的集合，可以是数组，也可以是跟数组性质很像的对象，比如arguments
         * @name $.slice
         * @grammar $.slice(collection, [index])  ⇒ array
         * @example (function(){
         *     var args = $.slice(arguments, 2);
         *     console.log(args); // => [3]
         * })(1, 2, 3);
         */
        slice: function(array, index) {
            return Array.prototype.slice.call(array, index || 0);
        },

        /**
         * @name $.later
         * @grammar $.later(fn, [when, [periodic, [context, [data]]]])  ⇒ timer
         * @desc 延迟执行fn
         * **参数:**
         * - ***fn***: 将要延时执行的方法
         * - ***when***: *可选(默认 0)* 什么时间后执行
         * - ***periodic***: *可选(默认 false)* 设定是否是周期性的执行
         * - ***context***: *可选(默认 undefined)* 给方法设定上下文
         * - ***data***: *可选(默认 undefined)* 给方法设定传入参数
         * @example $.later(function(str){
         *     console.log(this.name + \' \' + str); // => Example hello
         * }, 250, false, {name:\'Example\'}, [\'hello\']);
         */
        later: function(fn, when, periodic, context, data) {
            return window[\'set\' + (periodic ? \'Interval\' : \'Timeout\')](function() {
                fn.apply(context, data);
            }, when || 0);
        },

        /**
         * @desc 解析模版
         * @grammar $.parseTpl(str, data)  ⇒ string
         * @name $.parseTpl
         * @example var str = "<p><%=name%></p>",
         * obj = {name: \'ajean\'};
         * console.log($.parseTpl(str, data)); // => <p>ajean</p>
         */
        parseTpl: function(str, data) {
            var tmpl = \'var __p=[],print=function(){__p.push.apply(__p,arguments);};\' + \'with(obj||{}){__p.push(\\\'\' + str.replace(/\\\\/g, \'\\\\\\\\\').replace(/\'/g, "\\\\\'").replace(/<%=([\\s\\S]+?)%>/g, function(match, code) {
                return "\'," + code.replace(/\\\\\'/g, "\'") + ",\'";
            }).replace(/<%([\\s\\S]+?)%>/g, function(match, code) {
                    return "\');" + code.replace(/\\\\\'/g, "\'").replace(/[\\r\\n\\t]/g, \' \') + "__p.push(\'";
                }).replace(/\\r/g, \'\\\\r\').replace(/\\n/g, \'\\\\n\').replace(/\\t/g, \'\\\\t\') + "\');}return __p.join(\'\');";
            var func = new Function(\'obj\', tmpl);
            return data ? func(data) : func;
        },

        /**
         * @desc 减少执行频率, 多次调用，在指定的时间内，只会执行一次。
         * **options:**
         * - ***delay***: 延时时间
         * - ***fn***: 被稀释的方法
         * - ***debounce_mode***: 是否开启防震动模式, true:start, false:end
         *
         * <code type="text">||||||||||||||||||||||||| (空闲) |||||||||||||||||||||||||
         * X    X    X    X    X    X      X    X    X    X    X    X</code>
         *
         * @grammar $.throttle(delay, fn) ⇒ function
         * @name $.throttle
         * @example var touchmoveHander = function(){
         *     //....
         * }
         * //绑定事件
         * $(document).bind(\'touchmove\', $.throttle(250, touchmoveHander));//频繁滚动，每250ms，执行一次touchmoveHandler
         *
         * //解绑事件
         * $(document).unbind(\'touchmove\', touchmoveHander);//注意这里面unbind还是touchmoveHander,而不是$.throttle返回的function, 当然unbind那个也是一样的效果
         *
         */
        throttle: function(delay, fn, debounce_mode) {
            var last = 0,
                timeId;

            if (typeof fn !== \'function\') {
                debounce_mode = fn;
                fn = delay;
                delay = 250;
            }

            function wrapper() {
                var that = this,
                    period = Date.now() - last,
                    args = arguments;

                function exec() {
                    last = Date.now();
                    fn.apply(that, args);
                };

                function clear() {
                    timeId = undefined;
                };

                if (debounce_mode && !timeId) {
                    // debounce模式 && 第一次调用
                    exec();
                }

                timeId && clearTimeout(timeId);
                if (debounce_mode === undefined && period > delay) {
                    // throttle, 执行到了delay时间
                    exec();
                } else {
                    // debounce, 如果是start就clearTimeout
                    timeId = setTimeout(debounce_mode ? clear : exec, debounce_mode === undefined ? delay - period : delay);
                }
            };
            // for event bind | unbind
            wrapper._zid = fn._zid = fn._zid || $.proxy(fn)._zid;
            return wrapper;
        },

        /**
         * @desc 减少执行频率, 在指定的时间内, 多次调用，只会执行一次。
         * **options:**
         * - ***delay***: 延时时间
         * - ***fn***: 被稀释的方法
         * - ***t***: 指定是在开始处执行，还是结束是执行, true:start, false:end
         *
         * 非at_begin模式
         * <code type="text">||||||||||||||||||||||||| (空闲) |||||||||||||||||||||||||
         *                         X                                X</code>
         * at_begin模式
         * <code type="text">||||||||||||||||||||||||| (空闲) |||||||||||||||||||||||||
         * X                                X                        </code>
         *
         * @grammar $.debounce(delay, fn[, at_begin]) ⇒ function
         * @name $.debounce
         * @example var touchmoveHander = function(){
         *     //....
         * }
         * //绑定事件
         * $(document).bind(\'touchmove\', $.debounce(250, touchmoveHander));//频繁滚动，只要间隔时间不大于250ms, 在一系列移动后，只会执行一次
         *
         * //解绑事件
         * $(document).unbind(\'touchmove\', touchmoveHander);//注意这里面unbind还是touchmoveHander,而不是$.debounce返回的function, 当然unbind那个也是一样的效果
         */
        debounce: function(delay, fn, t) {
            return fn === undefined ? $.throttle(250, delay, false) : $.throttle(delay, fn, t === undefined ? false : t !== false);
        }
    });

    /**
     * 扩展类型判断
     * @param {Any} obj
     * @see isString, isBoolean, isRegExp, isNumber, isDate, isObject, isNull, isUdefined
     */
    /**
     * @name $.isString
     * @grammar $.isString(val)  ⇒ Boolean
     * @desc 判断变量类型是否为***String***
     * @example console.log($.isString({}));// => false
     * console.log($.isString(123));// => false
     * console.log($.isString(\'123\'));// => true
     */
    /**
     * @name $.isBoolean
     * @grammar $.isBoolean(val)  ⇒ Boolean
     * @desc 判断变量类型是否为***Boolean***
     * @example console.log($.isBoolean(1));// => false
     * console.log($.isBoolean(\'true\'));// => false
     * console.log($.isBoolean(false));// => true
     */
    /**
     * @name $.isRegExp
     * @grammar $.isRegExp(val)  ⇒ Boolean
     * @desc 判断变量类型是否为***RegExp***
     * @example console.log($.isRegExp(1));// => false
     * console.log($.isRegExp(\'test\'));// => false
     * console.log($.isRegExp(/test/));// => true
     */
    /**
     * @name $.isNumber
     * @grammar $.isNumber(val)  ⇒ Boolean
     * @desc 判断变量类型是否为***Number***
     * @example console.log($.isNumber(\'123\'));// => false
     * console.log($.isNumber(true));// => false
     * console.log($.isNumber(123));// => true
     */
    /**
     * @name $.isDate
     * @grammar $.isDate(val)  ⇒ Boolean
     * @desc 判断变量类型是否为***Date***
     * @example console.log($.isDate(\'123\'));// => false
     * console.log($.isDate(\'2012-12-12\'));// => false
     * console.log($.isDate(new Date()));// => true
     */
    /**
     * @name $.isObject
     * @grammar $.isObject(val)  ⇒ Boolean
     * @desc 判断变量类型是否为***Object***
     * @example console.log($.isObject(\'123\'));// => false
     * console.log($.isObject(true));// => false
     * console.log($.isObject({}));// => true
     */
    /**
     * @name $.isNull
     * @grammar $.isNull(val)  ⇒ Boolean
     * @desc 判断变量类型是否为***null***
     * @example console.log($.isNull(false));// => false
     * console.log($.isNull(0));// => false
     * console.log($.isNull(null));// => true
     */
    /**
     * @name $.isUndefined
     * @grammar $.isUndefined(val)  ⇒ Boolean
     * @desc 判断变量类型是否为***undefined***
     * @example
     * console.log($.isUndefined(false));// => false
     * console.log($.isUndefined(0));// => false
     * console.log($.isUndefined(a));// => true
     */
    $.each("String Boolean RegExp Number Date Object Null Undefined".split(" "), function(i, name) {
        var fnbody = \'\';
        switch (name) {
            case \'Null\':
                fnbody = \'obj === null\';
                break;
            case \'Undefined\':
                fnbody = \'obj === undefined\';
                break;
            default:
                //fnbody = "new RegExp(\'" + name + "]\', \'i\').test($.toString(obj))";
                fnbody = "new RegExp(\'" + name + "]\', \'i\').test(Object.prototype.toString.call(obj))";//解决zepto与jQuery共存时报错的问题，$被jQuery占用了。
        }
        $[\'is\' + name] = new Function(\'obj\', "return " + fnbody);
    });

})(Zepto);

//Support.js
(function($, undefined) {
    var ua = navigator.userAgent,
        na = navigator.appVersion,
        br = $.browser;

    /**
     * @name $.browser
     * @desc 扩展zepto中对browser的检测
     *
     * **可用属性**
     * - ***qq*** 检测qq浏览器
     * - ***chrome*** 检测chrome浏览器
     * - ***uc*** 检测uc浏览器
     * - ***version*** 检测浏览器版本
     *
     * @example
     * if ($.browser.qq) {      //在qq浏览器上打出此log
     *     console.log(\'this is qq browser\');
     * }
     */
    $.extend($.browser, {
        qq: /qq/i.test(ua),
        chrome: /chrome/i.test(ua) || /CriOS/i.test(ua),
        uc: /UC/i.test(ua) || /UC/i.test(na)
    });

    $.browser.uc = $.browser.uc || !$.browser.qq && !$.browser.chrome && !/safari/i.test(ua);

    try {
        $.browser.version = br.uc ? na.match(/UC(?:Browser)?\\/([\\d.]+)/)[1] : br.qq ? ua.match(/MQQBrowser\\/([\\d.]+)/)[1] : br.chrome ? ua.match(/(?:CriOS|Chrome)\\/([\\d.]+)/)[1] : br.version;
    } catch (e) {}


    /**
     * @name $.support
     * @desc 检测设备对某些属性或方法的支持情况
     *
     * **可用属性**
     * - ***orientation*** 检测是否支持转屏事件，UC中存在orientaion，但转屏不会触发该事件，故UC属于不支持转屏事件(iOS 4上qq, chrome都有这个现象)
     * - ***touch*** 检测是否支持touch相关事件
     * - ***cssTransitions*** 检测是否支持css3的transition
     * - ***has3d*** 检测是否支持translate3d的硬件加速
     *
     * @example
     * if ($.support.has3d) {      //在支持3d的设备上使用
     *     console.log(\'you can use transtion3d\');
     * }
     */
    $.support = $.extend($.support || {}, {
        orientation: !($.browser.uc || (parseFloat($.os.version)<5 && ($.browser.qq || $.browser.chrome))) && "orientation" in window && "onorientationchange" in window,
        touch: "ontouchend" in document,
        cssTransitions: "WebKitTransitionEvent" in window,
        has3d: \'WebKitCSSMatrix\' in window && \'m11\' in new WebKitCSSMatrix()

    });

})(Zepto);

//Event.js
(function($) {
    /** detect orientation change */
    $(document).ready(function () {
        var getOrt = "matchMedia" in window ? function(){
                return window.matchMedia("(orientation: portrait)").matches?\'portrait\':\'landscape\';
            }:function(){
                var elem = document.documentElement;
                return elem.clientWidth / Math.max(elem.clientHeight, 320) < 1.1 ? "portrait" : "landscape";
            },
            lastOrt = getOrt(),
            handler = function(e) {
                if(e.type == \'orientationchange\'){
                    return $(window).trigger(\'ortchange\');
                }
                maxTry = 20;
                clearInterval(timer);
                timer = $.later(function() {
                    var curOrt = getOrt();
                    if (lastOrt !== curOrt) {
                        lastOrt = curOrt;
                        clearInterval(timer);
                        $(window).trigger(\'ortchange\');
                    } else if(--maxTry){//最多尝试20次
                        clearInterval(timer);
                    }
                }, 50, true);
            },
            timer, maxTry;
        $(window).bind($.support.orientation ? \'orientationchange\' : \'resize\', $.debounce(handler));
    });

    /**
     * @name Trigger Events
     * @theme event
     * @desc 扩展的事件
     * - ***scrollStop*** : scroll停下来时触发, 考虑前进或者后退后scroll事件不触发情况。
     * - ***ortchange*** : 当转屏的时候触发，兼容uc和其他不支持orientationchange的设备
     * @example $(document).on(\'scrollStop\', function () {        //scroll停下来时显示scrollStop
     *     console.log(\'scrollStop\');
     * });
     *
     * $(document).on(\'ortchange\', function () {        //当转屏的时候触发
     *     console.log(\'ortchange\');
     * });
     */
    /** dispatch scrollStop */
    function _registerScrollStop(){
        $(window).on(\'scroll\', $.debounce(80, function() {
            $(document).trigger(\'scrollStop\');
        }, false));
    }
    //在离开页面，前进或后退回到页面后，重新绑定scroll, 需要off掉所有的scroll，否则scroll时间不触发
    function _touchstartHander() {
        $(window).off(\'scroll\');
        _registerScrollStop();
    }
    _registerScrollStop();
    $(window).on(\'pageshow\', function(e){
        if(e.persisted) {//如果是从bfcache中加载页面
            $(document).off(\'touchstart\', _touchstartHander).one(\'touchstart\', _touchstartHander);
        }
    });
})(Zepto);
',
    'cb00ed49' => ';/**
 * @file 所有UI组件的基类，通过它可以简单的快速的创建新的组件。
 * @name UI 基类
 * @short Zepto UI
 * @desc 所有UI组件的基类，通过它可以简单的快速的创建新的组件。
 * @import core/zepto.js, core/zepto.extend.js
 */
(function($, undefined) {
    $.ui = $.ui || {
        version: \'2.0.0\',

        guid: _guid,

        /**
         * @name $.ui.define
         * @grammar $.ui.define(name, data[, superClass]) ⇒ undefined
         * @desc 定义组件,
         * - \'\'name\'\' 组件名称
         * - \'\'data\'\' 对象，设置此组件的prototype。可以添加属性或方法
         * - \'\'superClass\'\' 基类，指定此组件基于哪个现有组件，默认为Widget基类
         * **示例:**
         * <code type="javascript">
         * $.ui.define(\'helloworld\', {
         *     _data: {
         *         opt1: null
         *     },
         *     enable: function(){
         *         //...
         *     }
         * });
         * </code>
         *
         * **定义完后，就可以通过以下方式使用了**
         *<code type="javascript">
         * var instance = $.ui.helloworld({opt1: true});
         * instance.enable();
         *
         * //或者
         * $(\'#id\').helloworld({opt1:true});
         * //...later
         * $(\'#id\').helloworld(\'enable\');
         * </code>
         *
         * **Tips**
         * 1. 通过Zepto对象上的组件方法，可以直接实例话组件, 如: $(\'#btn\').button({label: \'abc\'});
         * 2. 通过Zepto对象上的组件方法，传入字符串this, 可以获得组件实例，如：var btn = $(\'#btn\').button(\'this\');
         * 3. 通过Zepto对象上的组件方法，可以直接调用组件方法，第一个参数用来指定方法名，之后的参数作为方法参数，如: $(\'#btn\').button(\'setIcon\', \'home\');
         * 4. 在子类中，如覆写了某个方法，可以在方法中通过this.$super()方法调用父级方法。如：this.$super(\'enable\');
         */
        define: function(name, data, superClass) {
            if(superClass) data.inherit = superClass;
            var Class = $.ui[name] = _createClass(function(el, options) {
                var obj = _createObject(Class.prototype, {
                    _id: $.parseTpl(tpl, {
                        name: name,
                        id: _guid()
                    })
                });
                obj._createWidget.call(obj, el, options,Class.plugins);
                return obj;
            }, data);
            return _zeptoLize(name, Class);
        },

        /**
         * @name $.ui.isWidget()
         * @grammar $.ui.isWidget(obj) ⇒ boolean
         * @grammar $.ui.isWidget(obj, name) ⇒ boolean
         * @desc 判断obj是不是widget实例
         *
         * **参数**
         * - \'\'obj\'\' 用于检测的对象
         * - \'\'name\'\' 可选，默认监测是不是\'\'widget\'\'(基类)的实例，可以传入组件名字如\'\'button\'\'。作用将变为obj是不是button组件实例。
         * @param obj
         * @param name
         * @example
         *
         * var btn = $.ui.button(),
         *     dialog = $.ui.dialog();
         *
         * console.log($.isWidget(btn)); // => true
         * console.log($.isWidget(dialog)); // => true
         * console.log($.isWidget(btn, \'button\')); // => true
         * console.log($.isWidget(dialog, \'button\')); // => false
         * console.log($.isWidget(btn, \'noexist\')); // => false
         */
        isWidget: function(obj, name){
            return obj instanceof (name===undefined ? _widget: $.ui[name] || _blankFn);
        }
    };

    var id = 1,
        _blankFn = function(){},
        tpl = \'<%=name%>-<%=id%>\',
        uikey = \'gmu-widget\';
        
    /**
     * generate guid
     */
    function _guid() {
        return id++;
    };

    function _createObject(proto, data) {
        var obj = {};
        Object.create ? obj = Object.create(proto) : obj.__proto__ = proto;
        return $.extend(obj, data || {});
    }

    function _createClass(Class, data) {
        if (data) {
            _process(Class, data);
            $.extend(Class.prototype, data);
        }
        return $.extend(Class, {
            plugins: [],
            register: function(fn) {
                if ($.isObject(fn)) {
                    $.extend(this.prototype,fn);
                    return;
                }
                this.plugins.push(fn);
            }
        });
    }

    /**
     * handle inherit & _data
     */
    function _process(Class, data) {
        var superClass = data.inherit || _widget,
            proto = superClass.prototype,
            obj;
        obj = Class.prototype = _createObject(proto, {
            $factory: Class,
            $super: function(key) {
                var fn = proto[key];
                return $.isFunction(fn) ? fn.apply(this, $.slice(arguments, 1)) : fn;
            }
        });
        obj._data = $.extend({}, proto._data, data._data);
        delete data._data;
        return Class;
    }

    /**
     * 强制setup模式
     * @grammar $(selector).dialog(opts);
     */
    function _zeptoLize(name) {
        $.fn[name] = function(opts) {

            var ret, obj,args = $.slice(arguments, 1);
            $.each(this,function(i,el){
                obj = $(el).data(uikey + name) ||  $.ui[name](el, $.extend($.isPlainObject(opts) ?  opts : {},{
                    setup: true
                }));
                if ($.isString(opts)) {
                    ret = $.isFunction(obj[opts]) && obj[opts].apply(obj, args);
                    if (opts == \'this\' || ret !== obj && ret !== undefined) {
                        return false;
                    }
                    ret = null;
                }
            });
            //ret 为真就是要返回ui实例之外的内容
            //obj \'this\'时返回
            //其他都是返回zepto实例
            return ret || (opts == \'this\' ? obj : this);
        };
    }
    /**
     * @name widget基类
     * @desc GMU所有的组件都是此类的子类，即以下此类里面的方法都可在其他组建中调用。
     */
    var _widget = function() {};
    $.extend(_widget.prototype, {
        _data: {
            status: true
        },

        /**
         * @name data
         * @grammar data(key) ⇒ value
         * @grammar data(key, value) ⇒ value
         * @desc 设置或者获取options, 所有组件中的配置项都可以通过此方法得到。
         * @example
         * $(\'a#btn\').button({label: \'按钮\'});
         * console.log($(\'a#btn\').button(\'data\', \'label\'));// => 按钮
         */
        data: function(key, val) {
            var _data = this._data;
            if ($.isObject(key)) return $.extend(_data, key);
            else return !$.isUndefined(val) ? _data[key] = val : _data[key];
        },

        /**
         * common constructor
         */
        _createWidget: function(el, opts,plugins) {

            if ($.isObject(el)) {
                opts = el || {};
                el = undefined;
            }

            var data = $.extend({}, this._data, opts);
            $.extend(this, {
                _el: el ? $(el) : undefined,
                _data: data
            });

            //触发plugins
            var me = this;
            $.each(plugins,function(i,fn){
                var result = fn.apply(me);
                if(result && $.isPlainObject(result)){
                    var plugins = me._data.disablePlugin;
                    if(!plugins || $.isString(plugins) && plugins.indexOf(result.pluginName) == -1){
                        delete result.pluginName
                        $.each(result,function(key,val){
                            var orgFn;
                            if((orgFn = me[key]) && $.isFunction(val)){
                                me[key] = function(){
                                    me[key + \'Org\'] = orgFn;
                                    return val.apply(me,arguments);
                                }
                            }else
                                me[key] = val;
                        })
                    }
                }
            });
            // use setup or render
            if(data.setup) this._setup(el && el.getAttribute(\'data-mode\'));
            else this._create();
            this._init();

            var me = this,
                $el = this.trigger(\'init\').root();
            $el.on(\'tap\', function(e) {
                (e[\'bubblesList\'] || (e[\'bubblesList\'] = [])).push(me);
            });
            // record this
            $el.data(uikey + this._id.split(\'-\')[0],this);
        },

        /**
         * @interface: use in render mod
         * @name _create
         * @desc 接口定义，子类中需要重新实现此方法，此方法在render模式时被调用。
         *
         * 所谓的render方式，即，通过以下方式初始化组件
         * <code>
         * $.ui.widgetName(options);
         * </code>
         */
        _create: function() {},

        /**
         * @interface: use in setup mod
         * @param {Boolean} data-mode use tpl mode
         * @name _setup
         * @desc 接口定义，子类中需要重新实现此方法，此方法在setup模式时被调用。第一个行参用来分辨时fullsetup，还是setup
         *
         * <code>
         * $.ui.define(\'helloworld\', {
         *     _setup: function(mode){
         *          if(mode){
         *              //为fullsetup模式
         *          } else {
         *              //为setup模式
         *          }
         *     }
         * });
         * </code>
         *
         * 所谓的setup方式，即，先有dom，然后通过选择器，初始化Zepto后，在Zepto对象直接调用组件名方法实例化组件，如
         * <code>
         * //<div id="widget"></div>
         * $(\'#widget\').widgetName(options);
         * </code>
         *
         * 如果用来初始化的element，设置了data-mode="true"，组件将以fullsetup模式初始化
         */
        _setup: function(mode) {},

        /**
         * @name root
         * @grammar root() ⇒ value
         * @grammar root(el) ⇒ value
         * @desc 设置或者获取根节点
         * @example
         * $(\'a#btn\').button({label: \'按钮\'});
         * console.log($(\'a#btn\').button(\'root\'));// => a#btn
         */
        root: function(el) {
            return this._el = el || this._el;
        },

        /**
         * @name id
         * @grammar id() ⇒ value
         * @grammar id(id) ⇒ value
         * @desc 设置或者获取组件id
         */
        id: function(id) {
            return this._id = id || this._id;
        },

        /**
         * @name destroy
         * @grammar destroy() ⇒ undefined
         * @desc 注销组件
         */
        destroy: function() {
            var That = this,
                $el;
            $.each(this.data(\'components\') || [], function(id, obj) {
                obj.destroy();
            });
            $el = this.trigger(\'destroy\').off().root();
            $el.find(\'*\').off();
            $el.removeData(uikey).off().remove();
            this.__proto__ = null;
            $.each(this, function(key, val) {
                delete That[key];
            });
        },

        /**
         * @name component
         * @grammar component() ⇒ array
         * @grammar component(subInstance) ⇒ instance
         * @grammar component(createFn) ⇒ instance
         * @desc 获取或者设置子组件, createFn为组件构造器，必须返回组件的实例。
         */
        component: function(createFn) {
            var list = this.data(\'components\') || this.data(\'components\', []);
            try {
                list.push($.isFunction(createFn) ? createFn.apply(this) : createFn);
            } catch(e) {}
            return this;
        },

        /**
         * @name on
         * @grammar on(type, handler) ⇒ instance
         * @desc 绑定事件，此事件绑定不同于zepto上绑定事件，此On的this只想组件实例，而非zepto实例
         */
        on: function(ev, callback) {
            this.root().on(ev, $.proxy(callback, this));
            return this;
        },

        /**
         * @name off
         * @grammar off(type) ⇒ instance
         * @grammar off(type, handler) ⇒ instance
         * @desc 解绑事件
         */
        off: function(ev, callback) {
            this.root().off(ev, callback);
            return this;
        },

        /**
         * @name trigger
         * @grammar trigger(type[, data]) ⇒ instance
         * @desc 触发事件, 此trigger会优先把options上的事件回调函数先执行，然后给根DOM派送事件。
         * options上回调函数可以通过e.preventDefaualt()来组织事件派发。
         */
        trigger: function(event, data) {
            event = $.isString(event) ? $.Event(event) : event;
            var onEvent = this.data(event.type),result;
            if( onEvent && $.isFunction(onEvent) ){
                event.data = data;
                result = onEvent.apply(this, [event].concat(data));
                if(result === false || event.defaultPrevented){
                    return this;
                }
            }
            this.root().trigger(event, data);
            return this;
        }
    });
})(Zepto);',
    91888440 => ';/*!
 * iScroll v4.2.2 ~ Copyright (c) 2012 Matteo Spinelli, http://cubiq.org
 * Released under MIT license, http://cubiq.org/license
 */
(function(window, doc){
    var m = Math,_bindArr = [],
        dummyStyle = doc.createElement(\'div\').style,
        vendor = (function () {
            var vendors = \'t,webkitT,MozT,msT,OT\'.split(\',\'),
                t,
                i = 0,
                l = vendors.length;

            for ( ; i < l; i++ ) {
                t = vendors[i] + \'ransform\';
                if ( t in dummyStyle ) {
                    return vendors[i].substr(0, vendors[i].length - 1);
                }
            }

            return false;
        })(),
        cssVendor = vendor ? \'-\' + vendor.toLowerCase() + \'-\' : \'\',


    // Style properties
        transform = prefixStyle(\'transform\'),
        transitionProperty = prefixStyle(\'transitionProperty\'),
        transitionDuration = prefixStyle(\'transitionDuration\'),
        transformOrigin = prefixStyle(\'transformOrigin\'),
        transitionTimingFunction = prefixStyle(\'transitionTimingFunction\'),
        transitionDelay = prefixStyle(\'transitionDelay\'),

    // Browser capabilities
        isAndroid = (/android/gi).test(navigator.appVersion),
        isTouchPad = (/hp-tablet/gi).test(navigator.appVersion),

        has3d = prefixStyle(\'perspective\') in dummyStyle,
        hasTouch = \'ontouchstart\' in window && !isTouchPad,
        hasTransform = !!vendor,
        hasTransitionEnd = prefixStyle(\'transition\') in dummyStyle,

        RESIZE_EV = \'onorientationchange\' in window ? \'orientationchange\' : \'resize\',
        START_EV = hasTouch ? \'touchstart\' : \'mousedown\',
        MOVE_EV = hasTouch ? \'touchmove\' : \'mousemove\',
        END_EV = hasTouch ? \'touchend\' : \'mouseup\',
        CANCEL_EV = hasTouch ? \'touchcancel\' : \'mouseup\',
        TRNEND_EV = (function () {
            if ( vendor === false ) return false;

            var transitionEnd = {
                \'\'			: \'transitionend\',
                \'webkit\'	: \'webkitTransitionEnd\',
                \'Moz\'		: \'transitionend\',
                \'O\'			: \'otransitionend\',
                \'ms\'		: \'MSTransitionEnd\'
            };

            return transitionEnd[vendor];
        })(),

        nextFrame = (function() {
            return window.requestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.oRequestAnimationFrame ||
                window.msRequestAnimationFrame ||
                function(callback) { return setTimeout(callback, 1); };
        })(),
        cancelFrame = (function () {
            return window.cancelRequestAnimationFrame ||
                window.webkitCancelAnimationFrame ||
                window.webkitCancelRequestAnimationFrame ||
                window.mozCancelRequestAnimationFrame ||
                window.oCancelRequestAnimationFrame ||
                window.msCancelRequestAnimationFrame ||
                clearTimeout;
        })(),

    // Helpers
        translateZ = has3d ? \' translateZ(0)\' : \'\',

    // Constructor
        iScroll = function (el, options) {
            var that = this,
                i;

            that.wrapper = typeof el == \'object\' ? el : doc.getElementById(el);
            that.wrapper.style.overflow = \'hidden\';
            that.scroller = that.wrapper.children[0];

            that.translateZ = translateZ;
            // Default options
            that.options = {
                hScroll: true,
                vScroll: true,
                x: 0,
                y: 0,
                bounce: true,
                bounceLock: false,
                momentum: true,
                lockDirection: true,
                useTransform: true,
                useTransition: false,
                topOffset: 0,
                checkDOMChanges: false,		// Experimental
                handleClick: true,


                // Events
                onRefresh: null,
                onBeforeScrollStart: function (e) { e.preventDefault(); },
                onScrollStart: null,
                onBeforeScrollMove: null,
                onScrollMove: null,
                onBeforeScrollEnd: null,
                onScrollEnd: null,
                onTouchEnd: null,
                onDestroy: null

            };

            // User defined options
            for (i in options) that.options[i] = options[i];

            // Set starting position
            that.x = that.options.x;
            that.y = that.options.y;

            // Normalize options
            that.options.useTransform = hasTransform && that.options.useTransform;

            that.options.useTransition = hasTransitionEnd && that.options.useTransition;



            // Set some default styles
            that.scroller.style[transitionProperty] = that.options.useTransform ? cssVendor + \'transform\' : \'top left\';
            that.scroller.style[transitionDuration] = \'0\';
            that.scroller.style[transformOrigin] = \'0 0\';
            if (that.options.useTransition) that.scroller.style[transitionTimingFunction] = \'cubic-bezier(0.33,0.66,0.66,1)\';

            if (that.options.useTransform) that.scroller.style[transform] = \'translate(\' + that.x + \'px,\' + that.y + \'px)\' + translateZ;
            else that.scroller.style.cssText += \';position:absolute;top:\' + that.y + \'px;left:\' + that.x + \'px\';



            that.refresh();

            that._bind(RESIZE_EV, window);
            that._bind(START_EV);


            if (that.options.checkDOMChanges) that.checkDOMTime = setInterval(function () {
                that._checkDOMChanges();
            }, 500);
        };

// Prototype
    iScroll.prototype = {
        enabled: true,
        x: 0,
        y: 0,
        steps: [],
        scale: 1,
        currPageX: 0, currPageY: 0,
        pagesX: [], pagesY: [],
        aniTime: null,
        isStopScrollAction:false,

        handleEvent: function (e) {
            var that = this;
            switch(e.type) {
                case START_EV:
                    if (!hasTouch && e.button !== 0) return;
                    that._start(e);
                    break;
                case MOVE_EV: that._move(e); break;
                case END_EV:
                case CANCEL_EV: that._end(e); break;
                case RESIZE_EV: that._resize(); break;
                case TRNEND_EV: that._transitionEnd(e); break;
            }
        },

        _checkDOMChanges: function () {
            if (this.moved ||  this.animating ||
                (this.scrollerW == this.scroller.offsetWidth * this.scale && this.scrollerH == this.scroller.offsetHeight * this.scale)) return;

            this.refresh();
        },

        _resize: function () {
            var that = this;
            setTimeout(function () { that.refresh(); }, isAndroid ? 200 : 0);
        },

        _pos: function (x, y) {
            x = this.hScroll ? x : 0;
            y = this.vScroll ? y : 0;

            if (this.options.useTransform) {
                this.scroller.style[transform] = \'translate(\' + x + \'px,\' + y + \'px) scale(\' + this.scale + \')\' + translateZ;
            } else {
                x = m.round(x);
                y = m.round(y);
                this.scroller.style.left = x + \'px\';
                this.scroller.style.top = y + \'px\';
            }

            this.x = x;
            this.y = y;

        },



        _start: function (e) {
            var that = this,
                point = hasTouch ? e.touches[0] : e,
                matrix, x, y,
                c1, c2;

            if (!that.enabled) return;

            if (that.options.onBeforeScrollStart) that.options.onBeforeScrollStart.call(that, e);

            if (that.options.useTransition ) that._transitionTime(0);

            that.moved = false;
            that.animating = false;

            that.distX = 0;
            that.distY = 0;
            that.absDistX = 0;
            that.absDistY = 0;
            that.dirX = 0;
            that.dirY = 0;
            that.isStopScrollAction = false;

            if (that.options.momentum) {
                if (that.options.useTransform) {
                    // Very lame general purpose alternative to CSSMatrix
                    matrix = getComputedStyle(that.scroller, null)[transform].replace(/[^0-9\\-.,]/g, \'\').split(\',\');
                    x = +matrix[4];
                    y = +matrix[5];
                } else {
                    x = +getComputedStyle(that.scroller, null).left.replace(/[^0-9-]/g, \'\');
                    y = +getComputedStyle(that.scroller, null).top.replace(/[^0-9-]/g, \'\');
                }

                if (x != that.x || y != that.y) {
                    that.isStopScrollAction = true;
                    if (that.options.useTransition) that._unbind(TRNEND_EV);
                    else cancelFrame(that.aniTime);
                    that.steps = [];
                    that._pos(x, y);
                    if (that.options.onScrollEnd) that.options.onScrollEnd.call(that);
                }
            }



            that.startX = that.x;
            that.startY = that.y;
            that.pointX = point.pageX;
            that.pointY = point.pageY;

            that.startTime = e.timeStamp || Date.now();

            if (that.options.onScrollStart) that.options.onScrollStart.call(that, e);

            that._bind(MOVE_EV, window);
            that._bind(END_EV, window);
            that._bind(CANCEL_EV, window);
        },

        _move: function (e) {
            var that = this,
                point = hasTouch ? e.touches[0] : e,
                deltaX = point.pageX - that.pointX,
                deltaY = point.pageY - that.pointY,
                newX = that.x + deltaX,
                newY = that.y + deltaY,

                timestamp = e.timeStamp || Date.now();

            if (that.options.onBeforeScrollMove) that.options.onBeforeScrollMove.call(that, e);

            that.pointX = point.pageX;
            that.pointY = point.pageY;

            // Slow down if outside of the boundaries
            if (newX > 0 || newX < that.maxScrollX) {
                newX = that.options.bounce ? that.x + (deltaX / 2) : newX >= 0 || that.maxScrollX >= 0 ? 0 : that.maxScrollX;
            }
            if (newY > that.minScrollY || newY < that.maxScrollY) {
                newY = that.options.bounce ? that.y + (deltaY / 2) : newY >= that.minScrollY || that.maxScrollY >= 0 ? that.minScrollY : that.maxScrollY;
            }

            that.distX += deltaX;
            that.distY += deltaY;
            that.absDistX = m.abs(that.distX);
            that.absDistY = m.abs(that.distY);

            if (that.absDistX < 6 && that.absDistY < 6) {
                return;
            }

            // Lock direction
            if (that.options.lockDirection) {
                if (that.absDistX > that.absDistY + 5) {
                    newY = that.y;
                    deltaY = 0;
                } else if (that.absDistY > that.absDistX + 5) {
                    newX = that.x;
                    deltaX = 0;
                }
            }

            that.moved = true;

            // internal for header scroll

            that._beforePos ? that._beforePos(newY, deltaY) && that._pos(newX, newY) : that._pos(newX, newY);

            that.dirX = deltaX > 0 ? -1 : deltaX < 0 ? 1 : 0;
            that.dirY = deltaY > 0 ? -1 : deltaY < 0 ? 1 : 0;

            if (timestamp - that.startTime > 300) {
                that.startTime = timestamp;
                that.startX = that.x;
                that.startY = that.y;
            }

            if (that.options.onScrollMove) that.options.onScrollMove.call(that, e);
        },

        _end: function (e) {
            if (hasTouch && e.touches.length !== 0) return;

            var that = this,
                point = hasTouch ? e.changedTouches[0] : e,
                target, ev,
                momentumX = { dist:0, time:0 },
                momentumY = { dist:0, time:0 },
                duration = (e.timeStamp || Date.now()) - that.startTime,
                newPosX = that.x,
                newPosY = that.y,
                newDuration;


            that._unbind(MOVE_EV, window);
            that._unbind(END_EV, window);
            that._unbind(CANCEL_EV, window);

            if (that.options.onBeforeScrollEnd) that.options.onBeforeScrollEnd.call(that, e);


            if (!that.moved) {

                if (hasTouch && this.options.handleClick && !that.isStopScrollAction) {
                    that.doubleTapTimer = setTimeout(function () {
                        that.doubleTapTimer = null;

                        // Find the last touched element
                        target = point.target;
                        while (target.nodeType != 1) target = target.parentNode;

                        if (target.tagName != \'SELECT\' && target.tagName != \'INPUT\' && target.tagName != \'TEXTAREA\') {
                            ev = doc.createEvent(\'MouseEvents\');
                            ev.initMouseEvent(\'click\', true, true, e.view, 1,
                                point.screenX, point.screenY, point.clientX, point.clientY,
                                e.ctrlKey, e.altKey, e.shiftKey, e.metaKey,
                                0, null);
                            ev._fake = true;
                            target.dispatchEvent(ev);
                        }
                    },  0);
                }


                that._resetPos(400);

                if (that.options.onTouchEnd) that.options.onTouchEnd.call(that, e);
                return;
            }

            if (duration < 300 && that.options.momentum) {
                momentumX = newPosX ? that._momentum(newPosX - that.startX, duration, -that.x, that.scrollerW - that.wrapperW + that.x, that.options.bounce ? that.wrapperW : 0) : momentumX;
                momentumY = newPosY ? that._momentum(newPosY - that.startY, duration, -that.y, (that.maxScrollY < 0 ? that.scrollerH - that.wrapperH + that.y - that.minScrollY : 0), that.options.bounce ? that.wrapperH : 0) : momentumY;

                newPosX = that.x + momentumX.dist;
                newPosY = that.y + momentumY.dist;

                if ((that.x > 0 && newPosX > 0) || (that.x < that.maxScrollX && newPosX < that.maxScrollX)) momentumX = { dist:0, time:0 };
                if ((that.y > that.minScrollY && newPosY > that.minScrollY) || (that.y < that.maxScrollY && newPosY < that.maxScrollY)) momentumY = { dist:0, time:0 };
            }

            if (momentumX.dist || momentumY.dist) {
                newDuration = m.max(m.max(momentumX.time, momentumY.time), 10);



                that.scrollTo(m.round(newPosX), m.round(newPosY), newDuration);

                if (that.options.onTouchEnd) that.options.onTouchEnd.call(that, e);
                return;
            }



            that._resetPos(200);
            if (that.options.onTouchEnd) that.options.onTouchEnd.call(that, e);
        },

        _resetPos: function (time) {
            var that = this,
                resetX = that.x >= 0 ? 0 : that.x < that.maxScrollX ? that.maxScrollX : that.x,
                resetY = that.y >= that.minScrollY || that.maxScrollY > 0 ? that.minScrollY : that.y < that.maxScrollY ? that.maxScrollY : that.y;

            if (resetX == that.x && resetY == that.y) {
                if (that.moved) {
                    that.moved = false;
                    if (that.options.onScrollEnd) that.options.onScrollEnd.call(that);		// Execute custom code on scroll end
                    if (that._afterPos) that._afterPos();
                }

                return;
            }

            that.scrollTo(resetX, resetY, time || 0);
        },



        _transitionEnd: function (e) {
            var that = this;

            if (e.target != that.scroller) return;

            that._unbind(TRNEND_EV);

            that._startAni();
        },


        /**
         *
         * Utilities
         *
         */
        _startAni: function () {
            var that = this,
                startX = that.x, startY = that.y,
                startTime = Date.now(),
                step, easeOut,
                animate;

            if (that.animating) return;

            if (!that.steps.length) {
                that._resetPos(400);
                return;
            }

            step = that.steps.shift();

            if (step.x == startX && step.y == startY) step.time = 0;

            that.animating = true;
            that.moved = true;

            if (that.options.useTransition) {
                that._transitionTime(step.time);
                that._pos(step.x, step.y);
                that.animating = false;
                if (step.time) that._bind(TRNEND_EV);
                else that._resetPos(0);
                return;
            }

            animate = function () {
                var now = Date.now(),
                    newX, newY;

                if (now >= startTime + step.time) {
                    that._pos(step.x, step.y);
                    that.animating = false;
                    if (that.options.onAnimationEnd) that.options.onAnimationEnd.call(that);			// Execute custom code on animation end
                    that._startAni();
                    return;
                }

                now = (now - startTime) / step.time - 1;
                easeOut = m.sqrt(1 - now * now);
                newX = (step.x - startX) * easeOut + startX;
                newY = (step.y - startY) * easeOut + startY;
                that._pos(newX, newY);
                if (that.animating) that.aniTime = nextFrame(animate);
            };

            animate();
        },

        _transitionTime: function (time) {
            time += \'ms\';
            this.scroller.style[transitionDuration] = time;

        },

        _momentum: function (dist, time, maxDistUpper, maxDistLower, size) {
            var deceleration = 0.0006,
                speed = m.abs(dist) * (this.options.speedScale||1) / time,
                newDist = (speed * speed) / (2 * deceleration),
                newTime = 0, outsideDist = 0;

            // Proportinally reduce speed if we are outside of the boundaries
            if (dist > 0 && newDist > maxDistUpper) {
                outsideDist = size / (6 / (newDist / speed * deceleration));
                maxDistUpper = maxDistUpper + outsideDist;
                speed = speed * maxDistUpper / newDist;
                newDist = maxDistUpper;
            } else if (dist < 0 && newDist > maxDistLower) {
                outsideDist = size / (6 / (newDist / speed * deceleration));
                maxDistLower = maxDistLower + outsideDist;
                speed = speed * maxDistLower / newDist;
                newDist = maxDistLower;
            }

            newDist = newDist * (dist < 0 ? -1 : 1);
            newTime = speed / deceleration;

            return { dist: newDist, time: m.round(newTime) };
        },

        _offset: function (el) {
            var left = -el.offsetLeft,
                top = -el.offsetTop;

            while (el = el.offsetParent) {
                left -= el.offsetLeft;
                top -= el.offsetTop;
            }

            if (el != this.wrapper) {
                left *= this.scale;
                top *= this.scale;
            }

            return { left: left, top: top };
        },



        _bind: function (type, el, bubble) {
            _bindArr.concat([el || this.scroller, type, this]);
            (el || this.scroller).addEventListener(type, this, !!bubble);
        },

        _unbind: function (type, el, bubble) {
            (el || this.scroller).removeEventListener(type, this, !!bubble);
        },


        /**
         *
         * Public methods
         *
         */
        destroy: function () {
            var that = this;

            that.scroller.style[transform] = \'\';



            // Remove the event listeners
            that._unbind(RESIZE_EV, window);
            that._unbind(START_EV);
            that._unbind(MOVE_EV, window);
            that._unbind(END_EV, window);
            that._unbind(CANCEL_EV, window);



            if (that.options.useTransition) that._unbind(TRNEND_EV);

            if (that.options.checkDOMChanges) clearInterval(that.checkDOMTime);

            if (that.options.onDestroy) that.options.onDestroy.call(that);

            //清除所有绑定的事件
            for (var i = 0, l = _bindArr.length; i < l;) {
                _bindArr[i].removeEventListener(_bindArr[i + 1], _bindArr[i + 2]);
                _bindArr[i] = null;
                i = i + 3
            }
            _bindArr = [];

            //干掉外边的容器内容
            var div = doc.createElement(\'div\');
            div.appendChild(this.wrapper);
            div.innerHTML = \'\';
            that.wrapper = that.scroller = div = null;
        },

        refresh: function () {
            var that = this,
                offset;



            that.wrapperW = that.wrapper.clientWidth || 1;
            that.wrapperH = that.wrapper.clientHeight || 1;

            that.minScrollY = -that.options.topOffset || 0;
            that.scrollerW = m.round(that.scroller.offsetWidth * that.scale);
            that.scrollerH = m.round((that.scroller.offsetHeight + that.minScrollY) * that.scale);
            that.maxScrollX = that.wrapperW - that.scrollerW;
            that.maxScrollY = that.wrapperH - that.scrollerH + that.minScrollY;
            that.dirX = 0;
            that.dirY = 0;

            if (that.options.onRefresh) that.options.onRefresh.call(that);

            that.hScroll = that.options.hScroll && that.maxScrollX < 0;
            that.vScroll = that.options.vScroll && (!that.options.bounceLock && !that.hScroll || that.scrollerH > that.wrapperH);


            offset = that._offset(that.wrapper);
            that.wrapperOffsetLeft = -offset.left;
            that.wrapperOffsetTop = -offset.top;


            that.scroller.style[transitionDuration] = \'0\';
            that._resetPos(400);
        },

        scrollTo: function (x, y, time, relative) {
            var that = this,
                step = x,
                i, l;

            that.stop();

            if (!step.length) step = [{ x: x, y: y, time: time, relative: relative }];

            for (i=0, l=step.length; i<l; i++) {
                if (step[i].relative) { step[i].x = that.x - step[i].x; step[i].y = that.y - step[i].y; }
                that.steps.push({ x: step[i].x, y: step[i].y, time: step[i].time || 0 });
            }

            that._startAni();
        },

        scrollToElement: function (el, time) {
            var that = this, pos;
            el = el.nodeType ? el : that.scroller.querySelector(el);
            if (!el) return;

            pos = that._offset(el);
            pos.left += that.wrapperOffsetLeft;
            pos.top += that.wrapperOffsetTop;

            pos.left = pos.left > 0 ? 0 : pos.left < that.maxScrollX ? that.maxScrollX : pos.left;
            pos.top = pos.top > that.minScrollY ? that.minScrollY : pos.top < that.maxScrollY ? that.maxScrollY : pos.top;
            time = time === undefined ? m.max(m.abs(pos.left)*2, m.abs(pos.top)*2) : time;

            that.scrollTo(pos.left, pos.top, time);
        },

        scrollToPage: function (pageX, pageY, time) {
            var that = this, x, y;

            time = time === undefined ? 400 : time;

            if (that.options.onScrollStart) that.options.onScrollStart.call(that);


            x = -that.wrapperW * pageX;
            y = -that.wrapperH * pageY;
            if (x < that.maxScrollX) x = that.maxScrollX;
            if (y < that.maxScrollY) y = that.maxScrollY;


            that.scrollTo(x, y, time);
        },

        disable: function () {
            this.stop();
            this._resetPos(0);
            this.enabled = false;

            // If disabled after touchstart we make sure that there are no left over events
            this._unbind(MOVE_EV, window);
            this._unbind(END_EV, window);
            this._unbind(CANCEL_EV, window);
        },

        enable: function () {
            this.enabled = true;
        },

        stop: function () {
            if (this.options.useTransition) this._unbind(TRNEND_EV);
            else cancelFrame(this.aniTime);
            this.steps = [];
            this.moved = false;
            this.animating = false;
        },

        isReady: function () {
            return !this.moved &&  !this.animating;
        }
    };

    function prefixStyle (style) {
        if ( vendor === \'\' ) return style;

        style = style.charAt(0).toUpperCase() + style.substr(1);
        return vendor + style;
    }

    dummyStyle = null;	// for the sake of it

    if (typeof exports !== \'undefined\') exports.iScroll = iScroll;
    else window.iScroll = iScroll;

    (function($){
        if(!$)return;
        var orgiScroll = iScroll,
            id = 0,
            cacheInstance = {};
        function createInstance(el,options){
            var uqid = \'iscroll\' + id++;
            el.data(\'_iscroll_\',uqid);
            return cacheInstance[uqid] = new orgiScroll(el[0],options)
        }
        window.iScroll = function(el,options){
            return createInstance($(typeof el == \'string\' ? \'#\' + el : el),options)
        };
        $.fn.iScroll = function(method){
            var resultArr = [];
            this.each(function(i,el){
                if(typeof method == \'string\'){
                    var instance = cacheInstance[$(el).data(\'_iscroll_\')],pro;
                    if(instance && (pro = instance[method])){
                        var result = $.isFunction(pro) ? pro.apply(instance, Array.prototype.slice.call(arguments,1)) : pro;
                        if(result !== instance && result !== undefined){
                            resultArr.push(result);
                        }
                    }
                }else{
                    if(!$(el).data(\'_iscroll_\'))
                        createInstance($(el),method)
                }
            });

            return resultArr.length ? resultArr : this;
        }
    })(window.Zepto || null)



})(window, document);
/**
 * Change list
 * 修改记录
 *
 * 1. 2012-08-14 解决滑动中按住停止滚动，松开后被点元素触发点击事件。
 *
 * 具体修改:
 * a. 202行 添加isStopScrollAction: false 给iScroll的原型上添加变量
 * b. 365行 _start方法里面添加that.isStopScrollAction = false; 默认让这个值为false
 * c. 390行 if (x != that.x || y != that.y)条件语句里面 添加了  that.isStopScrollAction = true; 当目标值与实际值不一致，说明还在滚动动画中
 * d. 554行 that.isStopScrollAction || (that.doubleTapTimer = setTimeout(function () {
 *          ......
 *          ......
 *          }, that.options.zoom ? 250 : 0));
 *   如果isStopScrollAction为true就不派送click事件
 *
 *
 * 2. 2012-08-14 给options里面添加speedScale属性，提供外部控制冲量滚动速度
 *
 * 具体修改
 * a. 108行 添加speedScale: 1, 给options里面添加speedScale属性，默认为1
 * b. 798行 speed = m.abs(dist) * this.options.speedScale / time, 在原来速度的基础上*speedScale来改变速度
 *
 * 3. 2012-08-21 修改部分代码，给iscroll_plugin墙用的
 *
 * 具体修改
 * a. 517行  在_pos之前，调用_beforePos,如果里面不返回true,  将不会调用_pos
 *  // internal for header scroll
 *  if (that._beforePos)
 *      that._beforePos(newY, deltaY) && that._pos(newX, newY);
 *  else
 *      that._pos(newX, newY);
 *
 * b. 680行 在滚动结束后调用 _afterPos.
 * // internal for header scroll
 * if (that._afterPos) that._afterPos();
 *
 * c. 106行构造器里面添加以下代码
 * // add var to this for header scroll
 * that.translateZ = translateZ;
 *
 * 为处理溢出
 * _bind 方法
 * destroy 方法
 * 最开头的 _bindArr = []
 *
 */
/**
 * @file GMU定制版iscroll，基于[iScroll 4.2.2](http://cubiq.org/iscroll-4), 去除zoom, pc兼容，snap, scrollbar等功能。同时把iscroll扩展到了Zepto的原型中。
 * @name iScroll
 * @import core/zepto.js
 * @desc GMU定制版iscroll，基于{@link[http://cubiq.org/iscroll-4] iScroll 4.2.2}, 去除zoom, pc兼容，snap, scrollbar等功能。同时把iscroll扩展到了***Zepto***的原型中。
 */

/**
 * @name iScroll
 * @grammar new iScroll(el,[options])  ⇒ self
 * @grammar $(\'selecotr\').iScroll([options])  ⇒ zepto实例
 * @desc 将iScroll加入到了***$.fn***中，方便用Zepto的方式调用iScroll。
 * **el**
 * - ***el {String/ElementNode}*** iscroll容器节点
 *
 * **Options**
 * - ***hScroll*** {Boolean}: (可选, 默认: true)横向是否可以滚动
 * - ***vScroll*** {Boolean}: (可选, 默认: true)竖向是否可以滚动
 * - ***momentum*** {Boolean}: (可选, 默认: true)是否带有滚动效果
 * - ***checkDOMChanges*** {Boolean, 默认: false}: (可选)每个500毫秒判断一下滚动区域的容器是否有新追加的内容，如果有就调用refresh重新渲染一次
 * - ***useTransition*** {Boolean, 默认: false}: (可选)是否使用css3来来实现动画，默认是false,建议开启
 * - ***topOffset*** {Number}: (可选, 默认: 0)可滚动区域头部缩紧多少高度，默认是0， ***主要用于头部下拉加载更多时，收起头部的提示按钮***
 * @example
 * $(\'div\').iscroll().find(\'selector\').atrr({\'name\':\'aaa\'}) //保持链式调用
 * $(\'div\').iScroll(\'refresh\');//调用iScroll的方法
 * $(\'div\').iScroll(\'scrollTo\', 0, 0, 200);//调用iScroll的方法, 200ms内滚动到顶部
 */


/**
 * @name destroy
 * @desc 销毁iScroll实例，在原iScroll的destroy的基础上对创建的dom元素进行了销毁
 * @grammar destroy()  ⇒ undefined
 */

/**
 * @name refresh
 * @desc 更新iScroll实例，在滚动的内容增减时，或者可滚动区域发生变化时需要调用***refresh***方法来纠正。
 * @grammar refresh()  ⇒ undefined
 */

/**
 * @name scrollTo
 * @desc 使iScroll实例，在指定时间内滚动到指定的位置， 如果relative为true, 说明x, y的值是相对与当前位置的。
 * @grammar scrollTo(x, y, time, relative)  ⇒ undefined
 */
/**
 * @name scrollToElement
 * @desc 滚动到指定内部元素
 * @grammar scrollToElement(element, time)  ⇒ undefined
 * @grammar scrollToElement(selector, time)  ⇒ undefined
 */
/**
 * @name scrollToPage
 * @desc 跟scrollTo很像，这里传入的是百分比。
 * @grammar scrollToPage(pageX, pageY, time)  ⇒ undefined
 */
/**
 * @name disable
 * @desc 禁用iScroll
 * @grammar disable()  ⇒ undefined
 */
/**
 * @name enable
 * @desc 启用iScroll
 * @grammar enable()  ⇒ undefined
 */
/**
 * @name stop
 * @desc 定制iscroll滚动
 * @grammar stop()  ⇒ undefined
 */

',
    '4e70a49e' => ';/**
 * @file
 * @ignore
 * @name Fx
 * @desc 扩展animate，使其具有队列功能
 * @import core/zepto.js
 */
(function ($, undefined) {
    var speeds = {_default: 400, fast: 200, slow: 600},
        supportedTransforms = /^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i,
        transformr = /transform$/i,
        prefix = $.fx.cssPrefix,
        clearProperties = {};

    clearProperties[prefix + \'transition-property\'] =
        clearProperties[prefix + \'transition-duration\'] =
            clearProperties[prefix + \'transition-timing-function\'] =
                clearProperties[prefix + \'animation-name\'] =
                    clearProperties[prefix + \'animation-duration\'] = \'\';

    /**
     * 队列类定义
     */
    function Queue() {
        this.length = 0;
        this._act = null;
    }

    /**
     * 给类扩展push, shift, unshift, clear方法
     */
    $.extend(Queue.prototype, {
        push:Array.prototype.push,
        shift:Array.prototype.shift,
        unshift:Array.prototype.unshift,
        clear:function () {
            Array.prototype.splice.apply(this, [0, this.length]);
        }
    });

    /**
     * 通过dom对象获取Queue对象, 一个dom对象对应一个Queue对象，一个dom对象只实例化一个Queue对象
     */
    function _getQueueInstance(elem) {
        var _instance;
        (_instance = elem.data(\'_fx_queue\')) || elem.data(\'_fx_queue\', (_instance = new Queue()));
        return  _instance instanceof Queue ? _instance : null;
    }

    /**
     * 添加方法到Queue对象
     */
    function _add_queue(_instance, fn, elem) {
        // wrap传入的fn, 主要是为了在调用的时候，加入next方法作为第一个形参传入到fn里面。
        var wrap = function () {
            _instance.unshift(\'inprogress\');
            $.isFunction(wrap.start) && wrap.start.call(wrap.elem);
            fn.apply(wrap.elem, [function () {//next 方法
                if (wrap === _instance._act) {
                    _instance._act = null;
                    if (_instance[0] === \'inprogress\') {
                        _instance.shift();
                    }
                    $.later(function () {
                        _continue(_instance);
                    }, 0);
                }
            }]);
        };

        /**
         * start 在此成员执行之前执行
         * stop 当$.stop执行的时候，会调用这个stop方法
         * abort 当调用$.dequeue的时候会调用这个方法, 目前还没想好是否有用
         * elem 保存elem对象，将会作为start, stop, abort的执行者。
         */
        $.each([\'start\', \'stop\', \'abort\'], function (index, key) {
            wrap[key] = fn[key];
            delete fn[key];
        });
        wrap.elem = elem;
        _instance.push(wrap);
    }

    /**
     * 继续下一个方法
     */
    function _continue(_instance) {
        if (_instance[0] === \'inprogress\') return;
        var act = _instance._act = _instance.shift();
        $.isFunction(act) && act();
    }

    /**
     * @desc 往队列中添加成员，或读取队列，或替换队列. 见{@link queue}.
     * @name $.queue
     * @grammar  $.queue($el)   ⇒ array
     * @grammar  $.queue($el, fn)   ⇒ $el
     * @grammar  $.queue($el, data)   ⇒ $el
     * @param   {Element}           elem zepto对象
     * @param   {Function|Array}    data 可以是一个fn, 也可以是一个数组，如果是数组就会替换队列
     * @return  {Element|Array}    返回Zepto对象,或者队列数组，如果data传入的是fn，则放回Zepto对象，否则返回当前队列数组
     * @example var div = $(\'div\');
     * div.animate({left:200});
     * console.log($.queue(div));// => [fn] 返回指定元素中的队列集合
     *
     * $.queue(div, function(next){ // 往队列中插入一个动作
     *     next();
     * });
     *
     * $.queue(div, [function(next){
     *      //队列中的第一个成员
     * }, function(){
     *      //队列中的第二个成员
     * }, function(){
     *      //队列中的第三个成员
     * }]);//替换这个队列
     */
    $.queue = function (elem, data) {
        var _instance = _getQueueInstance(elem);

        if (!_instance) {
            return elem;
        }
        if ($.isFunction(data)) {

            _add_queue(_instance, data, elem);

        } else if ($.isArray(data)) {

            _instance.clear();

            $.each(data, function (key, val) {
                _add_queue(_instance, val, elem);
            });

        }
        setTimeout(function () {
            _continue(_instance);
        }, 0);

        return data === undefined ? _instance : elem;
    };

    /**
     * @desc 继续队列，如果队列当前成员没有执行完毕，则直接跳过. 见{@link dequeue}.
     * @name $.dequeue
     * @grammar $.dequeue($el)   ⇒ $el
     * @param  {Element} elem Zepto对象
     * @return {Element} 返回Zepto对象
     * @example var div = $(\'div\');
     * div.animate({left: 200});
     *
     * div.queue(function(next){
     *  //do something
     *  $.dequeue(div);//停止队列中的当前成员，继续队列的下一个
     * });
     */
    $.dequeue = function (elem) {
        var _instance = _getQueueInstance(elem), inprogress, act;

        if (!_instance) return elem;

        while (_instance[0] === \'inprogress\') {
            inprogress = true;
            _instance.shift();
        }
        inprogress && (act = _instance._act) && act.abort && act.abort.call(act.elem);

        _continue(_instance);
        return elem;
    };

    /**
     * @desc 清楚队列. 见{@link clearQueue}.
     * @name $.clearQueue
     * @grammar  $.clearQueue($el)   ⇒ $el
     * @param  {Element} elem Zepto对象
     * @return {Element} 返回Zepto对象
     * @example var div = $(\'div\');
     * div.animate({left:200});
     * div.animate({top:200});
     *
     * $(\'a\').on(\'click\', function(e){
     *     $.clearQueue(div);//队列中的当前成员，执行完后，就停止。
     * });
     */
    $.clearQueue = function (elem) {
        var _instance = _getQueueInstance(elem);

        if (_instance) {
            _instance.clear();
        }

        return elem
    };

    // 将fast, slow转化成对应的时间
    function _translateSpeed(speed, second) {
        var val = typeof speed == \'number\' ? speed : (speeds[speed] || speeds._default);
        return second ? val / 1000 : val;
    }

    $.extend($.fn, {
        /**
         * @desc 覆写Zepto中的{@link[http://zeptojs.com/#animate] animate}，让其默认以队列方式运行, 同时可以设置queue为false来关闭此功能。
         * @name animate
         * @grammar animate(properties, [duration, [easing, [function(){ ... }]]])  ⇒ self
         * @grammar animate(properties, { duration: msec, easing: type, complete: fn })  ⇒ self
         * @grammar animate(animationName, { ... })  ⇒ self
         * @example var div = $(\'div\');
         * div.animate({left: 200});
         * div.animate({top: 200}); //动画2会在动画1执行完后才执行
         *
         * div.animate({top:500}, {queue: false})//由于设置了queue为false，所以此动画马上就会开始执行
         */
        animate:function (properties, duration, ease, callback) {
            var me = this, timer, wrapCallback, start, queue, endEvent;

            if ($.isObject(duration)) {
                ease = duration.easing;
                callback = duration.complete;
                start = duration.start;
                queue = duration.queue;
                duration = duration.duration;
            }

            duration = _translateSpeed(duration, false);
            if (duration) duration = duration / 1000;

            if (queue === false) {
                return me.anim(properties, duration, ease, callback);
            }

            endEvent = $.fx.transitionEnd;
            if (typeof properties == \'string\') {
                endEvent = $.fx.animationEnd;
            }

            wrapCallback = function (next) {
                var _executed = false,
                    _fn = function () {
                        if (_executed) return;//保证fn只执行一次
                        _executed = true;
                        timer && clearTimeout(timer);

                        callback && callback.apply(me, arguments);
                        next();
                    };
                me.anim(properties, duration, ease, _fn);
                //如果当前样式值与目标样式值一致，transitionEnd事件不会派送, 所以这里做个timeout，确保next方法会执行。
                //todo 找个更好的方法解决这个问题
                duration && (timer = setTimeout(_fn, duration * 1000 + 500)); //这里有500ms差时
            };

            wrapCallback.start = start;

            //主要用来停下动画的，css transition队列，停止的方法是把样式值设成当前值，然后吧transition给清除了
            wrapCallback.stop = function (jumpToEnd) {
                var props = {},
                    hasTransfrom = false,
                    cur;

                timer && clearTimeout(timer);

                if (!jumpToEnd) {
                    cur = getComputedStyle(this[0], \'\');
                    $.each(properties, function (key) {
                        if (supportedTransforms.test(key) || transformr.test(key)) {
                            hasTransfrom = true;
                        } else {
                            props[key] = cur[key] || \'\';
                        }
                    });
                    //todo 支持其他非webkit浏览器
                    hasTransfrom && (props[prefix + \'transform\'] = cur[\'WebkitTransform\']);
                }
                //todo 只解除zepto中anim里面的wrappedCallback
                me.unbind(endEvent);

                me.css($.extend(props, clearProperties));
                jumpToEnd && callback && callback();
            };
            return $.queue(me, wrapCallback)
        },

        /**
         * @desc 往队列中添加一个指定时间的延时
         * @name     delay
         * @grammar  delay(duration)  ⇒ self
         * @param    {Number}    duration 指定延时时间
         * @return   {Element}   返回Zepto对象
         * @example var div = $(div);
         * div.animate({top:100}).delay(1000).animate({left:100});
         * //动画3，在动画1执行完了后，1秒钟才执行
         */
        delay:function (duration) {
            var timer, _fn = function(next){
                timer = setTimeout(next, duration);
            };
            _fn.stop = function(){
                timer && clearTimeout(timer);
            };
            return $.queue(this, _fn);
        },

        /**
         * @desc 停止当前动画
         *
         * **参数**
         * - ***clearQueue***  *可选(默认 true)* 标记是否清除队列
         * - ***jumpToEnd***  *可选(默认 false)* 停止当前动画时，是否将动画直接执行完毕，而不是在当前位置停止
         *
         * @name stop
         * @grammar  stop([clearQueue[, jumpToEnd]])  ⇒ self
         * @example var div = $(\'div\');
         * div.animate({left:200})
         *    .animate({left: 400});
         *
         * $(\'a\').on(\'click\', function(){
         *      div.stop();//停止动画
         * });
         */
        stop:function (clearQueue, jumpToEnd) {
            var _instance = _getQueueInstance(this), act;
            if (_instance) {
                clearQueue === undefined && (clearQueue = true );
                clearQueue && this.clearQueue();
                act = _instance._act;
                if (act) {
                    _instance._act = null;
                    $.isFunction(act.stop) && act.stop.call(act.elem, jumpToEnd);
                }
                if (!clearQueue) {
                    this.dequeue();
                }
            }
            return this;
        },

        /**
         * @desc 往队列中添加成员
         * @function
         * @name queue
         * @grammar  queue()   ⇒ array
         * @grammar  queue(fn)   ⇒ self
         * @grammar  queue(data)   ⇒ self
         * @example var div = $(\'div\');
         * div.animate({left:200});
         * console.log(div.queue());// => [fn] 返回指定元素中的队列集合
         *
         * div.queue(function(next){ // 往队列中插入一个动作
         *     next();
         * });
         *
         * div.queue([function(next){
         *      //队列中的第一个成员
         * }, function(){
         *      //队列中的第二个成员
         * }, function(){
         *      //队列中的第三个成员
         * }]);//替换这个队列
         */
        queue:function (data) {
            return $.queue(this, data);
        },

        /**
         * @desc 继续队列，如果队列当前成员没有执行完毕，则直接跳过
         * @name dequeue
         * @grammar dequeue   ⇒ self
         * @example var div = $(\'div\');
         * div.animate({left: 200});
         *
         * div.queue(function(next){
         *  //do something
         *  div.dequeue();//停止队列中的当前成员，继续队列的下一个
         * });
         */
        dequeue:function () {
            return $.dequeue(this);
        },

        /**
         * @desc 清空队列
         * @name        clearQueue
         * @grammar  queue(fn)   ⇒ self
         * @example var div = $(\'div\');
         * div.animate({left:200});
         * div.animate({top:200});
         *
         * $(\'a\').on(\'click\', function(e){
         *     $.clearQueue(div);//队列中的当前成员，执行完后，就停止。
         * });
         */
        clearQueue:function () {
            return $.clearQueue(this);
        }

    }, false);

    $.extend($.fx, {
        speeds:speeds
    });
})(Zepto);',
    '8a990505' => ';/**
 * @file 实现了通用fix方法。
 * @name Fix
 * @import core/zepto.extend.js
 */

/**
 * @name fix
 * @grammar fix(options)   ⇒ self
 * @desc 固顶fix方法，对不支持position:fixed的设备上将元素position设为absolute，
 * 在每次scrollstop时根据opts参数设置当前显示的位置，类似fix效果。
 *
 * Options:
 * - \'\'top\'\' {Number}: 距离顶部的px值
 * - \'\'left\'\' {Number}: 距离左侧的px值
 * - \'\'bottom\'\' {Number}: 距离底部的px值
 * - \'\'right\'\' {Number}: 距离右侧的px值
 * @example
 * var div = $(\'div\');
 * div.fix({top:0, left:0}); //将div固顶在左上角
 * div.fix({top:0, right:0}); //将div固顶在右上角
 * div.fix({bottom:0, left:0}); //将div固顶在左下角
 * div.fix({bottom:0, right:0}); //将div固顶在右下角
 *
 * @blackList
 * - M031 魅族4.0(支持position:fixed;但是元素的visibility为hidden);
 */

(function ($, undefined) {
    $.extend($.fn, {
        fix: function(opts) {
            var me = this;                      //如果一个集合中的第一元素已fix，则认为这个集合的所有元素已fix，
            if(me.attr(\'isFixed\')) return me;   //这样在操作时就可以针对集合进行操作，不必单独绑事件去操作
            me.css(opts).css(\'position\', \'fixed\').attr(\'isFixed\', true);
            var blackList = /M031/.test(navigator.userAgent),
                doFixed = function() {
                    me.css({
                        top: window.pageYOffset + (opts.bottom !== undefined ? window.innerHeight - me.height() - opts.bottom : (opts.top ||0)),
                        left: opts.right !== undefined ? document.body.offsetWidth - me.width() - opts.right : (opts.left || 0)
                    });
                    opts.width == \'100%\' && me.css(\'width\', document.body.offsetWidth);
                };
            if(blackList) {
                me.css(\'position\', \'absolute\');
                $(document).on(\'scrollStop\', doFixed);
                $(window).on(\'ortchange\', doFixed);
            } else {
                var buff = $(\'<div style="position:fixed;top:10px;"></div>\').appendTo(\'body\'),
                    top = buff.offset(true).top,
                    checkFixed = function() {
                        if(window.pageYOffset > 0) {
                            if(buff.offset(true).top !== top) {
                                me.css(\'position\', \'absolute\');
                                doFixed();
                                $(document).on(\'scrollStop\', doFixed);
                                $(window).on(\'ortchange\', doFixed);
                            }
                            $(document).off(\'scrollStop\', checkFixed);
                            buff.remove();
                        }
                    };
                $(document).on(\'scrollStop\', checkFixed);
            }
            return me;
        }
    });
}(Zepto));',
    'c4b18900' => ';/**
 *  @file 实现了通用highlight方法。
 *  @name Highlight
 *  @desc 点击高亮效果
 *  @import core/zepto.js, core/zepto.extend.js
 */
(function($) {
    var actElem, inited = false, timer, cls, removeCls = function(){
        clearTimeout(timer);
        if(actElem && (cls = actElem.attr(\'highlight-cls\'))){
            actElem.removeClass(cls).attr(\'highlight-cls\', \'\');
            actElem = null;
        }
    };
    $.extend($.fn, {
        /**
         * @name highlight
         * @desc 禁用掉系统的高亮，当手指移动到元素上时添加指定class，手指移开时，移除该class
         * @grammar  highlight(className)   ⇒ self
         * @example var div = $(\'div\');
         * div.highlight(\'div-hover\');
         *
         * $(\'a\').highlight();// 把所有a的自带的高亮效果去掉。
         */
        highlight: function(className) {
            inited = inited || !!$(document).on(\'touchend.highlight touchmove.highlight touchcancel.highlight\', removeCls);
            removeCls();
            return this.each(function() {
                var $el = $(this);
                $el.css(\'-webkit-tap-highlight-color\', \'rgba(255,255,255,0)\').off(\'touchstart.highlight\');
                className && $el.on(\'touchstart.highlight\', function() {
                    timer = $.later(function() {
                        actElem = $el.attr(\'highlight-cls\', className).addClass(className);
                    }, 100);
                });
            });
        }
    });
})(Zepto);
',
    '64b76138' => ';/**
 * @file 返回顶部组件
 * @name Gotop
 * @desc 提供一个快速回到页面顶部的按钮
 * @import core/zepto.extend.js, core/zepto.ui.js,core/zepto.fix.js
 */
(function($, undefined) {
    /**
     * @name     $.ui.gotop
     * @grammar  $(el).gotop(options) ⇒ self
     * @grammar  $.ui.gotop([el [,options]]) =>instance
     * @desc **el**
     * css选择器, 或者zepto对象
     * **Options**
     * - \'\'container\'\' {selector}: (可选,默认：body) 组件容器
     * - \'\'useAnimation\'\' {Boolean}: (可选, 默认为true), 返回顶部时是否使用动画,在使用iScroll时,返回顶部的动作由iScroll实例执行,此参数无效
     * - \'\'position\'\' {Object}: (可选, 默认为{bottom:10, right:10}), 使用fix效果时，要用的位置参数
     * - \'\'afterScroll\'\' {function}: (可选,默认：null) 返回顶部后执行的回调函数
     * - \'\'iScrollInstance\'\' {Object}: (可选) 使用iscroll时需要传入iScroll实例，用来判定显示与隐藏
     * - \'\'disablePlugin\'\' {Boolean}: (可选,默认：false) 是否禁用插件，当加载了gotop.iscroll.js插件但又不想用该插件时，可传入这个参数来禁用插件
     * **Demo**
     * <codepreview href="../gmu/_examples/widget/gotop/gotop.html">
     * ../gmu/_examples/widget/gotop/gotop.html
     * ../gmu/_examples/widget/gotop/gotop_demo.css
     * </codepreview>
     */
    $.ui.define(\'gotop\', {
        _data: {
            container:          \'\',
            useAnimation:       true,
            useFix:             true,
            position:           {bottom: 10, right: 10},
        	afterScroll:        null,
            iScrollInstance:    null,
            disablePlugin:      false,
            _isShow:            false
        },

        _create: function() {
            var me = this;
            (me.root() || me.root($(\'<div></div>\'))).addClass(\'ui-gotop\').append(\'<div></div>\').appendTo(me.data(\'container\') || (me.root().parent().length ? \'\' : document.body));
            return me;
        },

        _setup: function(mode) {
            var me = this;
            me._create();
            return me;
        },

        _init: function() {
            var me = this,
                root = me.root(),
                _eventHandler = $.proxy(me._eventHandler, me);
            $(document).on(\'touchmove touchend touchcancel scrollStop\', _eventHandler);
            $(window).on(\'scroll ortchange\', _eventHandler);
            root.on(\'click\', _eventHandler);
            me.on(\'destroy\', function() {
                $(document).off(\'touchmove touchend touchcancel scrollStop\', _eventHandler);
                $(window).off(\'scroll ortchange\', _eventHandler);
            });
            me.data(\'useFix\') && root.fix(me.data(\'position\'));
            me.data(\'root\', root[0]);
            return me;
        },

        /**
         * 事件处理中心
         */
        _eventHandler: function(e) {
            var me = this;
            switch (e.type) {
                case \'touchmove\':
                    me.hide();
                    clearTimeout(me.data(\'_TID\'));
                    me.data(\'_TID\', $.later(function(){
                        me._check.call(me);
                    }, 300));
                    break;
                case \'scroll\':
                    clearTimeout(me.data(\'_TID\'));
                    break;
                case \'touchend\':
                case \'touchcancel\':
                    clearTimeout(me.data(\'_TID\'));
                    me.data(\'_TID\', $.later(function(){
                        me._check.call(me);
                    }, 300));
                    break;
                case \'scrollStop\':
                    me._check();
                    break;
                case \'ortchange\':
                    me._check.call(me);
                    break;
                case \'click\':
                    me._scrollTo();
                    break;
            }
        },

        /**
         * 判断是否显示gotop
         */
        _check: function(position) {
            var me = this;
            (position !== undefined ? position : window.pageYOffset) > document.documentElement.clientHeight ? me.show() : me.hide();
            return  me;
        },

		/**
         * 滚动到顶部或指定节点位置
         */
		_scrollTo: function() {
            var me = this,
                from = window.pageYOffset;
            me.hide();
            clearTimeout(me.data(\'_TID\'));
            if (!me.data(\'useAnimation\')) {
                window.scrollTo(0, 1);
                me.trigger(\'afterScroll\');
            } else {
                me.data(\'moveToTop\', $.later(function() {
                    if (from > 1) {
                        window.scrollBy(0, -Math.min(150,from - 1));
                        from -= 150;
                    } else {
                        clearInterval(me.data(\'moveToTop\'));
                        me.trigger(\'afterScroll\');
                    }
                }, 25, true));
            }
            return me;
		},

        /**
         * @desc 显示gotop
         * @name show
         * @grammar show() => self
         *  @example
         * //setup mode
         * $(\'#gotop\').gotop(\'show\');
         *
         * //render mode
         * var demo = $.ui.gotop();
         * demo.show();
         */

        show: function() {
            var me = this;
            me.data(\'root\').style.display = \'block\'; //写style让浏览器repaint,不能用zepto方法
            me.data(\'_isShow\', true);
            return me;
        },

        /**
         * @desc 隐藏gotop
         * @name hide
         * @grammar hide() => self
         * @example
         * //setup mode
         * $(\'#gotop\').gotop(\'hide\');
         *
         * //render mode
         * var demo = $.ui.gotop();
         * demo.hide();
         */
        hide: function() {
            var me = this;
            if(me.data(\'_isShow\')) {
                me.data(\'root\').style.display = \'none\';
                me.data(\'_isShow\', false);
            }
            return me;
        }
        /**
         * @name Trigger Events
         * @theme event
         * @desc 组件内部触发的事件
         * ^ 名称 ^ 处理函数参数 ^ 描述 ^
         * | init | event | 组件初始化的时候触发，不管是render模式还是setup模式都会触发 |
         * | afterScroll | event | 返回顶部后触发的事件 |
         * | destory | event | 组件在销毁的时候触发 |
         */
    });

})(Zepto);
',
    '022a0e72' => '.ui-gotop{position:fixed;display:none;width:50px;height:50px;bottom:10px;right:10px;z-index:999;cursor:pointer;-webkit-tap-highlight-color:rgba(0,0,0,0);}.ui-gotop div{margin:5px;width:40px;height:40px;border-radius:2px;-webkit-box-shadow:0 0 5px #9c9c9c;opacity:.9;background:#454545 url(/static/common/gmu-GMU_2.0.3_TAG1/assets/widget/gotop/ui-gotop-icon_3b20fcd2.png) no-repeat center center;-webkit-background-size:18px 15px;}@media all and(min-device-width:768px) and(max-device-width:1024px){.ui-gotop{width:60px;height:60px;}.ui-gotop div{width:48px;height:48px;-webkit-background-size:22px 18px;}}',
    '771aff3e' => ';/**
 * @file 图片轮播组件
 * @name Slider
 * @desc 图片轮播组件
 * @import core/zepto.extend.js, core/zepto.ui.js
 */

(function($, undefined) {
    /**
     * @name       $.ui.slider
     * @grammar    $.ui.slider(el [,options]) => instance
     * @desc **el**
     * css选择器, 或者zepto对象
     * **Options**
     * - \'\'container\'\' {selector|zepto}: (可选)放置的父容器
     * - \'\'content\'\' {Array}: (必选)内容,格式为：[ {href:\'图片跳转URL\', pic:\'图片路径\', title:\'图片下方文字\'}, {...}]
     * - \'\'viewNum\'\' {Number}: (可选, 默认:1) 可以同时看到几张图片
     * - \'\'imgInit\'\' {Number}: (可选, 默认:2)初始加载几张图片
     * - \'\'imgZoom\'\' {Boolean}: (可选, 默认:false)是否缩放图片,设为true时可以将超出边界的图片等比缩放
     * - \'\'loop\'\' {Boolean}: (可选, 默认:false)设为true时,播放到最后一张时继续正向播放第一张(无缝滑动)，设为false则反向播放倒数第2张
     * - \'\'stopPropagation\'\' {Boolean}: (可选, 默认:false)是否在横向滑动的时候阻止冒泡(慎用,会导致上层元素接受不到touchMove事件)
     * - \'\'springBackDis\'\' {Number}: (可选, 默认:15)滑动能够回弹的最大距离
     * - \'\'autoPlay\'\' {Boolean}: ((可选, 默认:true)是否自动播放
     * - \'\'autoPlayTime\'\' {Number}: (可选, 默认:4000ms)自动播放的间隔
     * - \'\'animationTime\'\' {Number}: (可选, 默认:400ms)滑动动画时间
     * - \'\'showArr\'\' {Boolean}: (可选, 默认:true)是否展示上一个下一个箭头
     * - \'\'showDot\'\' {Boolean}: (可选, 默认:true)是否展示页码
     * - \'\'slide\'\' {Function}: (可选)开始切换页面时执行的函数,第1个参数为Event对象,第2个参数为滑动后的page页码
     * - \'\'slideend\'\' {Function}: (可选)页面切换完成(滑动完成)时执行的函数,第1个参数为Event对象,第2个参数为滑动后的page页码
     *
     * **Demo**
     * <codepreview href="../gmu/_examples/widget/slider/slider.html">
     * ../gmu/_examples/widget/slider/slider.html
     * </codepreview>
     */
    $.ui.define(\'slider\', {
        _data:{
            viewNum:                1,
            imgInit:                2,
            imgZoom:                false,
            loop:                   false,
            stopPropagation:        false,
            springBackDis:          15,
            autoPlay:               true,
            autoPlayTime:           4000,
            animationTime:          400,
            showArr:                true,
            showDot:                true,
            slide:                  null,
            slideend:               null,
            index:                  0,
            _stepLength:            1,
            _direction:             1
        },

        _create:function() {
            var me = this,
                i = 0, j, k = [],
                content = me.data(\'content\');
            me._initConfig();
            (me.root() || me.root($(\'<div></div>\'))).addClass(\'ui-slider\').appendTo(me.data(\'container\') || (me.root().parent().length ? \'\' : document.body)).html(
            \'<div class="ui-slider-wheel"><div class="ui-slider-group">\' +
            (function() {
                for(; j = content[i]; i++) k.push(\'<div class="ui-slider-item"><a href="\' + j.href + \'"><img lazyload="\' + j.pic + \'"/></a>\' + (j.title ? \'<p>\' + j.title + \'</p>\': \'\') + \'</div>\');
                k.push(me.data(\'loop\') ? \'</div><div class="ui-slider-group">\' + k.join(\'\') + \'</div></div>\' : \'</div></div>\');
                return k.join(\'\');
            }()));
            me._addDots();
        },

        _setup: function(mode) {
            var me = this,
                root = me.root().addClass(\'ui-slider\');
            me._initConfig();
            if(!mode) {
                var items = root.children(),
                    group = $(\'<div class="ui-slider-group"></div>\').append(items.addClass(\'ui-slider-item\'));
                root.empty().append($(\'<div class="ui-slider-wheel"></div>\').append(group).append(me.data(\'loop\') ? group.clone() : \'\'));
                me._addDots();
            } else me.data(\'loop\') && $(\'.ui-slider-wheel\', root).append($(\'.ui-slider-group\', root).clone());
        },

        _init:function() {
            var me = this,
                index = me.data(\'index\'),
                root = me.root(),
                _eventHandler = $.proxy(me._eventHandler, me);
            me._setWidth();
            $(me.data(\'wheel\')).on(\'touchstart touchmove touchend touchcancel webkitTransitionEnd\', _eventHandler);
            $(window).on(\'ortchange\', _eventHandler);
            $(\'.ui-slider-pre\', root).on(\'tap\', function() { me.pre() });
            $(\'.ui-slider-next\', root).on(\'tap\', function() { me.next() });
            me.on(\'destroy\',function() {
                clearTimeout(me.data(\'play\'));
                $(window).off(\'ortchange\', _eventHandler);
            });
            me.data(\'autoPlay\') && me._setTimeout();
        },

        /**
         * 初始化参数配置
         */
        _initConfig: function() {
            var o = this._data;
            if(o.viewNum > 1) {
                o.loop = false;
                o.showDot = false;
                o.imgInit = o.viewNum + 1;
            }
        },

        /**
         * 添加底部圆点及两侧箭头
         */
        _addDots:function() {
            var me = this,
                root = me.root(),
                length = $(\'.ui-slider-item\', root).length / (me.data(\'loop\') ? 2 : 1),
                html = [];
            if(me.data(\'showDot\')) {
                html.push(\'<p class="ui-slider-dots">\');
                while(length--) html.push(\'<b></b>\');
                html.push(\'</p>\');
            }
            me.data(\'showArr\') && (html.push(\'<span class="ui-slider-pre"><b></b></span><span class="ui-slider-next"><b></b></span>\'));
            root.append(html.join(\'\'));
        },
        /**
         * 设置轮播条及元素宽度,设置选中dot,设置索引map,加载图片
         */
        _setWidth:function(){
            var me = this,
                o = me._data,
                root = me.root(),
                width = Math.ceil(root.width() / o.viewNum),
                height = root.height(),
                loop = o.loop,
                items = $(\'.ui-slider-item\', root).toArray(),
                length = items.length,
                wheel = $(\'.ui-slider-wheel\', root).width(width * length)[0],
                dots = $(\'.ui-slider-dots b\', root).toArray(),
                allImgs = $(\'img\', root).toArray(),
                lazyImgs = allImgs.concat(),
                dotIndex = {}, i, j,
                l = o.imgInit || length;
            o.showDot && (dots[0].className = \'ui-slider-dot-select\');
            if(o.imgZoom) $(lazyImgs).on(\'load\', function() {
                var h = this.height,
                    w = this.width,
                    min_h = Math.min(h, height),
                    min_w = Math.min(w, width);
                if(h/height > w/width) this.style.cssText += \'height:\' + min_h + \'px;\' + \'width:\' + min_h/h * w + \'px;\';
                else this.style.cssText += \'height:\' + min_w/w * h + \'px;\' + \'width:\' + min_w + \'px\';
                this.onload = null;
            });
            for(i = 0; i < length; i++) {
                items[i].style.cssText += \'width:\'+ width + \'px;position:absolute;-webkit-transform:translate3d(\' + i * width + \'px,0,0);z-index:\' + (900 - i);
                dotIndex[i] = loop ? (i > length/2 - 1  ? i - length/2 : i) : i;
                if(i < l) {
                    j = lazyImgs.shift();
                    j.src = j.getAttribute(\'lazyload\');
                    if(o.loop) {
                        j = allImgs[i + length / 2];
                        j.src = j.getAttribute(\'lazyload\');
                    }
                }
            }
            me.data({
                root:           root[0],
                wheel:          wheel,
                items:          items,
                lazyImgs:       lazyImgs,
                allImgs:        allImgs,
                length:         length,
                width:          width,
                height:         height,
                dots:           dots,
                dotIndex:       dotIndex,
                dot:            dots[0]
            });
            return me;
        },

        /**
         * 事件管理函数
         */
        _eventHandler:function(e) {
            var me = this;
            switch (e.type) {
                case \'touchmove\':
                    me._touchMove(e);
                    break;
                case \'touchstart\':
                    me._touchStart(e);
                    break;
                case \'touchcancel\':
                case \'touchend\':
                    me._touchEnd();
                    break;
                case \'webkitTransitionEnd\':
                    me._transitionEnd();
                    break;
                case \'ortchange\':
                    me._resize.call(me);
                    break;
            }
        },

        /**
         * touchstart事件
         */
        _touchStart:function(e) {
            var me = this;
            me.data({
                pageX:      e.touches[0].pageX,
                pageY:      e.touches[0].pageY,
                S:          false,      //isScrolling
                T:          false,      //isTested
                X:          0           //horizontal moved
            });
            me.data(\'wheel\').style.webkitTransitionDuration = \'0ms\';
        },

        /**
         * touchmove事件
         */
        _touchMove:function(e) {
            var o = this._data,
                X = o.X = e.touches[0].pageX - o.pageX;
            if(!o.T) {
                var index = o.index,
                    length = o.length,
                    S = Math.abs(X) < Math.abs(e.touches[0].pageY - o.pageY);
                o.loop && (o.index = index > 0 && (index < length - 1) ? index : (index === length - 1) && X < 0 ? length/2 - 1 : index === 0 && X > 0 ? length/2 : index);
                S || clearTimeout(o.play);
                o.T = true;
                o.S = S;
            }
            if(!o.S) {
                o.stopPropagation && e.stopPropagation();
                e.preventDefault();
                o.wheel.style.webkitTransform = \'translate3d(\' + (X - o.index * o.width) + \'px,0,0)\';
            }
        },

        /**
         * touchend事件
         */
        _touchEnd:function() {
            var me = this,
                o = me._data;
            if(!o.S) {
                var distance = o.springBackDis,
                stepLength = o.X <= -distance ? Math.ceil(-o.X / o.width) : (o.X > distance) ? -Math.ceil(o.X / o.width) : 0;
                o._stepLength = Math.abs(stepLength);
                me._slide(o.index + stepLength);
            }
        },

        /**
         * 轮播位置判断
         */
        _slide:function(index, auto) {
            var me = this,
                o = me._data,
                length = o.length,
                end = length - o.viewNum + 1;
            if(-1 < index && index < end) {
                me._move(index);
            } else if(index >= end) {
                if(!o.loop) {
                    me._move(end - (auto ? 2 : 1));
                    o._direction = -1;
                } else {
                    o.wheel.style.cssText += \'-webkit-transition:0ms;-webkit-transform:translate3d(-\' + (length/2 - 1) * o.width + \'px,0,0);\';
                    o._direction =  1;
                    $.later(function() {me._move(length/2)}, 20);
                }
            } else {
                if(!o.loop) me._move(auto ? 1 : 0);
                else {
                    o.wheel.style.cssText += \'-webkit-transition:0ms;-webkit-transform:translate3d(-\' + (length/2) * o.width + \'px,0,0);\';
                    $.later(function() {me._move(length/2 - 1)}, 20);
                }
                o._direction =  1;
            }
            return me;
        },

        /**
         * 轮播方法
         */
        _move:function(index) {
            var o = this._data,
                dotIndex = o.dotIndex[index];
            this.trigger(\'slide\', dotIndex);
            if(o.lazyImgs.length) {
                var j = o.allImgs[index];
                j && j.src || (j.src = j.getAttribute(\'lazyload\'));
            }
            if(o.showDot) {
                o.dot.className = \'\';
                o.dots[dotIndex].className = \'ui-slider-dot-select\';
                o.dot = o.dots[dotIndex];
            }
            o.index = index;
            o.wheel.style.cssText += \'-webkit-transition:\' + o.animationTime + \'ms;-webkit-transform:translate3d(-\' + index * o.width + \'px,0,0);\';
        },

        /**
         * 滑动结束
         */
        _transitionEnd:function() {
            var me = this,
                o = me._data;
            me.trigger(\'slideend\', o.dotIndex[o.index]);
            if(o.lazyImgs.length){
                for(var length = o._stepLength, i = 0; i< length; i++) {
                    var j = o.lazyImgs.shift();
                    j && (j.src = j.getAttribute(\'lazyload\'));
                    if(o.loop) {
                        j = o.allImgs[o.index + o.length / 2];
                        j && !j.src && (j.src = j.getAttribute(\'lazyload\'));
                    }
                }
                o._stepLength = 1;
            }
            me._setTimeout();
        },

        /**
         * 设置自动播放
         */
        _setTimeout:function() {
            var me = this, o = me._data;
            if(!o.autoPlay) return me;
            clearTimeout(o.play);
            o.play = $.later(function() {
                me._slide.call(me, o.index + o._direction, true);
            }, o.autoPlayTime);
            return me;
        },

        /**
         * 重设容器及子元素宽度
         */
        _resize:function() {
            var me = this,
                o = me._data,
                width = o.root.offsetWidth / o.viewNum, //todo 添加获取隐藏元素大小的方法
                length = o.length,
                items = o.items;
            if(!width) return me;
            o.width = width;
            clearTimeout(o.play);
            for(var i = 0; i < length; i++) items[i].style.cssText += \'width:\' + width + \'px;-webkit-transform:translate3d(\' + i * width + \'px,0,0);\';
            o.wheel.style.removeProperty(\'-webkit-transition\');
            o.wheel.style.cssText += \'width:\' + width * length + \'px;-webkit-transform:translate3d(-\' + o.index * width + \'px,0,0);\';
            o._direction = 1;
            me._setTimeout();
            return me;
        },

        /**
         * @desc 滚动到上一张
         * @name pre
         * @grammar pre() => self
         *  @example
         * //setup mode
         * $(\'#slider\').slider(\'pre\');
         *
         * //render mode
         * var demo = $.ui.slider();
         * demo.pre();
         */
        pre:function() {
            var me = this;
            me._slide(me.data(\'index\') - 1);
            return me;
        },

        /**
         * @desc 滚动到下一张
         * @name next
         * @grammar next() => self
         *  @example
         * //setup mode
         * $(\'#slider\').slider(\'next\');
         *
         * //render mode
         * var demo = $.ui.slider();
         * demo.next();
         */
        next:function() {
            var me = this;
            me._slide(me.data(\'index\') + 1);
            return me;
        },

        /**
         * @desc 停止自动播放
         * @name stop
         * @grammar stop() => self
         *  @example
         * //setup mode
         * $(\'#slider\').slider(\'stop\');
         *
         * //render mode
         * var demo = $.ui.slider();
         * demo.stop();
         */
        stop:function() {
            var me = this;
            clearTimeout(me.data(\'play\'));
            me.data(\'autoPlay\', false);
            return me;
        },

        /**
         * @desc 恢复自动播放
         * @name resume
         * @grammar resume() => self
         *  @example
         * //setup mode
         * $(\'#slider\').slider(\'resume\');
         *
         * //render mode
         * var demo = $.ui.slider();
         * demo.resume();
         */
        resume:function() {
            var me = this;
            me.data(\'_direction\',1);
            me.data(\'autoPlay\', true);
            me._setTimeout();
            return me;
        }

        /**
         * @name Trigger Events
         * @theme event
         * @desc 组件内部触发的事件
         * ^ 名称 ^ 处理函数参数 ^ 描述 ^
         * | init | event | 组件初始化的时候触发，不管是render模式还是setup模式都会触发 |
         * | slide | event | 开始切换页面时执行的函数，参数为滑动后的page页码 |
         * | slideend | event | 页面切换完成(滑动完成)时执行的函数，参数为滑动后的page页码 |
         * | destory | event | 组件在销毁的时候触发 |
         */
    });
})(Zepto);
',
    'e88ef32f' => '.ui-slider{height:148px;width:100%;overflow:hidden;position:relative;-webkit-user-select:none;}.ui-slider-wheel{height:100%;position:relative;left:0;top:0;-webkit-transform:translate3d(0,0,0);-webkit-transition-duration:0ms;-webkit-animation-timing-function:ease-out;}.ui-slider-wheel a{display:block;text-decoration:none;}.ui-slider-group{height:100%;float:left;}.ui-slider-item{height:100%;width:100%;background-color:#e3e3e3;text-align:center;top:0;display:inline-block;overflow:hidden;}.ui-slider-item p{position:absolute;bottom:0;width:100%;text-align:left;pointer-events:none;overflow:hidden;word-break:break-all;white-space:nowrap;text-overflow:ellipsis;}.ui-slider-dots{position:absolute;bottom:6px;right:0;padding-right:6px;}',
    '718a6061' => '.ui-slider{height:148px;}.ui-slider-item img{background:#E7E7E7 url(/static/common/gmu-GMU_2.0.3_TAG1/assets/widget/slider/ui-slider-imgbg_7d18a223.png) center center no-repeat;}.ui-slider-item p{color:#fff;background:rgba(0,0,0,0.5);padding:6px 0;text-indent:10px;}.ui-slider-dots b{display:inline-block;margin-right:8px;width:6px;height:6px;border-radius:3px;background:rgba(144,144,144,0.8);}.ui-slider-dots .ui-slider-dot-select{background:#fff;}.ui-slider-pre,.ui-slider-next{position:absolute;z-index:99;width:20px;height:40px;top:50%;margin-top:-35px;background:rgba(0,0,0,0.3) url(/static/common/gmu-GMU_2.0.3_TAG1/assets/widget/slider/ui-slider-arrow_6eb369de.png) no-repeat;background-size:35px 15px;outline:none;}.ui-slider-pre b,.ui-slider-next b{display:inline-block;width:50px;height:60px;position:relative;top:-10px;}.ui-slider-pre b{left:0;}.ui-slider-next b{left:-30px;}.ui-slider-pre{background-position:3px center;left:0;border-radius:0 20px 20px 0;}.ui-slider-next{background-position:-18px center;right:0;border-radius:20px 0 0 20px;}@media all and(min-device-width:768px) and(max-device-width:1024px){.ui-slider-item p{font-size:16px;padding:.5em 0;text-indent:.8em;}.ui-slider-dots{bottom:.5em;padding-right:.5em;}.ui-slider-dots b{margin-right:.5em;width:.5em;height:.5em;border-radius:.25em;}.ui-slider-pre,.ui-slider-next{width:24px;height:48px;top:50%;margin-top:-42px;background-size:42px 18px;}.ui-slider-pre b,.ui-slider-next b{width:60px;height:72px;top:-12px;}.ui-slider-pre b{left:0;}.ui-slider-next b{left:-36px;}.ui-slider-pre{background-position:3px center;border-radius:0 24px 24px 0;}.ui-slider-next{background-position:-22px center;border-radius:24px 0 0 24px;}}',
    'bcf554e6' => ';/**
 * @file 加载更多组件
 * @name Refresh
 * @desc 加载更多组件
 * @import core/zepto.ui.js
 * @importCSS loading.css
 */

(function($, undefined) {
    /**
     * @name $.ui.refresh
     * @grammar $.ui.refresh(options) ⇒ instance
     * @grammar refresh(options) ⇒ self
     * @desc **Options**
     * - \'\'ready\'\' {Function}: (必选) 当点击按钮，或者滑动达到可加载内容条件时，此方法会被调用。需要在此方法里面进行ajax内容请求，并在请求完后，调用afterDataLoading()，通知refresh组件，改变状态。
     * _ \'\'statechange\'\' {Function}: (可选) 样式改变时触发，该事件可以被阻止，阻止后可以自定义加载样式，回调参数：event(事件对象), elem(refresh按钮元素), state(状态), dir(方向)
     * - \'\'events\'\' 所有[Trigger Events](#refresh_triggerevents)中提及的事件都可以在此设置Hander, 如init: function(e){}。
     *
     * **setup方式html规则**
     * <code type="html">
     * <div>
     *     <!--如果需要在头部放更多按钮-->
     *     <div class="ui-refresh-up"></div>
     *     ......
     *     <!--如果需要在底部放更多按钮-->
     *     <div class="ui-refresh-down"></div>
     * </div>
     * </code>
     * @notice 此组件不支持render模式，只支持setup模式
     * @desc **Demo**
     * <codepreview href="../gmu/_examples/widget/refresh/refresh.html">
     * ../gmu/_examples/widget/refresh/refresh.html
     * </codepreview>
     */
    $.ui.define(\'refresh\', {
        _data: {
            ready: null,
            statechange: null
        },

        _setup: function () {
            var me = this,
                data = me._data,
                $el = me.root();

            data.$upElem = $el.find(\'.ui-refresh-up\');
            data.$downElem = $el.find(\'.ui-refresh-down\');
            $el.addClass(\'ui-refresh\');
            return me;
        },

        _init: function() {
            var me = this,
                data = me._data;
            $.each([\'up\', \'down\'], function (i, dir) {
                var $elem = data[\'$\' + dir + \'Elem\'],
                    elem = $elem.get(0);
                if ($elem.length) {
                    me.status(dir, true);    //初始设置加载状态为可用
                    if (!elem.childNodes.length || ($elem.find(\'.ui-refresh-icon\').length && $elem.find(\'.ui-refresh-label\').length)) {    //若内容为空则创建，若不满足icon和label的要求，则不做处理
                        !elem.childNodes.length && me._createBtn(dir);
                        data.refreshInfo || (data.refreshInfo = {});
                        data.refreshInfo[dir] = {
                            $icon: $elem.find(\'.ui-refresh-icon\'),
                            $label: $elem.find(\'.ui-refresh-label\'),
                            text: $elem.find(\'.ui-refresh-label\').html()
                        }
                    }
                    $elem.on(\'click\', function () {
                        if (!me.status(dir) || data._actDir) return;         //检查是否处于可用状态，同一方向上的仍在加载中，或者不同方向的还未加载完成 traceID:FEBASE-569
                        me._setStyle(dir, \'loading\');
                        me._loadingAction(dir, \'click\');
                    });
                }
            });
            return me;
        },

        _createBtn: function (dir) {
            this._data[\'$\' + dir + \'Elem\'].html(\'<span class="ui-refresh-icon"></span><span class="ui-refresh-label">加载更多</span>\');
            return this;
        },

        _setStyle: function (dir, state) {
            var me = this,
                stateChange = $.Event(\'statechange\');

            me.trigger(stateChange, [me._data[\'$\' + dir + \'Elem\'], state, dir]);
            if (stateChange.defaultPrevented) return me;

            return me._changeStyle(dir, state);
        },

        _changeStyle: function (dir, state) {
            var data = this._data,
                refreshInfo = data.refreshInfo[dir];

            switch (state) {
                case \'loaded\':
                    refreshInfo[\'$label\'].html(refreshInfo[\'text\']);
                    refreshInfo[\'$icon\'].removeClass();
                    data._actDir = \'\';
                    break;
                case \'loading\':
                    refreshInfo[\'$label\'].html(\'加载中...\');
                    refreshInfo[\'$icon\'].addClass(\'ui-loading\');
                    data._actDir = dir;
                    break;
                case \'disable\':
                    refreshInfo[\'$label\'].html(\'没有更多内容了\');
                    break;
            }
            return this;
        },

        _loadingAction: function (dir, type) {
            var me = this,
                data = me._data,
                readyFn = data.ready;

            $.isFunction(readyFn) && readyFn.call(me, dir, type);
            me.status(dir, false);
            return me;
        },

        /**
         * @name afterDataLoading
         * @grammar afterDataLoading(dir)  ⇒ instance
         * @desc - \'\'dir\'\' \\\'up\\\' 或者 \\\'down\\\'
         *
         * 当组件调用reday，在ready中通过ajax请求内容回来后，需要调用此方法，来改变refresh状态。
         */
        afterDataLoading: function (dir) {
            var me = this,
                dir = dir || me._data._actDir;
            me._setStyle(dir, \'loaded\');
            me.status(dir, true);
            return me;
        },

        /**
         * @name status
         * @grammar status(dir， status)  ⇒ instance
         * @desc 用来设置加载是否可用，分方向的。
         * - \'\'dir\'\' \\\'up\\\' 或者 \\\'down\\\'
         * - \'\'status\'\' \'\'true\'\' 或者 \'\'false\'\'。
         *
         * 当组件调用reday，在ready中通过ajax请求内容回来后，需要调用此方法，来改变refresh状态。
         */
        status: function(dir, status) {
            var data = this._data;
            return status === undefined ? data[\'_\' + dir + \'Open\'] : data[\'_\' + dir + \'Open\'] = !!status;
        },

        _setable: function (able, dir, hide) {
            var me = this,
                data = me._data,
                dirArr = dir ? [dir] : [\'up\', \'down\'];
            $.each(dirArr, function (i, dir) {
                var $elem = data[\'$\' + dir + \'Elem\'];
                //若是enable操作，直接显示，disable则根据text是否是true来确定是否隐藏
                able ? $elem.show() : (hide ?  $elem.hide() : me._setStyle(dir, \'disable\'));
                me.status(dir, able);
            });
            return me;
        },

        /**
         * @name disable
         * @grammar disable(dir)  ⇒ instance
         * @desc 如果已无类容可加载时，可以调用此方法来，禁用Refresh。
         * - \'\'dir\'\' \\\'up\\\' 或者 \\\'down\\\'
         * - \'\'hide\'\' {Boolean} 是否隐藏按钮。如果此属性为false，将只有文字变化。
         */
        disable: function (dir, hide) {
            return this._setable(false, dir, hide);
        },

        /**
         * @name enable
         * @grammar enable(dir)  ⇒ instance
         * @desc 用来启用组件。
         * - \'\'dir\'\' \\\'up\\\' 或者 \\\'down\\\'
         */
        enable: function (dir) {
            return this._setable(true, dir);
        }

        /**
         * @name Trigger Events
         * @theme event
         * @desc 组件内部触发的事件
         *
         * ^ 名称 ^ 处理函数参数 ^ 描述 ^
         * | init | event | 组件初始化的时候触发，不管是render模式还是setup模式都会触发 |
         * | statechange | event, elem, state, dir | 组件发生状态变化时会触发 |
         * | destroy | event | 组件在销毁的时候触发 |
         *
         * **组件状态说明**
         * - \'\'loaded\'\' 默认状态
         * - \'\'loading\'\' 加载中状态。
         * - \'\'disabled\'\' 禁用状态，表示无内容加载了！
         * - \'\'beforeload\'\' 在手没有松开前满足加载的条件状态。 需要引入插件才有此状态，lite，iscroll，或者iOS5。
         *
         * statechnage事件可以用来DIY按钮样式，包括各种状态。组件内部通过了一套，如果statechange事件被阻止了，组件内部的将不会执行。
         * 如:
         * <codepreview href="../gmu/_examples/webapp/refresh/refresh_iscroll_custom.html">
         * ../gmu/_examples/webapp/refresh/refresh_iscroll_custom.html
         * </codepreview>
         */

    });
})(Zepto);',
    'a32c70bd' => ';/**
 * @file 加载更多组件 － iScroll版
 * @name Refresh.iscroll
 * @short Refresh.iscroll
 * @import core/zepto.iscroll.js, widget/refresh.js
 */

(function($, undefined) {
    /**
     * @name 说明
     * @desc Refresh iscroll插件，支持拉动加载，内滚采用iscroll方式，体验更加贴近native。
     * **Demo**
     * <codepreview href="../gmu/_examples/widget/refresh/refresh_iscroll.html">
     * ../gmu/_examples/widget/refresh/refresh_iscroll.html
     * </codepreview>
     */
    $.ui.refresh.register(function () {
        return {
            pluginName: \'iscroll\',
            _init: function () {
                var me = this,
                    data = me._data,
                    $el = me.root(),
                    wrapperH = $el.height();

                me._initOrg();
                $.extend(data, {
                    useTransition: true,
                    speedScale: 1,
                    topOffset: data[\'$upElem\'] ? data[\'$upElem\'].height() : 0,
                    threshold: 0
                });

                $el.wrapAll($(\'<div class="ui-refresh-wrapper"></div>\').height(wrapperH)).css(\'height\', \'auto\');
                me._loadIscroll();
            },
            _changeStyle: function (dir, state) {
                var me = this,
                    data = me._data,
                    refreshInfo = data.refreshInfo[dir];

                me._changeStyleOrg(dir, state);
                switch (state) {
                    case \'loaded\':
                        refreshInfo[\'$icon\'].addClass(\'ui-refresh-icon\');
                        break;
                    case \'beforeload\':
                        refreshInfo[\'$label\'].html(\'松开立即加载\');
                        refreshInfo[\'$icon\'].addClass(\'ui-refresh-flip\');
                        break;
                    case \'loading\':
                        refreshInfo[\'$icon\'].removeClass().addClass(\'ui-loading\');
                        break;
                }
                return me;
            },
            _loadIscroll: function () {
                var me = this,
                    data = me._data,
                    threshold = data.threshold;

                data.iScroll = new iScroll(me.root().parent().get(0), data.iScrollOpts = $.extend({
                    useTransition: data.useTransition,
                    speedScale: data.speedScale,
                    topOffset: data.topOffset
                }, data.iScrollOpts, {
                    onScrollStart: function (e) {
                        me.trigger(\'scrollstart\', e);
                    },
                    onScrollMove: (function () {
                        var up = data.$upElem && data.$upElem.length ,
                            down = data.$downElem && data.$downElem.length;

                        return function (e) {
                            var upRefreshed = data[\'_upRefreshed\'],
                                downRefreshed = data[\'_downRefreshed\'],
                                upStatus = me.status(\'up\'),
                                downStatus = me.status(\'down\');

                            if (up && !upStatus && down && !downStatus) return;
                            if (downStatus && down && !downRefreshed && this.y < (this.maxScrollY - threshold)) {    //下边按钮，上拉加载
                                me._setMoveState(\'down\', \'beforeload\', \'pull\');
                            } else if (upStatus && up && !upRefreshed && this.y > threshold) {     //上边按钮，下拉加载
                                me._setMoveState(\'up\', \'beforeload\', \'pull\');
                                this.minScrollY = 0;
                            } else if (downStatus && downRefreshed && this.y > (this.maxScrollY + threshold)) {      //下边按钮，上拉恢复
                                me._setMoveState(\'down\', \'loaded\', \'restore\');
                            } else if (upStatus && upRefreshed && this.y < threshold) {      //上边按钮，下拉恢复
                                me._setMoveState(\'up\', \'loaded\', \'restore\');
                                this.minScrollY = -data.topOffset;
                            }
                            me.trigger(\'scrollmove\', e);
                        };
                    })(),
                    onScrollEnd: function (e) {
                        var actDir = data._actDir;
                        if (actDir) {
                            me._setStyle(actDir, \'loading\');
                            me._loadingAction(actDir, \'pull\');
                        }
                        me.trigger(\'scrollend\', e);
                    }
                }));
            },
            _setMoveState: function (dir, state, actType) {
                var me = this,
                    data = me._data;

                me._setStyle(dir, state);
                data[\'_\' + dir + \'Refreshed\'] = actType == \'pull\';
                data[\'_actDir\'] = actType == \'pull\' ? dir : \'\';

                return me;
            },
            afterDataLoading: function (dir) {
                var me = this,
                    data = me._data,
                    dir = dir || data._actDir;

                data.iScroll.refresh();
                data[\'_\' + dir + \'Refreshed\'] = false;
                return me.afterDataLoadingOrg(dir);
            }
        }
    });
})(Zepto);',
    '64d490f2' => '.ui-refresh .ui-refresh-up .ui-refresh-icon,.ui-refresh .ui-refresh-down .ui-refresh-icon{display:inline-block;width:25px;height:25px;vertical-align:middle;background:url(\'/static/common/gmu-GMU_2.0.3_TAG1/assets/widget/refresh/r-flip_61e12b8a.png\') no-repeat;-webkit-background-size:25px 25px;-webkit-transition-property:-webkit-transform;-webkit-transition-duration:400ms;-webkit-transition-timing-function:ease-in-out;}.ui-refresh .ui-refresh-down .ui-refresh-icon{-webkit-transform:rotate(180deg) translateZ(0);}.ui-refresh .ui-refresh-up .ui-refresh-flip{-webkit-transform:rotate(180deg) translateZ(0);}.ui-refresh .ui-refresh-down .ui-refresh-flip{-webkit-transform:rotate(0deg) translateZ(0);}.ui-refresh .ui-refresh-up .ui-loading,.ui-refresh .ui-refresh-down .ui-loading{-webkit-transition-duration:0ms;}',
    '429d6415' => ';/**
 * @file 按钮组件
 * @name Button
 * @desc 按钮组件
 * @import core/zepto.extend.js, core/zepto.ui.js, core/zepto.highlight.js
 * @importCSS icons.css
 */
(function ($, undefined) {
    var iconRE = /\\bui\\-icon\\-(\\w+)\\b/ig,
        iconposRE = /\\bui\\-button\\-icon\\-pos\\-(\\w+)\\b/ig;

    /**
     * @name $.ui.button
     * @grammar $.ui.button(el, options) ⇒ instance
     * @grammar $.ui.button(options) ⇒ instance
     * @grammar button(options) ⇒ self
     * @desc **el**
     *
     * css选择器，或者zepto对象
     *
     * **Options**
     * - \'\'disabled\'\' {Boolean}: (可选，默认：false)禁用与否
     * - \'\'selected\'\' {Boolean}: (可选，默认：false)选中与否
     * - \'\'label\'\' {String}: (可选)按钮文字
     * - \'\'icon\'\' {String}: (可选) 设置图标，可以是：
     *   home | delete | plus | arrow-u | arrow-d | check | gear | grid | star | arrow-r | arrow-l | minus | refresh | forward | back | alert | info | search | custom
     * - \'\'alttext\'\' {String}: (可选)当只设置icon,没有设置label的时候，组件会认为这是个只有icon的按钮，里面不会放任何文字，如果这个值设定了，icon按钮也会有文字内容，但不可见。
     * - \'\'iconpos\'\' {String}: (可选，默认：left) 设置图标位置，可以设置4种：left, top, right, bottom
     * - \'\'attributes\'\' {Object}: (可选) 在render模式下可以用来设置href， title， 等等
     * - \'\'container\'\' {Zepto}: (可选)设置父节点。
     * - \'\'events\'\' 所有[Trigger Events](#button_triggerevents)中提及的事件都可以在此设置Hander, 如init: function(e){}。
     *
     * **如果是setup模式，部分参数是直接从DOM上读取**
     * - \'\'label\'\' 读取element中文本类容
     * - \'\'icon\'\' 读取elment的data-icon属性
     * - \'\'iconpos\'\' 读取elment的data-iconpos属性
     * **比如**
     * <code>//<a id="btn" data-icon="home">按钮文字</a>
     * console.log($(\'#btn\').button(\'data\', \'label\')); // => 按钮文字
     * console.log($(\'#btn\').button(\'data\', \'icon\')); // => home
     * </code>
     *
     * **Demo**
     * <codepreview href="../gmu/_examples/widget/button/button.html">
     * ../gmu/_examples/widget/button/button.html
     * ../gmu/_examples/widget/button/button_demo.css
     * </codepreview>
     */
    $.ui.define(\'button\', {
        _data:{
            disabled: false, // true | false
            selected: false, //true | false
            label: \'\',
            alttext: \'\', //当只设置icon,没有设置label的时候，组件会认为这是个只有icon的按钮，里面不会放任何文字，如果这个值设定，icon按钮也会有文字内容，但不可见。
            type: \'button\', // button | checkbox | radio | input 在无插件的情况下只有button才能用。
            icon: \'\',//home | delete | plus | arrow-u | arrow-d | check | gear | grid | star | arrow-r | arrow-l | minus | refresh | forward | back | alert | info | search | custom
            iconpos: \'\',//left, top, right, bottom 只有在文字和图片都有的情况下才有用
            attributes: null,
            container: null
        },

        _createDefEL: function(){
            return $(\'<button>\');
        },

        _prepareBtnEL: function(mode){
            return this.root();
        },

        _prepareDom : function(mode){
            var data = this._data, $el = this.root(), key;
            if(mode==\'create\'){
                (data.label || data.alttext) && (data._textSpan = $(\'<span class="ui-button-text">\'+(data.label || data.alttext)+\'</span>\').appendTo(data._buttonElement));
                data.icon && (data._iconSpan = $(\'<span class="ui-icon ui-icon-\'+data.icon+\'"></span>\').appendTo(data._buttonElement));
            } else if(mode == \'fullsetup\') {
                data._textSpan = $(\'.ui-button-text\', data._buttonElement);
                key = data._buttonElement.hasClass(\'ui-button-icon-only\')?\'alttext\':\'label\';
                data[key] = data[key] || data._textSpan.text();
                data._iconSpan = $(\'.ui-icon\', data._buttonElement);
                data.icon = data.icon || data._iconSpan.attr(\'class\').match(iconRE) && RegExp.$1;
                data.iconpos = data.iconpos || data._buttonElement.attr(\'class\').match(iconposRE) && RegExp.$1;
            } else {
                data.label = data.label || data._buttonElement.text() || $el.val();
                data.alttext = data.alttext || $el.attr(\'data-alttext\');
                data.icon = data.icon || $el.attr(\'data-icon\');
                data.iconpos = data.iconpos || $el.attr(\'data-iconpos\');

                data._buttonElement.empty();
                data.icon && (data._iconSpan = $(\'<span class="ui-icon ui-icon-\'+data.icon+\'"></span>\').appendTo(data._buttonElement));
                (data.label || data.alttext) && (data._textSpan = $(\'<span class="ui-button-text">\'+(data.label || data.alttext)+\'</span>\').appendTo(data._buttonElement));
            }
        },

        _create: function () {
            var me = this, $el, data = me._data;

            !data.icon && !data.label && (data.label = \'按钮\');//如果既没有设置icon, 又没有设置label，则设置label为\'按钮\'

            $el = me._el || (me.root(me._createDefEL()));
            data._buttonElement = me._prepareBtnEL(\'create\');
            me._prepareDom(\'create\');
            $el.appendTo(data._container = $(data.container||\'body\'));
            data._buttonElement !== $el && data._buttonElement.insertAfter($el);
        },

        _detectType: function(){
            return \'button\';
        },

        _setup: function( mode ){
            var me = this, data = me._data;
            mode = mode?\'fullsetup\':\'setup\';
            data.type = me._detectType();
            data._buttonElement = me._prepareBtnEL(mode);
            me._prepareDom( mode );
        },

        _prepareClassName: function(){
            var me = this,
                data = me._data,
                className = \'ui-button\';

            className += data.label && data.icon ? \' ui-button-text-icon ui-button-icon-pos-\'+(data.iconpos||\'left\') :
                data.label ? \' ui-button-text-only\' : \' ui-button-icon-only\';
            className += data.disabled?\' ui-state-disable\':\'\';
            className += data.selected?\' ui-state-active\':\'\';
            return className;
        },

        _init: function(){
            var me = this,
                $el = me.root(),
                data = me._data,
                className = me._prepareClassName();

            data.attributes && $el.attr(data.attributes);
            $el.prop(\'disabled\', !!data.disabled);
            data._buttonElement.addClass(className).highlight(data.disabled?\'\':\'ui-state-hover\');

            //绑定事件
            data._buttonElement.on(\'click\', $.proxy(me._eventHandler, me));
            $.each([\'click\', \'change\'], function(){ //绑定在data中的事件， 这里只需要绑定系统事件
                data[this] && me.on(this, data[this]);
                delete data[this];
            });
        },

        /**
         * 事件管理器
         * @private
         */
        _eventHandler:function (event) {
            var data = this._data;
            if(data.disabled) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        },

        /**
         * 设置按钮状态，传入true，设置成可用，传入false设置成不可用
         * @param enable
         * @private
         */
        _setState:function (enable) {
            var data = this._data, change = data.disabled != !enable;
            if(change){
                data.disabled = !enable;
                data._buttonElement[enable?\'removeClass\':\'addClass\'](\'ui-state-disable\').highlight(enable?\'ui-state-hover\':\'\');;
                this.trigger(\'statechange\', enable);
            }
            return this;
        },

        /**
         * @desc 设置成可用状态。
         * @name enable
         * @grammar enable()  ⇒ instance
         * @example
         * //setup mode
         * $(\'#btn\').button(\'enable\');
         *
         * //render mode
         * var btn = $.ui.button();
         * btn.enable();
         */
        enable:function () {
            return this._setState(true);
        },

        /**
         * @desc 设置成不可用状态。
         * @name disable
         * @grammar disable()  ⇒ instance
         * @example
         * //setup mode
         * $(\'#btn\').button(\'disable\');
         *
         * //render mode
         * var btn = $.ui.button();
         * btn.disable();
         */
        disable:function () {
            return this._setState(false);
        },

        /**
         * @desc 切换可用和不可用状态。
         * @name toggleEnable
         * @grammar toggleEnable()  ⇒ instance
         * @example
         * //setup mode
         * $(\'#btn\').button(\'toggleEnable\');
         *
         * //render mode
         * var btn = $.ui.button();
         * btn.toggleEnable();
         */
        toggleEnable:function () {
            var data = this._data;
            return this._setState(data.disabled);
        },

        _setSelected: function(val){
            var data = this._data;
            if(data.selected != val){
                data._buttonElement[ (data.selected = val) ? \'addClass\':\'removeClass\' ](\'ui-state-active\');
                this.trigger(\'change\');
            }
            return this;
        },

        /**
         * @desc 设置成选中状态
         * @name select
         * @grammar select()  ⇒ instance
         * @example
         * //setup mode
         * $(\'#btn\').button(\'select\');
         *
         * //render mode
         * var btn = $.ui.button();
         * btn.select();
         */
        select: function(){
            return this._setSelected(true);
        },

        /**
         * @desc 设置成非选中状态
         * @name unselect
         * @grammar unselect()  ⇒ instance
         * @example
         * //setup mode
         * $(\'#btn\').button(\'unselect\');
         *
         * //render mode
         * var btn = $.ui.button();
         * btn.unselect();
         */
        unselect: function(){
            return this._setSelected(false);
        },

        /**
         * @desc 切换选中于非选中状态。
         * @name toggleSelect
         * @grammar toggleSelect()  ⇒ instance
         * @example
         * //setup mode
         * $(\'#btn\').button(\'toggleSelect\');
         *
         * //render mode
         * var btn = $.ui.button();
         * btn.toggleSelect();
         */
        toggleSelect: function(){
            return this._setSelected(!this._data.selected);
        },

        /**
         * @desc 销毁组件。
         * @name destroy
         * @grammar destroy()  ⇒ instance
         * @example
         * //setup mode
         * $(\'#btn\').button(\'destroy\');
         *
         * //render mode
         * var btn = $.ui.button();
         * btn.destroy();
         */
        destroy: function(){
            var me = this, data = this._data;
            data._buttonElement.off(\'click\', me._eventHandler).highlight();
            data._buttonElement.remove();
            me.$super(\'destroy\');
        }

        /**
         * @name Trigger Events
         * @theme event
         * @desc 组件内部触发的事件
         *
         * ^ 名称 ^ 处理函数参数 ^ 描述 ^
         * | init | event | 组件初始化的时候触发，不管是render模式还是setup模式都会触发 |
         * | click | event | 当按钮点击时触发，当按钮为disabled状态时，不会触发 |
         * | statechange | event, state(disabled的值) | 当按钮disabled状态发生变化时触发 |
         * | change | event | 当按钮类型为\'\'checkbox\'\'或者\'\'radio\'\'时，选中状态发生变化时触发 |
         * | destory | event | 组件在销毁的时候触发 |
         */
    });
})(Zepto);',
    '0f801695' => '.ui-icon{background-image:url("/static/common/gmu-GMU_2.0.3_TAG1/assets/icons-36-black_70aa1ac7.png");-webkit-background-size:776px 18px;background-size:776px 18px;width:18px;height:18px;display:inline-block;}.ui-icon.white{background-image:url("/static/common/gmu-GMU_2.0.3_TAG1/assets/icons-36-white_d7f92426.png");}.ui-icon-plus{background-position:-0 50%;}.ui-icon-minus{background-position:-36px 50%;}.ui-icon-delete{background-position:-72px 50%;}.ui-icon-arrow-r{background-position:-108px 50%;}.ui-icon-arrow-l{background-position:-144px 50%;}.ui-icon-arrow-u{background-position:-180px 50%;}.ui-icon-arrow-d{background-position:-216px 50%;}.ui-icon-check{background-position:-252px 50%;}.ui-icon-gear{background-position:-288px 50%;}.ui-icon-refresh{background-position:-324px 50%;}.ui-icon-forward{background-position:-360px 50%;}.ui-icon-back{background-position:-396px 50%;}.ui-icon-grid{background-position:-432px 50%;}.ui-icon-star{background-position:-468px 50%;}.ui-icon-alert{background-position:-504px 50%;}.ui-icon-info{background-position:-540px 50%;}.ui-icon-home{background-position:-576px 50%;}.ui-icon-search{background-position:-612px 50%;}.ui-icon-checkbox{background-position:-684px 50%;}.ui-state-active .ui-icon-checkbox{background-position:-648px 50%;}.ui-icon-checkbox-off{background-position:-684px 50%;}.ui-icon-checkbox-on{background-position:-648px 50%;}.ui-icon-radio{background-position:-756px 50%;}.ui-state-active .ui-icon-radio{background-position:-720px 50%;}.ui-icon-radio-off{background-position:-756px 50%;}.ui-icon-radio-on{background-position:-720px 50%;}',
    'e36ab8da' => '.ui-helper-hidden{position:absolute!important;clip:rect(1px 1px 1px 1px);left:-10000px;}.ui-button{cursor:pointer;-webkit-user-select:none;-webkit-box-sizing:border-box;box-sizing:border-box;-webkit-appearance:none;}.ui-button.ui-state-disable{cursor:default;}.ui-button-icon-only .ui-button-text{position:absolute!important;clip:rect(1px 1px 1px 1px);}',
    '594d43fe' => '.ui-button{display:inline-block;padding:.5em .6em;margin:2px;border:1px solid #D2D2D2;-webkit-border-radius:2px;border-radius:2px;background-color:#fff;color:#333;text-decoration:none;font-size:14px;}@media all and(min-device-width:768px) and(max-device-width:1024px){.ui-button{font-size:16px;}}.ui-button.white{background-color:rgba(0,0,0,0.4);border-color:black;}.ui-button.ui-state-disable{color:#ababab;background-color:#e6e6e6;border-color:#d2d2d2;}.ui-button.ui-state-active{color:#fff;background-color:#2d7ded;border-color:#135cbe;}.ui-button.ui-state-active.white{color:#fff;background-color:rgba(0,0,0,0.8);border-color:black;}.ui-button.ui-state-hover{background-color:#d9d9d9;border-color:#B3B3B3;}.ui-button.ui-state-hover.white{background-color:rgba(0,0,0,0.6);border-color:black;}.ui-button-text-icon{position:relative;}.ui-button.white .ui-icon{background-image:url(/static/common/gmu-GMU_2.0.3_TAG1/assets/icons-36-white_d7f92426.png);}.ui-button-text-icon .ui-icon{position:absolute;}.ui-button-icon-pos-left{padding-left:30px;}.ui-button-icon-pos-left .ui-icon{left:5px;top:50%;margin-top:-9px;}.ui-button-icon-pos-right{padding-right:30px;}.ui-button-icon-pos-right .ui-icon{right:5px;top:50%;margin-top:-9px;}.ui-button-icon-pos-top{padding-top:30px;}.ui-button-icon-pos-top .ui-icon{top:5px;left:50%;margin-left:-9px;}.ui-button-icon-pos-bottom{padding-bottom:30px;}.ui-button-icon-pos-bottom .ui-icon{bottom:5px;left:50%;margin-left:-9px;}.ui-button-icon-only{padding:.38em .5em;}',
  ),
);
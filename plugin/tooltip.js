var tooltip = {

    /* НАЧАЛО НАСТРОЕК */
    options: {
        attr_name: "tooltip", // наименование создаваемого tooltip'ого атрибута
        blank_text: "(откроется в новом окне)", // текст для ссылок с target="_blank"
        newline_entity: "", // укажите пустую строку (""), если не хотите использовать в tooltip'ах многострочность; ежели хотите, то укажите тот символ или символы, которые будут заменяться на перевод строки
        max_width: 200, // максимальная ширина tooltip'а в пикселах; обнулите это значение, если ширина должна быть нелимитирована
        delay: 100, // задержка при показе tooltip'а в миллисекундах
        skip_tags: ["link", "style"] // теги, у которых не обрабатываем атрибуты alt и title
    },
    /* КОНЕЦ НАСТРОЕК */

    t: document.createElement("DIV"),
    c: null,
    g: false,
    canvas: null,

    m: function(e){
        if (tooltip.g){
            var x = window.event ? event.clientX + (tooltip.canvas.scrollLeft || document.body.scrollLeft) : e.pageX;
            var y = window.event ? event.clientY + (tooltip.canvas.scrollTop || document.body.scrollTop) : e.pageY;
            tooltip.a(x, y);
        }
    },

    d: function(){
        tooltip.canvas = document.getElementsByTagName(document.compatMode && document.compatMode == "CSS1Compat" ? "HTML" : "BODY")[0];
        tooltip.t.setAttribute("id", "tooltip");
        document.body.appendChild(tooltip.t);
        if (tooltip.options.max_width) tooltip.t.style.maxWidth = tooltip.options.max_width + "px"; // all but ie
        var a = document.all && !window.opera ? document.all : document.getElementsByTagName("*"); // in opera 9 document.all produces type mismatch error
        var l = a.length;
        for (var i = 0; i < l; i++){

            if (!a[i] || tooltip.options.skip_tags.in_array(a[i].tagName.toLowerCase())) continue;

            var tooltip_title = a[i].getAttribute("title"); // returns form object if IE & name="title"; then IE crashes; so...
            if (tooltip_title && typeof tooltip_title != "string") tooltip_title = "";

            var tooltip_alt = a[i].getAttribute("alt");
            var tooltip_blank = a[i].getAttribute("target") && a[i].getAttribute("target") == "_blank" && tooltip.options.blank_text;
            if (tooltip_title || tooltip_blank){
                a[i].setAttribute(tooltip.options.attr_name, tooltip_blank ? (tooltip_title ? tooltip_title + " " + tooltip.options.blank_text : tooltip.options.blank_text) : tooltip_title);
                if (a[i].getAttribute(tooltip.options.attr_name)){
                    a[i].removeAttribute("title");
                    if (tooltip_alt && a[i].complete) a[i].removeAttribute("alt");
                    tooltip.l(a[i], "mouseover", tooltip.s);
                    tooltip.l(a[i], "mouseout", tooltip.h);
                }
            }else if (tooltip_alt && a[i].complete){
                a[i].setAttribute(tooltip.options.attr_name, tooltip_alt);
                if (a[i].getAttribute(tooltip.options.attr_name)){
                    a[i].removeAttribute("alt");
                    tooltip.l(a[i], "mouseover", tooltip.s);
                    tooltip.l(a[i], "mouseout", tooltip.h);
                }
            }
            if (!a[i].getAttribute(tooltip.options.attr_name) && tooltip_blank){
                //
            }
        }
        document.onmousemove = tooltip.m;
        window.onscroll = tooltip.h;
        tooltip.a(-99, -99);
    },
    
    _: function(s){
        s = s.replace(/\&/g,"&amp;");
        s = s.replace(/\</g,"&lt;");
        s = s.replace(/\>/g,"&gt;");
        return s;
    },

    s: function(e){
        if (typeof tooltip == "undefined") return;
        var d = window.event ? window.event.srcElement : e.target;
        if (!d.getAttribute(tooltip.options.attr_name)) return;
        var s = d.getAttribute(tooltip.options.attr_name);
        if (tooltip.options.newline_entity){
            var s = tooltip._(s);
            s = s.replace(eval("/" + tooltip._(tooltip.options.newline_entity) + "/g"), "<br />");
            tooltip.t.innerHTML = s;
        }else{
            if (tooltip.t.firstChild) tooltip.t.removeChild(tooltip.t.firstChild);
            tooltip.t.appendChild(document.createTextNode(s));
        }
        tooltip.c = setTimeout(function(){
            tooltip.t.style.visibility = 'visible';
        }, tooltip.options.delay);
        tooltip.g = true;
    },

    h: function(e){
        if (typeof tooltip == "undefined") return;
        tooltip.t.style.visibility = "hidden";
        if (!tooltip.options.newline_entity && tooltip.t.firstChild) tooltip.t.removeChild(tooltip.t.firstChild);
        clearTimeout(tooltip.c);
        tooltip.g = false;
        tooltip.a(-99, -99);
    },

    l: function(o, e, a){
        if (o.addEventListener) o.addEventListener(e, a, false); // was true--Opera 7b workaround!
        else if (o.attachEvent) o.attachEvent("on" + e, a);
            else return null;
    },

    a: function(x, y){
        var w_width = tooltip.canvas.clientWidth ? tooltip.canvas.clientWidth + (tooltip.canvas.scrollLeft || document.body.scrollLeft) : window.innerWidth + window.pageXOffset;
        var w_height = window.innerHeight ? window.innerHeight + window.pageYOffset : tooltip.canvas.clientHeight + (tooltip.canvas.scrollTop || document.body.scrollTop); // should be vice verca since Opera 7 is crazy!

        if (document.all && document.all.item && !window.opera) tooltip.t.style.width = tooltip.options.max_width && tooltip.t.offsetWidth > tooltip.options.max_width ? tooltip.options.max_width + "px" : "auto";
        
        var t_width = tooltip.t.offsetWidth;
        var t_height = tooltip.t.offsetHeight;

        tooltip.t.style.left = x + 12 + "px";
        tooltip.t.style.top = y + 2 + "px";
        
        if (x + t_width > w_width) tooltip.t.style.left = w_width - t_width + "px";
        if (y + t_height > w_height) tooltip.t.style.top = w_height - t_height + "px";
    }
}

Array.prototype.in_array = function(value){
    var l = this.length;
    for (var i = 0; i < l; i++)
        if (this[i] === value) return true;
    return false;
};

var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
if (root){
    if (root.addEventListener) root.addEventListener("load", tooltip.d, false);
    else if (root.attachEvent) root.attachEvent("onload", tooltip.d);
}
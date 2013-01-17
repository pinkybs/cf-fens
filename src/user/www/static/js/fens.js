
function doKeyPress(event) 
{
	if (event.keyCode == 13) {
		doSearch();
		return;
	}
}

/**
 * save search keyword log ajax request
 * @param  string keyword
 * @return void
 */
function saveSearchLog(keyword)
{   
    var url = UrlConfig.BaseUrl + '/ajaxlog';
    new Ajax.Request(url, {
        parameters : {
			hidSrhKey : keyword
        },
        onSuccess: function(response) {
        	//alert(response.responseText);
        }
    });
}

/**
 * get browser
 *
 * @return integer
 */
function getOs()
{
    //IE,return 1
    if (navigator.userAgent.indexOf("MSIE")>0) {
       return 1;
    }
    //Firefox,return 2
    if (isFirefox=navigator.userAgent.indexOf("Firefox") > 0) {
       return 2;
    }
    //Safari,return 3
    if (isSafari=navigator.userAgent.indexOf("Safari") > 0) {
       return 3;
    }
    //Camino,return 4
    if (isCamino=navigator.userAgent.indexOf("Camino") > 0) {
       return 4;
    }
    //Gecko,return 5
    if (isMozilla=navigator.userAgent.indexOf("Gecko/") > 0) {
       return 5;
    }

    return 0;
}

/**
 * Get Absolute Location Ex
 *
 * @param object element
 * @return integer
 */
function GetAbsoluteLocationEx(element)
{
    if ( arguments.length != 1 || element == null ) {
        return null;
    }
    var elmt = element;
    var offsetTop = elmt.offsetTop;
    var offsetLeft = elmt.offsetLeft;
    var offsetWidth = elmt.offsetWidth;
    var offsetHeight = elmt.offsetHeight;
    while( elmt = elmt.offsetParent ) {
        //add this judge
        if ( elmt.style.position == 'absolute' || elmt.style.position == 'relative'
            || ( elmt.style.overflow != 'visible' && elmt.style.overflow != '' ) ) {
            break;
        }
        offsetTop += elmt.offsetTop;
        offsetLeft += elmt.offsetLeft;
    }
    return { absoluteTop: offsetTop, absoluteLeft: offsetLeft,
        offsetWidth: offsetWidth, offsetHeight: offsetHeight };
}

/**
 * get Absolute Left
 *
 * @param object  e
 * @return integer
 */
function   getAbsLeft(e)
{
    var l = e.offsetLeft;
    while (e=e.offsetParent) {
          l += e.offsetLeft;
    }
    return   l;
}

/**
 * get Absolute Left
 *
 * @param object  e
 * @return integer
 */
function   getAbsTop(e)
{
    var t=e.offsetTop;
    while (e=e.offsetParent) {
        t += e.offsetTop;
    }
    return   t;
}

/**
 * show page nav
 *
 * @param integer count
 * @param integer pageindex
 * @param integer pagesize
 * @param integer pagecount
 * @return string
 */
function showPagerNav(count,pageindex,pagesize,pagecount,action)
{
    if (!pagecount) {
        pagecount = 10;
    }

    if (!action) {
        action = 'changePageAction';
    }

    if (count <= pagesize) {
        return '';
    }

    var nav = '';

    var forward = '';
    var pagerleft = '';
    var pagercurrent = '';
    var pagerright = '';
    var next = '';
    var maxpage = Math.ceil(count/pagesize);
    var classA = 'border:1px solid #8AD84D;display:block;text-decoration:none;text-align:center;';
    var classAActive = classA + 'background-color:#8AD84D;color:#FFFFFF;';
    var classLi = 'float:left;margin-right:5px;display:inline;width:20px;';
    var classUl = 'clear:both;';

    if (pageindex > 1) {
        forward += '<li style="' + classLi + '"><a style="' + classA + '" href="javascript:' + action + '(' + (pageindex - 1) + ');">&lt;&lt;</a></li>';
    }

    if (maxpage > pageindex) {
        next = '<li style="' + classLi + '"><a style="' + classA + '" href="javascript:' + action + '(' + (pageindex + 1) + ');">&gt;&gt;</a></li>';
    }

    var page = Math.ceil(pagecount/2);

    //all page count
    var i = 1;

    //left nav
    var left = 0;
    for (left = pageindex - 1; left > 0 && left > pageindex - page; left--) {
        i++;
        pagerleft = '<li style="' + classLi + '"><a style="' + classA + '" href="javascript:' + action + '(' + left + ');">' + left + '</a></li>' + pagerleft;
    }

    //current nav number
    pagercurrent = '<li style="' + classLi + '"><a style="' + classAActive + '" href="javascript:' + action + '(' + pageindex + ');" class="active">' + pageindex + '</a></li>';

    //right nva
    var right = 0;
    for (right = pageindex + 1; right <= maxpage && right < pageindex + page ; right++) {
        i++;
        pagerright = pagerright + '<li style="' + classLi + '"><a style="' + classA + '" href="javascript:' + action + '(' + right + ');">' + right + '</a></li>';
    }

    //If right side is not enough, show the page number for left until the page number number is up to 1
    if (i < pagecount && left >= 1) {
        for (; left > 0 && i < pagecount; left--,i++) {
            pagerleft = '<li style="' + classLi + '"><a style="' + classA + '" href="javascript:' + action + '(' + left + ');">' + left + '</a></li>' + pagerleft;
        }
    }

    //If left side is not enough, showthe page number for right until the page number number is up to max
    if (i < pagecount && right <= maxpage) {
        for (; right <= maxpage && i < pagecount; right++,i++) {
            pagerright = pagerright + '<li style="' + classLi + '"><a style="' + classA + '" href="javascript:' + action + '(' + right + ');">' + right + '</a></li>';
        }
    }

    nav = '<ul style="' + classUl + '">' + forward + pagerleft + pagercurrent + pagerright + next + '</ul>';

    return nav;
}

/**
 * quote String
 *
 * @param string str
 * @return string
 */
function quoteString(str)
{
    str = replaceAll(str,'\n','<br>');
    str = replaceAll(str,' ','&nbsp;');

    return str;
}

/**
 * replace the string
 *
 * @param string strOrg
 * @param string strFind
 * @param string strReplace
 * @return string
 */
function replaceAll(strOrg,strFind,strReplace)
{
     var index = 0;
     while (strOrg.indexOf(strFind,index) != -1) {
        strOrg = strOrg.replace(strFind,strReplace);
        index = strOrg.indexOf(strFind,index);
     }
     return strOrg
}

function focusFirstEle() 
{
    if ($('overWindow') != null) {
        var temp = $('overWindow').getElementsBySelector('[type="text"]');
        
        if (temp.length == 0) {
            temp = $('overWindow').getElementsByTagName('textarea');
        }
        
        if (temp.length != 0) {
            temp[0].focus();
        }
        
        modifyDialogHeight();
    }
}

/*
 * truncate money
 * 
 * return string
 */
function truncatemoney(number)
{
    var length = number.length;
    
    if ( length <= 8 ) {
        number = round(number/10000);
        number += '万';
    }
    else if ( length == 9 ) {
        number = round(number/100000000, 3);
        number += '億';
    }
    else if ( length == 10 ) {
        number = round(number/100000000, 2);
        number += '億';
    }
    else if ( length == 11 ) {
        number = round(number/100000000, 1);
        number += '億';
    }
    return number;
}

function round(v,e)
{
    var t=1;
    for( ; e>0; t*=10, e--);
    for( ; e<0; t/=10, e++);
    return Math.round(v*t)/t;
} 


/*---------------------------------------------------------------------------------------------------
    namespace.js   

/*jslint evil: true */
// 声明一个全局对象Namespace，用来注册命名空间
Namespace = new Object();

// 全局对象仅仅存在register函数，参数为名称空间全路径，如"Grandsoft.GEA"
Namespace.register = function(fullNS)
{
    // 将命名空间切成N部分, 比如Grandsoft、GEA等
    var nsArray = fullNS.split('.');
    var sEval = '';
    var sNS = '';
    for (var i = 0; i < nsArray.length; i++)
    {
        if (i != 0) sNS += '.';
        sNS += nsArray[i];
        // 依次创建构造命名空间对象（假如不存在的话）的语句
        // 比如先创建Grandsoft，然后创建Grandsoft.GEA，依次下去
        sEval += 'if (typeof(' + sNS + ') == "undefined") ' + sNS + ' = new Object();';
    }
    if (sEval != '') eval(sEval);
}






/*---------------------------------------------------------------------------------------------------
    alphafilter.js   

/*jslint evil: true */
/*--------------------------------------------------------------------------*
 *  
 *  alphafilter JavaScript Library beta5
 *  
 *  MIT-style license. 
 *  
 *  2007 Kazuma Nishihata 
 *  http://www.webcreativepark.net
 *  
 *--------------------------------------------------------------------------*/

new function(){

	if(window.addEventListener){
		window.addEventListener('load',alphafilter,false);
	}else if(window.attachEvent){
		window.attachEvent('onload',alphafilter);
	}
	
	function alphafilter(){
		if (typeof document.body.style.maxHeight == "undefined") {//for old ie
			var elements = getElementsByClassName("alphafilter"); 
			for (var i=0; i<elements.length; i++) {
				var element = elements[i];
				if(element.nodeName=="IMG"){
					var newimg           = document.createElement("b");
					for(var key in element.currentStyle){
						newimg.style[key]=element.currentStyle[key];
					}
					newimg.className     = element.className;
					newimg.style.display = "inline-block";
					newimg.style.width   = element.width;
					newimg.style.height  = element.height;
					//newimg.style.float   = element.align;
					newimg.style.cssFloat   = element.align;
					newimg.style.filter  = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src="+element.src+",sizingMethod='scale')";
					element.parentNode.replaceChild(newimg,element);
				}else{
					var anchors = element.getElementsByTagName("a");
					for (var j=0; j<anchors.length; j++) {
						var anchor = anchors[j];
						anchor.style.position="relative";
					}
					var iputs = element.getElementsByTagName("input");
					for (var j=0; j<iputs.length; j++) {
						var iput = iputs[j];
						iput.style.position="relative";
					}
					var iputs = element.getElementsByTagName("textarea");
					for (var j=0; j<iputs.length; j++) {
						var iput = iputs[j];
						iput.style.position="relative";
					}
					var iputs = element.getElementsByTagName("select");
					for (var j=0; j<iputs.length; j++) {
						var iput = iputs[j];
						iput.style.position="relative";
					}
					var  newimg = element.currentStyle.backgroundImage || element.style.backgroundImage;
					newimg.match(/^url[("']+(.*\.png)[)"']+$/i)  //'
					var newimg = RegExp.$1;
					element.style.filter ="progid:DXImageTransform.Microsoft.AlphaImageLoader(src="+newimg+",sizingMethod='image')";
					element.style.background = "none";
				}
			}
		}
	}
	
	function getElementsByClassName(className){
		var i, j, eltClass;
		var objAll = document.getElementsByTagName ? document.getElementsByTagName("*") : document.all;
		var objCN = new Array();
		for (i = 0; i < objAll.length; i++) {
			eltClass = objAll[i].className.split(/\s+/);
			for (j = 0; j < eltClass.length; j++) {
				if (eltClass[j] == className) {
					objCN.push(objAll[i]);
					break;
				}
			}
		}
		return objCN;
	}
}



/*---------------------------------------------------------------------------------------------------
	extends.js

jslint evil: true '*/ 

//去除多余空格（半角、全角）
String.prototype.trimAll = function(){
	return this.replace(/^[\s　]+|[\s　]+$/, '').replace(/[\s　]+/g, ' ');
}

//去除字符串中全部空格（半角、全角）
String.prototype.clearAllSpace = function(){
	return this.replace(/^[\s　]+|[\s　]+$/, '').replace(/[\s　]+/g, '');
}

//过滤标点符号，转为空格
String.prototype.escapePunctuation = function(){
	return this.replace(/[~`!@#\$%\^&\*\(\)\-\+=\{\[\}\]:;"'<,>\.\?\/\\\|～·！◎＃￥％…※×（）—＋＝『』【】；‘：“”，‘’。、《》？÷§・「」－￥＿＋｜｛｝＜＞]+/g, ' ');
}

//把\n转为<br/>,空格' '转为&nbsp;
String.prototype.parseBR = function(){
	return this.replace(/  /g, ' &nbsp;&nbsp;').replace(/\r\n|\r|\n/g,'<br/>');
}

//把<br/>转为\n,空格&nbsp转为' ';
String.prototype.reParseBR = function(){
	return this.replace(/<br>/g, '\n').replace(/<br\/>/g, '\n').replace(/&nbsp;/g,' ');
}

String.prototype.unEscape = function(){	
	return this.replace(/&lt;/g,"<").replace(/&gt;/g,">").replace(/&amp;/g,"&");
}

//把 " 转为&quot;,空格 ' 转为&nbsp;
String.prototype.quoteEscape = function(){
	return this.replace(/&/g,"&amp;").replace(/\"/g,"&quot;").replace(/\'/g,"&#039;");
}

//把\n转为<br/>
String.prototype.nl2br = function(){
	return this.replace(/\r\n|\r|\n/g,'<br/>');
}

String.prototype.truncate2 = function(len,sep){
	if(len==null) len=2;
	if(sep==null) sep='…';
	
	var a=0;
	
	for(var i=0;i<this.length;i++){
		if (this.charCodeAt(i)>255)
			a+=2;
		else
			a++;
		
		if(a>=len)
			return this.substr(0,i+1) + sep;
	}
	
	return this;
}

String.prototype.truncateEmoji = function(len, separator){

	var mixedStr = String(this);
	var moji_pattern = /\[([ies]:[0-9]{1,3})\]/ig;
	var matches = this.match(moji_pattern);
	matches = (matches == null) ? new Array() : matches;
	mixedStr = mixedStr.replace(moji_pattern, '�');

    len = len || 30;
    separator = Object.isUndefined(separator) ? '...' : separator;
    mixedStr = (mixedStr.length > len) ? (mixedStr.slice(0, len - separator.length) + separator) : mixedStr;

    var moji_pattern2 = /�/i;
    
    for (var i = 0; i < matches.length; i++) {
        mixedStr = mixedStr.replace(moji_pattern2, matches[i]);
    }
	
	return mixedStr;
}

String.prototype.formatToDate = function(format){
	if (!format) format = 'yyyy年MM月dd日 hh:mm';
	return new Date(this.replace(/-/g,"/")).Format(format);
}

String.prototype.toDate = function(){
	return new Date(this.replace(/-/g,"/"));
}

String.prototype.toDateLocalString = function(){
	var temp = new Date(this.replace(/-/g,"/"));
	
	var monthStr = '' + (temp.getMonth()+1); 
	if (monthStr.length<2) monthStr = '0' + monthStr; 
	
	var dateStr = '' + (temp.getDate()); 
	if (dateStr.length<2) dateStr = '0' + dateStr;
	
	return '' + temp.getFullYear() + '-' + monthStr + '-' + dateStr; 
}

String.prototype.formatToDateShort = function(format){
	if (!format) format = 'yyyy年MM月dd日';
	return new Date(this.replace(/-/g,"/")).Format(format);
}


function Hashtable() 
{ 
    this._hash  = new Object(); 
    this.add  = function(key,value){ 
         if(typeof(key)!="undefined"){ 
             if(this.contains(key)==false){ 
                 this._hash[key]=typeof(value)=="undefined"?null:value; 
                      return true; 
             }
             else { 
             	return false; 
             } 
         }
         else { 
         	return false; 
         } 
    } 
    this.remove = function(key){
    	delete this._hash[key];
    } 
    this.count = function(){
    	var i=0;
    	var obj = new this._hash.constructor();
    	for(var k in this._hash){
    		if(obj[k] !== this._hash[k])
    			i++;
    	}
    	return i;
    } 
    this.items = function(key){
    	if(this.contains(key))
    		return this._hash[key];
    	else
    		return null;
    } 
    this.contains = function(key){
    	return typeof(this._hash[key])!="undefined";
    } 
    this.clear = function(){
    	var obj = new this._hash.constructor();
    	for(var k in this._hash){
    		if(obj[k] !== this._hash[k])
    			delete this._hash[k];
    	}
    }
    this.keys = function(){
	    var keys = new Array();
	    var obj = new this._hash.constructor();
	    for(var prop in this._hash)
	    {
	     	if (obj[prop] !== this._hash[prop])
	    		keys.push(prop);
	    }
	    return keys;
    }
    this.values = function(){
	    var values = new Array();
	    var obj = new this._hash.constructor();
	    for(var prop in eval(this._hash))
	    {
	    	if (obj[prop] !== this._hash[prop])
	    		values.push(this._hash[prop]);
	    }
	    return values;   	
    }
}

//hashtable in javascript
var Collections = new Object();

Collections.Base = Class.create();
Collections.Base.prototype = {
 initialize:function()
 {
  this.count = 0 ;
  this.container = new Object();
 }
}
Collections.Hashtable = Class.create();
Collections.Hashtable.prototype = Object.extend(new Collections.Base(),
  { 
   add:function(key ,value)
   {
    if(!this.containsKey(key))
    {
     this.count++;
    }
    this.container[key] = value;
   },
   get:function(key)
   {
    if(this.containsKey(key))
    {
     return this.container[key];
    }
   else
    {
     return null;
    }
   },
   containsKey:function(key)
   {
    return (key in this.container);
   },
   containsValue:function(value)
   {
    for(var prop in this.container)
    {
     if(this.container[prop]==value)
     {
      return true;
     }
    }
    return false;
   },
   keys:function()
   {
    var keys = new Array();
    for(var prop in this.container)
    {
     keys.push(prop);
    }
    return keys;
   },
   values:function()
   {
    var values = new Array();
    for(var prop in this.container)
    {
     values.push(this.container[prop]);
    }
    return values;
   },
   remove:function()
   {
    if(this.containsKey(key))
    {
     delete this.container[key];
     this.count--;
    }
   }
    
  }

)

function StringBuilder(value) {
    this.strings = new Array("");
    this.append(value);
}

// Appends the given value to the end of this instance.
StringBuilder.prototype.append = function (value) {
    if (value != null) {
        this.strings.push(value);
    }
    return this;
}

// Clears the string buffer
StringBuilder.prototype.clear = function () {
    this.strings.length = 1;
    return this;
}

// Converts this instance to a String.
StringBuilder.prototype.toString = function () {
    return this.strings.join("");
}

StringBuilder.prototype.isEmpty = function () {
    return this.strings.length==0;
}

function DateSelector(ddlYear, ddlMonth, ddlDay)
{
    this.ddlYear = ddlYear;
    this.ddlMonth = ddlMonth;
    this.ddlDay = ddlDay;
    this.InitYearSelect();
    this.InitMonthSelect();
}

// 增加一个最大年份的属性
DateSelector.prototype.MinYear = 1970;

// 增加一个最大年份的属性
DateSelector.prototype.MaxYear = (new Date()).getFullYear();

// 初始化年份
DateSelector.prototype.InitYearSelect = function()
{
    // 循环添加OPION元素到年份select对象中
    for(var i = this.MaxYear; i >= this.MinYear; i--)
    {
        // 新建一个OPTION对象
        var op = window.document.createElement("OPTION");
        
        // 设置OPTION对象的值
        op.value = i;
        
        // 设置OPTION对象的内容
        op.innerHTML = i;
        
        // 添加到年份select对象
        this.ddlYear.appendChild(op);
    }
}

// 初始化月份
DateSelector.prototype.InitMonthSelect = function()
{
    // 循环添加OPION元素到月份select对象中
    for(var i = 1; i < 13; i++)
    {
        // 新建一个OPTION对象
        var op = window.document.createElement("OPTION");
        
        // 设置OPTION对象的值
        op.value = i;
        
        // 设置OPTION对象的内容
        op.innerHTML = i;
        
        // 添加到月份select对象
        this.ddlMonth.appendChild(op);
    }
}

Date.prototype.Format = function(fmt) 
{ //author: meizz 
	var o = { 
		"M+" : this.getMonth()+1, //月份 
		"d+" : this.getDate(), //日 
		"h+" : this.getHours(), //小时 
		"m+" : this.getMinutes(), //分 
		"s+" : this.getSeconds(), //秒 
		"q+" : Math.floor((this.getMonth()+3)/3), //季度 
		"S" : this.getMilliseconds() //毫秒 
	}; 
	if(/(y+)/.test(fmt)) 
	fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
	for(var k in o) 
	if(new RegExp("("+ k +")").test(fmt)) 
	fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length))); 
	return fmt; 
} 

// 根据年份与月份获取当月的天数
DateSelector.DaysInMonth = function(year, month)
{
    var date = new Date(year, month, 0);
    return date.getDate();
}

Date.prototype.dateDiff  =  function(interval,o)
{
	//判断o是否为日期对象
	if(o&&o.constructor==Date){
		//判断是否interval是否是字符串对象
		if  (interval&&interval.constructor==String){
		
			var  _start=  this.getTime();
			var  _end=  o.getTime();
			
			var  number=  _end  -  _start;
			
			var  iOut  =  -1;
			      
			switch  (interval.charAt(0)){
				case  'y':case  'Y'://year
				iOut  =    o.getFullYear()  -  this.getFullYear();
				break;
				case  'm':case  'M'://month
				iOut  =  (o.getFullYear()  -  this.getFullYear())  *  12  +  (o.getMonth()-this.getMonth());
				break;
				case  'q':case  'Q'://quarter
				iOut  =  ((o.getFullYear()  -  this.getFullYear())  *  12  +  (o.getMonth()-this.getMonth()))/3;
				break;
				case  'd':case  'D'://day
				iOut  =  parseInt(number  /  86400000)  ;
				break;
				case  'w':case  'W'://week
				iOut  =  parseInt(number  /  86400000/7)  ;
				break;
				case  'h':case  'H'://hour
				iOut  =  parseInt(number  /  3600000  )  ;
				break;
				case  'n':case  'N'://minute
				iOut  =  parseInt(number  /  60000  )  ;
				break;
				case  's':  case  'S'://second
				iOut  =  parseInt(number  /  1000  )  ;
				break;
				case  't':case  'T'://microsecond
				iOut  =  parseInt(number);
				break;
				default:
				iOut  =  -1;
			}
			
			return  iOut;
		}
	}	
	return  -1;
}

Date.prototype.dateAdd  =  function(interval,number)
{
	var  date  =  this;

    switch(interval)
    {
        case  "y"  :  
			date.setFullYear(date.getFullYear()+number);
			return  date;
        case  "q"  :  
			date.setMonth(date.getMonth()+number*3);
			return  date;
        case  "m"  :  
			date.setMonth(date.getMonth()+number);
			return  date;
        case  "w"  :  
			date.setDate(date.getDate()+number*7);
			return  date;
        case  "d"  :  
			date.setDate(date.getDate()+number);
			return  date;
        case  "h"  :  
			date.setHours(date.getHours()+number);
			return  date;
		case  "m"  :  
			date.setMinutes(date.getMinutes()+number);
			return  date;
		case  "s"  :  
			date.setSeconds(date.getSeconds()+number);
			return  date;
        default  :  
			date.setDate(d.getDate()+number);
			return  date;
    }
}






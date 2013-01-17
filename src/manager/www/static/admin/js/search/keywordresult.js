/**
 * site list(static/admin/js/search/keywordresult.js)
 * site list 
 * 
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/26    zhangxin   
 */
 
/**
 *  page load function
 *  register funcion and init page
 */
Event.observe(window, 'load', function() { 
	Event.observe('btnFixedSearch', 'click', searchFixed);
	Event.observe('btnFixedClear', 'click', clearFixed);
	Event.observe('btnSearch', 'click', search);
	Event.observe('btnClear', 'click', clear);
});

//define page size
var CONST_DEFAULT_PAGE_SIZE = 30;

/**
 * search fixed site page by filter
 * @param null
 * @return void
 */
function searchFixed()
{
	if ('' == $F('txtFixedKeyword').trimAll()) {
		$('txtFixedKeyword').value = '';
		alert('キーワードを入力してください。')
		return false;
	}
	//show fixed page
	$('hidSrhFixedKeyword').value = $F('txtFixedKeyword').trimAll();	
	changePageAction2('1');
	//search page 
	clear();
}

/**
 * clear fixed search filter
 * @param null
 * @return void
 */
function clearFixed()
{
	window.location.reload();
}


/**
 * search site page by filter
 * @param null
 * @return void
 */
function search()
{
	if ('' == $F('hidSrhFixedKeyword').trimAll()) {
		return false;
	}
	$('hidSrhKeyword').value = $F('txtKeyword').trimAll();	
	changePageAction('1');
}

/**
 * clear search filter
 * @param null
 * @return void
 */
function clear()
{
	$('txtKeyword').value = '';
	search();
}

/**
 * change page ajax request(fixed)
 * @param  integer page
 * @return void
 */
function changePageAction2(page)
{   
    //ajax show list request
    $('pageIndex2').value = page;
    var url = UrlConfig.BaseUrl + '/ajax/search/listfixedpage';
    new Ajax.Request(url, {
        parameters : {
            pageIndex : $F('pageIndex2'),
            pageSize : CONST_DEFAULT_PAGE_SIZE,            
			srhFixedKeyword : $F('hidSrhFixedKeyword')
        },
        timeout:30000,
        onTimeout: function() {
            $('divFixedList').update('通信の問題で処理を中断しました。しばらくたってからもう一度お試し下さい。');
        },
        onCreate : function() {
		    $('divFixedList').update('読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/admin/img/photoeffect/loading.gif">');
		    $('fens_admin').scrollTo();
        },
        onSuccess: function(response) {
	        try {    
		        if (response.responseText != '' && response.responseText != 'false') {      
		            var responseObject = response.responseText.evalJSON(); 
		            var cntTotal = 0;
		            //show response array data to list table
		            if (responseObject && responseObject.info && responseObject.info.length > 0) {            
		            	var html = showInfoFixed(responseObject.info);
		            	var nav = showPagerNav(responseObject.count, Number($F('pageIndex2')), CONST_DEFAULT_PAGE_SIZE, false, 'changePageAction2');
		            	cntTotal = responseObject.count;
		            	$('divFixedList').update(nav + html + nav);
		            }
		            else {
		            	//for after edit , can not find the page' record when the page has only one record 
		            	if (parseInt($F('pageIndex2')) > 1) {
		            		changePageAction2(parseInt($F('pageIndex2')) - 1);
		            	}
		            	else {
		            		$('divFixedList').update('使用したキーワードでは検索結果が見つかりませんでした。');
		            	}
		            }
		            $('lblFixedCount').update(cntTotal);
		            $('lblFixedKeyword').update($F('hidSrhFixedKeyword'));
		        }
		    } catch (e) {
		        //alert(e);
		    }
        }
    });         
}

/**
 * show fixed page table
 * @param  object array
 * @return string
 */
function showInfoFixed(array)
{
    //concat html tags to array data
    var html = '';
    
    html += '<table width="100%" cellpadding="0" cellspacing="0" border="0" id="dataGrid">'
    		+ '<thead>'
            + '<tr class="head">'
            + '<th width="5%">順位</th>'
            + '<th width="35%">ページURL</th>'
            + '<th width="40%">タイトル</th>'
            + '<th width="10%">入替</th>'
            + '<th>外す</th>'           
            + '</tr>'
            + '</thead>'
            + '<tbody>';

    //for each row data
    for (var i = 0 ; i < array.length ; i++) {        
        var cssClass = 'a';
        if (1 == i % 2) {
        	cssClass = 'b';
        }
        
        var linkUp = '<a href="javascript:void(0);" onclick="updownPage(1,' + array[i].id + ');return false;">↑</a>';
        var linkDown = '<a href="javascript:void(0);" onclick="updownPage(2,' + array[i].id + ');return false;">↓</a>';
        var linkRemove = '<a href="javascript:void(0);" onclick="removePage(' + array[i].id + ');return false;">外す</a>';
        
        var intNo = (parseInt($F('pageIndex2'))-1)*CONST_DEFAULT_PAGE_SIZE + i;
        html += '<tr class="' + cssClass + '">'
              + '    <td>' + (intNo + 1) + '位</td>'
              + '    <td>' + array[i].page_url + '</td>'
              + '    <td>' + array[i].page_title.escapeHTML() + '</td>'
              + '    <td>' + linkUp + '　' + linkDown + '</td>'
              + '    <td>' + linkRemove + '</td>'                       
              + '</tr>';
    }
    
    html += '</tbody>'
            + '</table>';
    
    return html;
}

/**
 * change page ajax request
 * @param  integer page
 * @return void
 */
function changePageAction(page)
{   
    //ajax show list request
    $('pageIndex1').value = page;
    var url = UrlConfig.BaseUrl + '/ajax/search/listpage';
    new Ajax.Request(url, {
        parameters : {
            pageIndex : $F('pageIndex1'),
            pageSize : CONST_DEFAULT_PAGE_SIZE,            
            srhFixedKeyword : $F('hidSrhFixedKeyword'),
			srhKeyword : $F('hidSrhKeyword')
        },
		timeout:30000,
        onTimeout: function() {
            $('divList').update('通信の問題で処理を中断しました。しばらくたってからもう一度お試し下さい。');
        },
        onCreate : function() {
		    $('divList').update('読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/admin/img/photoeffect/loading.gif">');
		    //$('fens_admin').scrollTo();
        },
        onSuccess: function(response) {
	        try {    
		        if (response.responseText != '' && response.responseText != 'false') {      
		            var responseObject = response.responseText.evalJSON(); 
		            var cntTotal = 0;
		            var cntCurrent = 0;
		            //show response array data to list table
		            if (responseObject && responseObject.info && responseObject.info.length > 0) {            
		            	var html = showInfo(responseObject.info);
		            	var nav = showPagerNav(responseObject.count, Number($F('pageIndex1')), CONST_DEFAULT_PAGE_SIZE);
		            	cntTotal = responseObject.count;
		            	$('divList').update(nav + html + nav);
		            }
		            else {		            	
	            		$('divList').update('使用したキーワードでは検索結果が見つかりませんでした。');
		            }
		            //$('lblTotalCount').update(cntTotal);
		            $('lblKeyword').update($F('hidSrhKeyword'));
		        }
		    } catch (e) {
		        //alert(e);
		    }
        }
    });         
}

/**
 * show site table
 * @param  object array
 * @return string
 */
function showInfo(array)
{
    //concat html tags to array data
    var html = '';
    
    html += '<table width="100%" cellpadding="0" cellspacing="0" border="0" id="dataGrid" style="clear: both;">'
    		+ '<thead>'
            + '<tr class="head">'
            + '<th width="5%">ID</th>'
            + '<th width="35%">ページURL</th>'
            + '<th width="40%">タイトル</th>'
            + '<th width="10%">最終クローリング日時</th>'
            + '<th>選択</th>'
            + '</tr>'
            + '</thead>'
            + '<tbody>';

    //for each row data
    for (var i = 0 ; i < array.length ; i++) {        
        var cssClass = 'a';
        if (1 == i % 2) {
        	cssClass = 'b';
        }
        
        var linkAdd = '<a href="javascript:void(0);" onclick="addPage(' + array[i].id + ');return false;">選択</a>'; 
        if (1 == array[i].is_fixed) {
        	linkAdd = '選択済'; 
        }        
        
        var strContent = array[i].title.escapeHTML().replace($F('hidSrhFixedKeyword').escapeHTML(), '<span style="background:#ffff99;">' + $F('hidSrhFixedKeyword').escapeHTML() + '</span>');
        html += '<tr class="' + cssClass + '">'
              + '    <td>' + array[i].id + '</td>'
              + '    <td>' + array[i].url + '</td>'
              + '    <td>' + strContent + '</td>'
              + '    <td>' + (array[i].last_update_time == null ? '' : array[i].last_update_time) + '</td>'
              + '    <td>' + linkAdd + '</td>'                       
              + '</tr>';
    }
    
    html += '</tbody>'
            + '</table>';
    
    return html;
}

/**
 * delete a site 
 * @param  integer id
 * @return void
 */
function removePage(id)
{
	if (!window.confirm('このサイトを検索上位から外します。よろしければOKボタンを押して下さい。')) {
		return false;
	}
	
	//ajax delete a select site
    var url = UrlConfig.BaseUrl + '/ajax/search/removepage';
    new Ajax.Request(url, {
        method: 'post',
        parameters : {
            id : id
        },               
        onTimeout: function() {
            $('divFixedList').update('通信の問題で処理を中断しました。しばらくたってからもう一度お試し下さい。');
        },
        onCreate : function() {
		    //var html = '読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/admin/img/photoeffect/loading.gif">';
		    //$('divList').update(html);		    
		},
        onSuccess: function(response) { 	   
	        if (response.responseText == 'true') {      
	            changePageAction2($F('pageIndex2'));
	            changePageAction($F('pageIndex1'));
	        }
	        else {
	        	alert('外す失敗しました。しばらくたってからもう一度お試し下さい。');
	        }
		}
    });
}

/**
 * add a page to fixed 
 * @param  integer id
 * @return void
 */
function addPage(id)
{
	if (!window.confirm('このサイトを検索上位に固定します。よろしければOKボタンを押して下さい。')) {
		return false;
	}
	
	//ajax delete a select site
    var url = UrlConfig.BaseUrl + '/ajax/search/addpage';
    new Ajax.Request(url, {
        method: 'post',
        parameters : {
            id : id,
            fixedKeyword : $F('hidSrhFixedKeyword')
        },               
        onTimeout: function() {
            $('divFixedList').update('通信の問題で処理を中断しました。しばらくたってからもう一度お試し下さい。');
        },
        onCreate : function() {
		    //var html = '読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/admin/img/photoeffect/loading.gif">';
		    //$('divFixedList').update(html);		    
		},
        onSuccess: function(response) { 	   
	        if (response.responseText == 'true') {      
	            changePageAction2($F('pageIndex2'));
	            changePageAction($F('pageIndex1'));	            
	        }
	        else {
	        	alert('固定失敗しました。このページは既に上位結果になっている。');
	        }	        
		}
    });
}

/**
 * up/down a fixed page by one level
 * @param  integer upOrDown 1-UP 2-DOWN
 * @param  integer id
 * @return void
 */
function updownPage(upOrDown, id)
{
	//ajax up a select fixeed page sort
    var url = UrlConfig.BaseUrl + '/ajax/search/sortfixedpage';
    new Ajax.Request(url, {
        method: 'post',
        parameters : {
        	op : upOrDown,
            id : id,
            fixedKeyword : $F('hidSrhFixedKeyword')
        },               
        onTimeout: function() {
            $('divFixedList').update('通信の問題で処理を中断しました。しばらくたってからもう一度お試し下さい。');
        },
        onCreate : function() {
		    var html = '読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/admin/img/photoeffect/loading.gif">';
		    $('divFixedList').update(html);		    
		},
        onSuccess: function(response) { 	   
	        if (response.responseText == 'true') {      
	            changePageAction2($F('pageIndex2'));
	        }
	        else {
	        	$('divFixedList').update('順番設定失敗しました。しばらくたってからもう一度お試し下さい。');
	        }
		}
    });
}
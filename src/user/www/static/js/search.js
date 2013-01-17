/**
 * site search(static/js/search.js)
 * site search 
 * 
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/04/07    zhangxin   
 */
 
/**
 *  page load function
 *  register funcion and init page
 */
Event.observe(window, 'load', function() { 
	Event.observe('searchBtn', 'click', doSearch);
	Event.observe('searchText', 'keypress', doKeyPress);
    //changePageAction($F('pageIndex'));
});

//define default page size
var CONST_DEFAULT_PAGE_SIZE = 10;

/**
 * search site by filter
 * @param null
 * @return void
 */
function doSearch()
{
	if ('' == $F('searchText').trimAll()) {
    	return;
    }
	$('hidSrhKey').value = $F('searchText').trimAll();
	$('pageIndex').value = 1;
	//changePageAction('1');
	$('searchForm').submit();
	saveSearchLog($F('hidSrhKey'));
	return false;
}

/**
 * change page ajax request
 * @param  integer page
 * @return void
 */
function changePageAction(page)
{   
    //ajax show list request
    $('pageIndex').value = page;
    var url = UrlConfig.BaseUrl + '/ajaxsearch';
    new Ajax.Request(url, {
        parameters : {
            pageIndex : $F('pageIndex'),
            pageSize : CONST_DEFAULT_PAGE_SIZE,
			hidSrhKey : $F('hidSrhKey')
        },
        timeout:30000,
        onTimeout: function() {
            $('divList').update('通信の問題で処理を中断しました。しばらくたってからもう一度お試し下さい。');
        },
        onCreate : getDataFromServer,
        onSuccess: renderResults
    });         
}

/**
 * change page show when ajax is request -ing
 * @param  null
 * @return void
 */
function getDataFromServer()
{
    var html = '読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/img/loading.gif">';
    $('divList').update(html);
    $('fens_admin').scrollTo();
} 
       
/**
 * response from site view ajax request
 * @param  object response
 * @return void
 */
function renderResults(response)
{ 
    try {    
        if (response.responseText != '' && response.responseText != 'false') {      
            var responseObject = response.responseText.evalJSON(); 
            var cntTotal = 0;
            var cntCurrent = 0;
            //show response array data to list table
            if (responseObject && responseObject.info && responseObject.info.length > 0) {            
            	var html = showInfo(responseObject.info);
            	var nav = showPagerNav(responseObject.count, Number($F('pageIndex')), CONST_DEFAULT_PAGE_SIZE);
            	cntTotal = responseObject.count;
            	cntCurrent = CONST_DEFAULT_PAGE_SIZE;
            	$('divList').update(html + nav);
            }
            else {
            	$('divList').update('キーワードに該当するサイトが見つかりませんでした。');
            }
            $('lblTotalCount').update(cntTotal);
            var numstart = 0;
            var numend = 0;
           	if (cntTotal != 0) {
           		numstart = (parseInt($F('pageIndex')) - 1) * CONST_DEFAULT_PAGE_SIZE + 1;
           		numend = (numstart + CONST_DEFAULT_PAGE_SIZE - 1) > parseInt(cntTotal) ? cntTotal : (numstart + CONST_DEFAULT_PAGE_SIZE - 1);
           	}
           	$('lblStartNum').update(numstart);
           	$('lblEndNum').update(numend);
           	$('lblKeyword').update($F('hidSrhKey'));
        }
    } catch (e) {
        //alert(e);
    }
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
    
    html += '<table width="100%" cellpadding="0" cellspacing="0" border="0" id="dataGrid">'
            + '<tbody>';

    //for each row data
    for (var i = 0 ; i < array.length ; i++) {        
        var cssClass = 'a';
        if (1 == i % 2) {
        	cssClass = 'b';
        }
        
        var re = new RegExp($F('hidSrhKey').escapeHTML(), "ig")
        var strContent = array[i].summary.escapeHTML().replace(re, '<span style="background:#ffff99;">' + $F('hidSrhKey').escapeHTML() + '</span>');
        html += '<tr class="' + cssClass + '">'
              + '    <td>' + '<a target="_blank" href="' + array[i].url + '">' + array[i].title.truncate2(60, '…').escapeHTML() + '</a><br />' 
                           + strContent + '<br />' 
            			   + '<span class="colorgreen">' + array[i].url.truncate2(60, '…') + '</span><br /><br />' 
              + '    </td>'
              + '</tr>';
    }
    
    html += '</tbody>'
            + '</table>';
    
    return html;
}
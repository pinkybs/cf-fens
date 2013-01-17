/**
 * site list(static/admin/js/manager/listsite.js)
 * site list 
 * 
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/25    zhangxin   
 */
 
/**
 *  page load function
 *  register funcion and init page
 */
Event.observe(window, 'load', function() { 
	Event.observe('btnNew', 'click', addSite);
	Event.observe('btnSearch', 'click', search);
	Event.observe('btnClear', 'click', clear);
    changePageAction($F('pageIndex'));
});

//define default page size
var CONST_DEFAULT_PAGE_SIZE = 30;

/**
 * search site by filter
 * @param null
 * @return void
 */
function search()
{
	$('hidSrhName').value = $F('txtSrhName');
	$('hidSrhUrl').value = $F('txtSrhUrl');
	$('hidSrhCate').value = $F('selSrhCate');
	$('hidSrhArea').value = $F('selSrhArea');
	changePageAction('1');
}

/**
 * clear search filter
 * @param null
 * @return void
 */
function clear()
{
	$('txtSrhName').value = '';
	$('txtSrhUrl').value = '';
	$('selSrhCate').value = '';
	$('selSrhArea').value = '';
	search();
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
    var url = UrlConfig.BaseUrl + '/ajax/manage/listsite';
    new Ajax.Request(url, {
        parameters : {
            pageIndex : $F('pageIndex'),
            pageSize : CONST_DEFAULT_PAGE_SIZE,            
            srhCate : $F('hidSrhCate'),
            srhArea : $F('hidSrhArea'),
            srhName : $F('hidSrhName'),
			srhUrl : $F('hidSrhUrl')
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
    var html = '読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/admin/img/photoeffect/loading.gif">';
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
            	$('divList').update(nav + html + nav);
            }
            else {
            	//for after edit , can not find the page' record when the page has only one record 
            	if (parseInt($F('pageIndex')) > 1) {
            		changePageAction(parseInt($F('pageIndex')) - 1);
            	}
            	else {
            		$('divList').update('まだ何もありません。');
            	}
            }
            $('lblTotalCount').update(cntTotal);
            $('lblPageCount').update(cntCurrent>cntTotal ? cntTotal : cntCurrent);
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
    
    html += '<table width="100%" cellpadding="0" cellspacing="0" border="0" id="dataGrid" style="clear: both;">'
    		+ '<thead>'
            + '<tr class="head">'
            + '<th width="5%">ID</th>'
            + '<th width="15%">サイト名</th>'
            + '<th width="20%">サイトURL / Indexファイル名</th>'
            + '<th width="10%">エリア</th>'
            + '<th width="10%">カテゴリー</th>'
            + '<th width="15%">最終クローリング日時</th>'
            + '<th width="6%">ページ数</th>'
            + '<th>編集　削除</th>'
            + '</tr>'
            + '</thead>'
            + '<tbody>';

    //for each row data
    for (var i = 0 ; i < array.length ; i++) {        
        var cssClass = 'a';
        if (1 == i % 2) {
        	cssClass = 'b';
        }
        
        var linkEdit = '<a href="javascript:void(0);" onclick="editSite(' + array[i].id + ');return false;">編集</a>';
        var linkDel = '<a href="javascript:void(0);" onclick="delSite(' + array[i].id + ');return false;">削除</a>';
        
        html += '<tr class="' + cssClass + '">'
              + '    <td>' + array[i].id + '</td>'
              + '    <td>' + array[i].name.escapeHTML() + '</td>'
              + '    <td>' + array[i].url + array[i].toppage + '</td>'
              + '    <td>' + array[i].area_name.escapeHTML() + '</td>'
              + '    <td>' + array[i].cate_name.escapeHTML() + '</td>'
              + '    <td>' + (array[i].last_crawling_time == null ? '' : array[i].last_crawling_time) + '</td>'
              + '    <td style="text-align:center">' + array[i].page_count + '</td>'
              + '    <td>' + linkEdit + '　' + linkDel + '</td>'                       
              + '</tr>';
    }
    
    html += '</tbody>'
            + '</table>';
    
    return html;
}

function addSite()
{
	$('frmBack').action = UrlConfig.BaseUrl + '/manage/addsite';
	$('frmBack').submit();
}

function editSite(id)
{
	$('frmBack').action = UrlConfig.BaseUrl + '/manage/editsite/id/' + id;
	$('frmBack').submit();
}

/**
 * delete a site 
 * @param  integer sid
 * @return void
 */
function delSite(sid)
{
	if (!window.confirm('削除の確認\nサイトを削除しますか？')) {
		return false;
	}
	
	//ajax delete a select site
    var url = UrlConfig.BaseUrl + '/ajax/manage/delsite';
    new Ajax.Request(url, {
        method: 'post',
        parameters : {
            id : sid
        },         
        timeout:30000,      
        onTimeout: function() {
            $('divList').update('通信の問題で処理を中断しました。しばらくたってからもう一度お試し下さい。');
        },
        onCreate : function() {
		    var html = '読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/admin/img/photoeffect/loading.gif">';
		    $('divList').update(html);		    
		},
        onSuccess: function(response) { 	   
	        if (response.responseText == 'true') {      
	            changePageAction('1');
	        }
	        else {
	        	$('divList').update('削除失敗しました。しばらくたってからもう一度お試し下さい。');
	        }
		}
    });
}
/**
 * site category list(static/admin/js/manager/listcategory.js)
 * site category list 
 * 
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/25    zhangxin   
 */
 
/**
 *  page load function
 *  register funcion and init page
 */
Event.observe(window, 'load', function() { 
	Event.observe('btnNew', 'click', addCate);
    changePageAction($F('pageIndex'));
});

//define default page size
var CONST_DEFAULT_PAGE_SIZE = 30;

/**
 * change page ajax request
 * @param  integer page
 * @return void
 */
function changePageAction(page)
{   
    //ajax show list request
    $('pageIndex').value = page;
    var url = UrlConfig.BaseUrl + '/ajax/manage/listcategory';
    new Ajax.Request(url, {
        parameters : {
            pageIndex : $F('pageIndex'),
            pageSize : CONST_DEFAULT_PAGE_SIZE
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
 * response from site category view ajax request
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
            	$('divList').update('まだ何もありません。');
            }
            $('lblTotalCount').update(cntTotal);
            $('lblPageCount').update(cntCurrent>cntTotal ? cntTotal : cntCurrent);
        }
    } catch (e) {
        //alert(e);
    }
}

/**
 * show site category table
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
            + '<th width="10%">ID</th>'
            + '<th width="40%">カテゴリー名</th>'
            + '<th width="20%">所属サイト数</th>'
            + '<th width="10%">順番</th>'
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
        
        var linkUp = '<a href="javascript:void(0);" onclick="updownCate(1,' + array[i].id + ');return false;">↑</a>';
        var linkDown = '<a href="javascript:void(0);" onclick="updownCate(2,' + array[i].id + ');return false;">↓</a>';
        var linkEdit = '<a href="javascript:void(0);" onclick="editCate(' + array[i].id + ');return false;">編集</a>';
        var linkDel = '<a href="javascript:void(0);" onclick="delCate(' + array[i].id + ');return false;">削除</a>';
        
        html += '<tr class="' + cssClass + '">'
              + '    <td>' + array[i].id + '</td>'
              + '    <td>' + array[i].name.escapeHTML() + '</td>'
              + '    <td style="text-align:center">' + array[i].site_count + '</td>'
              + '    <td>' + linkUp　+ '　' + linkDown + '</td>'
              + '    <td>' + linkEdit + '　' + linkDel + '</td>'                       
              + '</tr>';
    }
    
    html += '</tbody>'
            + '</table>';
    
    return html;
}

/**
 * up a category by one level
 * @param  integer upOrDown 1-UP 2-DOWN
 * @param  integer cid
 * @return void
 */
function updownCate(upOrDown, cid)
{
	//ajax up a select category sort
    var url = UrlConfig.BaseUrl + '/ajax/manage/sortcategory';
    new Ajax.Request(url, {
        method: 'post',
        parameters : {
        	op : upOrDown,
            id : cid
        },               
        onTimeout: function() {
            $('divList').update('通信の問題で処理を中断しました。しばらくたってからもう一度お試し下さい。');
        },
        onCreate : function() {
		    var html = '読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/admin/img/photoeffect/loading.gif">';
		    $('divList').update(html);		    
		},
        onSuccess: function(response) { 	   
	        if (response.responseText == 'true') {      
	            changePageAction($F('pageIndex'));
	        }
	        else {
	        	$('divList').update('順番設定失敗しました。しばらくたってからもう一度お試し下さい。');
	        }
		}
    });
}

function addCate()
{
	$('frmBack').action = UrlConfig.BaseUrl + '/manage/addcategory';
	$('frmBack').submit();
}

function editCate(cid)
{
	$('frmBack').action = UrlConfig.BaseUrl + '/manage/editcategory/id/' + cid;
	$('frmBack').submit();
}

/**
 * delete a category 
 * @param  integer cid
 * @return void
 */
function delCate(cid)
{
	if (!window.confirm('このカテゴリーを削除します。よろしければOKボタンを押して下さい。')) {
		return false;
	}
	
	//ajax delete a select category
    var url = UrlConfig.BaseUrl + '/ajax/manage/delcategory';
    new Ajax.Request(url, {
        method: 'post',
        parameters : {
            id : cid
        },               
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
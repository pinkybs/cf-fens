/**
 * keyword search rank list(static/admin/js/search/keywordrank.js)
 * keyword search rank list 
 * 
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/27    zhangxin   
 */
 
/**
 *  page load function
 *  register funcion and init page
 */
Event.observe(window, 'load', function() { 	
    changePageAction('1');
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
    var url = UrlConfig.BaseUrl + '/ajax/search/keywordrank';
    new Ajax.Request(url, {
        parameters : {
            pageIndex : $F('pageIndex'),
            pageSize : CONST_DEFAULT_PAGE_SIZE
        },
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
 * response from search keyword rank view ajax request
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
            + '<th>キーワード　</th>'
            + '<th>入力数</th>'
            + '</tr>'
            + '</thead>'
            + '<tbody>';

    //for each row data
    for (var i = 0 ; i < array.length ; i++) {        
        var cssClass = 'a';
        if (1 == i % 2) {
        	cssClass = 'b';
        }
                
        html += '<tr class="' + cssClass + '">'
              + '    <td>' + array[i].search_keyword.escapeHTML() + '</td>'
              + '    <td>' + array[i].search_count + '</td>'                      
              + '</tr>';
    }
    
    html += '</tbody>'
            + '</table>';
    
    return html;
}
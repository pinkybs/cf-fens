/**
 * site edit(static/admin/js/manager/editsite.js)
 * site edit 
 * 
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/25    zhangxin   
 */
 
/**
 *  page load function
 *  register funcion and init page
 */
Event.observe(window, 'load', function() { 
	Event.observe('btnSubmit', 'click', editSite);
});

var _valid = new Validation('frmEdit', {immediate:true,useTitles:true});

/**
 * edit site 
 * @return void
 */
function editSite()
{
	$('errMsg').hide();
	if (!_valid.validate()) {
		$('errMsg').update('入力内容に誤りがあります。');
		$('errMsg').show();
		$('completeMsg').hide();
    	return;
    }
    
    $('completeMsg').show();
	//ajax edit site 
    var frmSubmit = $('frmEdit');
    frmSubmit.action = UrlConfig.BaseUrl + '/ajax/manage/editsite';
    frmSubmit.request({    
        onTimeout: function() {
            $('completeMsg').update('通信の問題で処理を中断しました。しばらくたってからもう一度お試し下さい。');
        },
        onCreate : function() {
		    var html = '読み込み中、少々お待ちください…………  ' + '<img src="' + UrlConfig.StaticUrl + '/admin/img/photoeffect/loading.gif">';
		    $('completeMsg').update(html);		    
		},
        onSuccess: function(response) { 	   
	        if (response.responseText == 'true') {      
	            $('completeMsg').update('サイトの変更が完了しました。');
	        }
	        else if ('invalid_url' == response.responseText) {
	        	$('completeMsg').update('新規失敗しました。指定したサイト＋TopページファイルのURLは存在しません。');
	        }
	        else {
	        	$('completeMsg').update('変更失敗しました。しばらくたってからもう一度お試し下さい。');
	        }
		}
    });
}
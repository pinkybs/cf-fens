/**
 * site add(static/admin/js/manager/addsite.js)
 * site add 
 * 
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/25    zhangxin   
 */
 
/**
 *  page load function
 *  register funcion and init page
 */
Event.observe(window, 'load', function() { 
	Event.observe('btnSubmit', 'click', addSite);
});

var _valid = new Validation('frmAdd', {immediate:true,useTitles:true});

/**
 * add site 
 * @return void
 */
function addSite()
{
	$('errMsg').hide();
	if (!_valid.validate()) {
		$('errMsg').update('入力内容に誤りがあります。');
		$('errMsg').show();
		$('completeMsg').hide();
    	return;
    }
    
    $('completeMsg').show();
	//ajax add site 
    var frmSubmit = $('frmAdd');
    frmSubmit.action = UrlConfig.BaseUrl + '/ajax/manage/addsite';
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
	            $('completeMsg').update('サイトの新規登録が完了しました。引き続きサイト新規登録が行えます。');
	            $('txtName').value = '';
	            $('txtUrl').value = '';
	            $('txtToppage').value = '';
	            $('selArea').value = '';
	            $('selCate').value = '';
	        }
	        else if ('invalid_url' == response.responseText) {
	        	$('completeMsg').update('新規失敗しました。指定したサイト＋TopページファイルのURLは存在しません。');
	        }
	        else {
	        	$('completeMsg').update('入力内容に誤りがあります。サイトURLが存在していないか、あるいはすでに登録されています。');
	        }
		}
    });
}
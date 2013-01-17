/**
 * site category add(static/admin/js/manager/addcategory.js)
 * site category add 
 * 
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/25    zhangxin   
 */
 
/**
 *  page load function
 *  register funcion and init page
 */
Event.observe(window, 'load', function() { 
	Event.observe('btnSubmit', 'click', addCate);
});

var _valid = new Validation('frmAdd', {immediate:true,useTitles:true});

/**
 * add category 
 * @return void
 */
function addCate()
{
	$('errMsg').hide();
	if (!_valid.validate()) {
		$('errMsg').update('入力内容に誤りがあります。');
		$('errMsg').show();
		$('completeMsg').hide();
    	return;
    }
    
    $('completeMsg').show();
	//ajax add category 
    var frmSubmit = $('frmAdd');
    frmSubmit.action = UrlConfig.BaseUrl + '/ajax/manage/addcategory';
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
	            $('completeMsg').update('カテゴリーの新規登録が完了しました。引き続きカテゴリー新規登録が行えます。');
	            $('txtName').value = '';
	        }
	        else {
	        	$('completeMsg').update('新規失敗しました。しばらくたってからもう一度お試し下さい。');
	        }
		}
    });
}
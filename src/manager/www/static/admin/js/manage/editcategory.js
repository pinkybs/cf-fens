/**
 * site category edit(static/admin/js/manager/editcategory.js)
 * site category edit 
 * 
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/25    zhangxin   
 */
 
/**
 *  page load function
 *  register funcion and init page
 */
Event.observe(window, 'load', function() { 
	Event.observe('btnSubmit', 'click', editCate);
});

var _valid = new Validation('frmEdit', {immediate:true,useTitles:true});

/**
 * edit category 
 * @return void
 */
function editCate()
{
	$('errMsg').hide();
	if (!_valid.validate()) {
		$('errMsg').update('入力内容に誤りがあります。');
		$('errMsg').show();
		$('completeMsg').hide();
    	return;
    }
    
    $('completeMsg').show();
	//ajax edit category 
    var frmSubmit = $('frmEdit');
    frmSubmit.action = UrlConfig.BaseUrl + '/ajax/manage/editcategory';
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
	            $('completeMsg').update('カテゴリーの変更が完了しました。');
	        }
	        else {
	        	$('completeMsg').update('変更失敗しました。しばらくたってからもう一度お試し下さい。');
	        }
		}
    });
}
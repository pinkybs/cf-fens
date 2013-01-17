{%include file="header.tpl"%}

<body id="layout1">

<div id="wrap">
	<div id="hdr" class="clearfix">
		<h1 id="logo"><a href="{%$baseUrl%}"><img src="{%$staticUrl%}/img/contents/logo_1.jpg" alt="WSS"/></a></h1>
		<p class="description">ここに簡単なコピー</p>
	</div><!--/#hdr-->
	
	<div id="container">
	
		<div id="searchBox">
			<form id="searchForm" name="searchForm" method="post" action="{%$baseUrl%}/search" onsubmit="return false;">
				<div id="searchBoxInner">
					<div class="inner clearfix"><input type="text" id="searchText" maxlength="100" onkeypress="doKeyPress1(event.keyCode);" /><!--
					--><input type="image" name="searchBtn" src="{%$staticUrl%}/img/contents/btn_search_1.jpg" id="searchBtn" onclick="doSearch1();return false;" /></div>
				</div><!--/#searchBoxInner-->
				<input type="hidden" id="hidSrhKey" name="hidSrhKey" value="" />
                        	<input type="hidden" id="pageIndex" name="pageIndex" value="1" />
			</form><!--/#searchFrom-->
		</div><!--/#searchBox-->
		
		<div id="categoriesBox" class="box">
			<div class="inner">
				<h2 style="background: url({%$staticUrl%}/img/contents/head_category.jpg) no-repeat 0 0;width: 163px;height: 20px;">Categories（カテゴリー）</h2>
				<ul class="clearfix">
				{% foreach from=$lstCate item=cateData %}
					{% if $cateData.bind_count>0 %}
					<li><a href="{%$baseUrl%}/category?hidSrhCate={%$cateData.id%}">{%$cateData.name|escape%}（{%$cateData.bind_count%}）</a></li>
					{% /if %}
				{% /foreach %}
				</ul>
			</div><!--/.inner-->
		</div><!--/#categoriesBox.box-->

		
		<div id="areaBox" class="box">
			<div class="inner">
				<h2 style="background: url({%$staticUrl%}/img/contents/head_area.jpg) no-repeat 0 0;width: 88px;height: 20px;">Area（エリア）</h2>
				<div class="block">
					<h3><span>北海道地方</span></h3>
					<ul>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=1">北海道</a></li>

					</ul>
				</div><!--/.block-->
				<div class="block">
					<h3><span>東北地方</span></h3>
					<ul class="clearfix">
						<li><a href="{%$baseUrl%}/area?hidSrhArea=2">青森</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=3">岩手</a></li>

						<li><a href="{%$baseUrl%}/area?hidSrhArea=4">宮城</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=5">秋田</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=6">山形</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=7">福島</a></li>
					</ul>
				</div><!--/.block-->
				<div class="block">

					<h3><span>関東地方</span></h3>
					<ul class="clearfix">
						<li><a href="{%$baseUrl%}/area?hidSrhArea=8">茨城</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=9">栃木</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=10">群馬</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=11">埼玉</a></li>

						<li><a href="{%$baseUrl%}/area?hidSrhArea=12">千葉</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=13">東京</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=14">神奈川</a></li>
					</ul>
				</div><!--/.block-->
				<div class="block">
					<h3><span>中部地方</span></h3>

					<ul class="clearfix">
						<li><a href="{%$baseUrl%}/area?hidSrhArea=15">新潟</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=16">富山</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=17">石川</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=18">福井</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=19">山梨</a></li>

						<li><a href="{%$baseUrl%}/area?hidSrhArea=20">長野</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=21">岐阜</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=22">静岡</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=23">愛知</a></li>
					</ul>
				</div><!--/.block-->
				<div class="block">

					<h3><span>近畿地方</span></h3>
					<ul class="clearfix">
						<li><a href="{%$baseUrl%}/area?hidSrhArea=24">三重</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=25">滋賀</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=26">京都</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=27">大阪</a></li>

						<li><a href="{%$baseUrl%}/area?hidSrhArea=28">兵庫</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=29">奈良</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=30">和歌山</a></li>
					</ul>
				</div><!--/.block-->
				<div class="block">
					<h3><span>中国地方</span></h3>

					<ul class="clearfix">
						<li><a href="{%$baseUrl%}/area?hidSrhArea=31">鳥取</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=32">島根</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=33">岡山</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=34">広島</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=35">山口</a></li>

					</ul>
				</div><!--/.block-->
				<div class="block clear">
					<h3><span>四国地方</span></h3>
					<ul class="clearfix">
						<li><a href="{%$baseUrl%}/area?hidSrhArea=36">徳島</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=37">香川</a></li>

						<li><a href="{%$baseUrl%}/area?hidSrhArea=38">愛媛</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=39">高知</a></li>
					</ul>
				</div><!--/.block-->
				<div class="block">
					<h3><span>九州・沖縄地方</span></h3>
					<ul class="clearfix">

						<li><a href="{%$baseUrl%}/area?hidSrhArea=40">福岡</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=41">佐賀</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=42">長崎</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=43">熊本</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=44">大分</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=45">宮崎</a></li>

						<li><a href="{%$baseUrl%}/area?hidSrhArea=46">鹿児島</a></li>
						<li><a href="{%$baseUrl%}/area?hidSrhArea=47">沖縄</a></li>
					</ul>
				</div><!--/.block-->
			</div><!--/.inner-->
		</div><!--/#areaBox.box-->
	
	</div><!--/#continer-->
		<script type="text/javascript" language="javascript">
        /**
         * submit to search action
         * @return void
         */
        function doSearch1() {
            if ('' == $F('searchText').trimAll()) {
                return;
            }
	    $('hidSrhKey').value = $F('searchText').trimAll();
	    var tmp = $F('hidSrhKey');
            $('searchForm').submit();
	    saveSearchLog(tmp);
        }

	function doKeyPress1(keycode) {
	    if (keycode == 13) {
		doSearch1();
	    }
	}
	</script>

{%include file="footer.tpl"%}
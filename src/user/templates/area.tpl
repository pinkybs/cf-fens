{%include file="header.tpl"%}

<body id="layout2">
<div id="wrap">

	<div id="hdr" class="clearfix">
		<h1 id="logo"><a href="{%$baseUrl%}"><img src="{%$staticUrl%}/img/contents/logo_2.jpg" alt="WSS"/></a></h1>
		<p class="description">ここに簡単なコピー</p>
	</div><!--/#hdr-->
	
	<div id="container">
		<div id="searchBox">
			<div class="inner1">
				<div class="inner2">
					<ul class="clearfix"><!--
						--><li id="li1" {%if 1==$hidChkArea%}class="listTop stay"{%/if%}><a id="btn1" href="javascript:void(0);"><span>ウェブ全体</span></a></li><!--
						--><li id="li2" {%if 2==$hidChkArea%}class="listTop stay"{%/if%}><a id="btn2" href="javascript:void(0);"><span>エリア全体</span></a></li><!--
						--><li id="li3" {%if 3==$hidChkArea%}class="listTop stay"{%/if%}><a id="btn3" href="javascript:void(0);"><span>このエリア以下</span></a></li><!--
					--></ul>
				</div><!--/.inner2-->	
			</div><!--/.inner1-->
			<form id="searchForm" name="searchForm" method="post" action="{%$baseUrl%}/search" onsubmit="return false;">
				<div class="inner3">
					<div class="inner4">
						<div class="inner5 clearfix"><input type="text" id="searchText" name="searchText" value="{%$hidSrhKey%}" maxlength="100" /><!--
						--><input type="image" src="{%$staticUrl%}/img/contents/btn_search_2.jpg" id="searchBtn" name="searchBtn" /></div>
						<input type="hidden" id="hidCheck" name="hidCheck" value="3" />
						<input type="hidden" id="hidSrhKey" name="hidSrhKey" value="{%$hidSrhKey%}" />
						<input type="hidden" id="hidSrhArea" name="hidSrhArea" value="{%$hidSrhArea%}" />
						<input type="hidden" id="pageIndex" name="pageIndex" value="{%$pageIndex%}" />
					</div><!--/.inner4-->
				</div><!--/.inner3-->
			</form><!--/#searchFrom-->	
		</div><!--/#searchBox-->

		
		<div id="resultBox" class="liquidBox">
			<div class="lead clearfix">
				<h2><span>エリア検索</span></h2>
				<div>「<span id="lblKeyword">{%$areaName|escape%}</span>」エリアで検索した結果&nbsp;<span id="lblStartNum">{%$numStart%}</span>〜<span id="lblEndNum">{%$numEnd%}</span>件目&nbsp;/&nbsp;約<span id="lblTotalCount">{%$cntData%}</span>件</div>
			</div>
			
			{% if $lstData %}
			<ul>
			{% foreach from=$lstData key=value item=itemData %}
			<li {% if $value%2==1 %}class="odd"{% /if %}>
			    <h4><a href="{%$itemData.url%}" target="_blank">{%$itemData.title|truncatejp:60:"..."|escape%}</a></h4>
			    <p>{%$itemData.summary|escape|replace:"$hidSrhKey":"$lightKey"%}</p>
			    <address><a href="{%$itemData.url%}">{%$itemData.url|truncatejp:60:"..."%}</a></address>
			        </li>
			{% /foreach %}
			</ul>
			{% else %}
				{% if $hidSrhKey=="" %}
				<p>このエリアーにはサイトが登録されていません。</p>
				{% else %}
				<p>キーワードに該当するサイトが登録されていません。</p>
				{% /if %}
			{% /if %}
		</div><!--/-->
		{% html_linno_pager_nav totalCount=$cntData pageIndex=$pageIndex pageSize=$pageSize urlAction=$pagingUrl %}
		
		<div id="areaBox" class="box">
			<div class="inner clearfix">
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
    	<script src="{%$staticUrl%}/js/area.js?{%$version.js%}"></script>

{%include file="footer.tpl"%}
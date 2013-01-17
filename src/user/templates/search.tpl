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
                </div><!--/.inner2-->
            </div><!--/.inner1-->
            <form name="searchForm" id="searchForm" method="post" action="{%$baseUrl%}/search" onsubmit="return false;">
                <div class="inner3">
                    <div class="inner4">
                        <div class="inner5 clearfix"><input type="text" id="searchText" name="searchText" value="{%$hidSrhKey%}" maxlength="100" /><!--
                        --><input type="image" src="{%$staticUrl%}/img/contents/btn_search_2.jpg" id="searchBtn" name="searchBtn" /></div>
                    <input type="hidden" id="hidSrhKey" name="hidSrhKey" value="{%$hidSrhKey%}" />
                            <input type="hidden" id="pageIndex" name="pageIndex" value="{%$pageIndex%}" />
                    </div><!--/.inner4-->
                </div><!--/.inner3-->
            </form><!--/#searchFrom-->
        </div><!--/#searchBox-->

        
        <div id="resultBox" class="liquidBox">
            <div class="lead clearfix">
                <h2><span>ウェブ検索結果</span></h2>
                <div>「<span id="lblKeyword">{%$hidSrhKey%}</span>」ウェブで検索した結果&nbsp;<span id="lblStartNum">{%$numStart%}</span>〜<span id="lblEndNum">{%$numEnd%}</span>件目&nbsp;/&nbsp;約<span id="lblTotalCount">{%$cntData%}</span>件</div>

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
            <p>キーワードに該当するサイトが見つかりませんでした。</p>
            {% /if %}
        </div><!--/-->
        {% html_linno_pager_nav totalCount=$cntData pageIndex=$pageIndex pageSize=$pageSize urlAction=$pagingUrl %}
    </div><!--/#continer-->
    <script src="{%$staticUrl%}/js/search.js?{%$version.js%}"></script>

{%include file="footer.tpl"%}
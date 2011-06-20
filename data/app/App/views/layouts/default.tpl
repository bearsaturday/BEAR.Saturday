<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja" id="{appinfo id}">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-language" content="ja" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title>{* タイトル *}{$layout.title}</title>
<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
<link rel="shortcut icon" href="/favicon.ico?{appinfo version}" />
{* metaタグ *}{$layout.metas}
<link rel="stylesheet" href="/css/default.css?{appinfo version}" type="text/css"    media="screen" />
<link rel="stylesheet" href="/css/app.css?{appinfo version}" type="text/css"    media="screen" />
<link rel="stylesheet" href="/css/form.css?{appinfo version}" type="text/css"   media="screen" />
{* JavaScript *} {if $layout.js.enable}
{* jquery.bear.min.jsと同じものです
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script type="text/javascript" src="/bear/jquery.bear.js?{appinfo version}"></script>
*}
<script type="text/javascript" src="/bear/jquery.bear.min.js?{appinfo version}"></script>
<script type="text/javascript" src="/bear/jquery.cookie.js"></script>
{if $layout.js.extra}{ $layout.js.extra}{/if}
<script type="text/javascript" src="/js/app.js?{appinfo version}"></script>
<script type="text/javascript" src="{$layout.js.page|default:'default.js'}"></script>
{/if} {* /JavaScript *}

{* meta linkタグ *}{$layout.links}
{* meta pager linkタグ *}{$pager.links.linktags|default:""}
</head>
<body class="container">
{* ヘッダー *}{include file="elements/header.tpl"} {* メッセージ *}{if $msg}
<center>
<div class="{$msg_css|default:"msg-ok"}">{$msg}</div>
</center>
{/if} {* ページ *}
<div class="content">{$content_for_layout}</div>
<br style="clear:both" />{* フッター *}{include file="elements/footer.tpl"}
</body>
</html>

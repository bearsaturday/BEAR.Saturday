<?xml version="1.0" encoding="{$charset}" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-Type" content="application/xhtml+xml; charset={$agent.charset}" />
        <title>{* タイトル *}{$layout.title}</title>
        {* metaタグ *}{$layout.metas}
        {* AUのみno-cache　*}{agent in="Ezweb"}
        <meta http-equiv="Cache-Control" content="no-cache" />
        <meta http-equiv="Expires" content="-1" />
        {/agent}
        <style type="text/css">
        <![CDATA[
        a:link{literal}{{/literal}color: #{$layout.color.a.link}{literal}}{/literal}
        a:visited{literal}{{/literal}color: #{$layout.color.a.visited}{literal}}{/literal}
        ]]>
        </style>
    </head>
    <body>
        <div style="font-size:x-small">
        {* ヘッダー *}{include file="elements/header.mobile.tpl"}
        {* ページ *}{$content_for_layout}
        {* フッター *}{include file="elements/footer.mobile.tpl"}
        </div>
    </body>
</html>
{* アプリケーション別HTTPステータス出力画面 *}
{* $isMobileとerrorがアサインされています *}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>{$error.code} {$error.codeMsg}</title>
</head>
<body text="#000000" bgcolor="#FFFFFF">
<table border="0" cellpadding="2" cellspacing="0" width="100%">

    <tr>
        <td bgcolor="orange"><font face="arial,sans-serif" color="#FFFFFF"><b>{$error.serverProtocol} {$error.code}</b></font>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
</table>
<blockquote>
<h2>{$error.code} {$error.codeMsg}</h2>
</blockquote>
<table width="100%" cellpadding="3" cellspacing="0">
    <tr>
        <td></td>
        <td>
        {if $error.code >= 500}{$error.body|default:"The server encountered temporary error."}
        {else}
        {$error.body|default:""}
        {/if}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
</table>
<hr size=4 color="orange">
</body>
</html>
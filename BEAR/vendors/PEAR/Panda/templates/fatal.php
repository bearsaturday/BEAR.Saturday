<?php
$html = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>503 Service Temporarily Unavailable</title>

</head>
<body text="#000000" bgcolor="#FFFFFF">
<table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td bgcolor="orange"><font face="arial,sans-serif" color="#FFFFFF"><b>Error</b></font>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
</table>
<blockquote>
<h2>503 Service Temporary Unavailable</h2>
<p>The server is temporary unable to service your request. ref# <code>{$id}</code>
</blockquote>
<hr size=2 color="orange">
</body>
</html>
EOD;
return $html;
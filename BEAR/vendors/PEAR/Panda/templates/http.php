<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php echo $error['code']; ?> <?php echo $error['codeMsg']; ?></title>
</head>
<body text="#000000" bgcolor="#FFFFFF">
<table border="0" cellpadding="2" cellspacing="0" width="100%">

    <tr>
        <td bgcolor="<?php echo $error['color'] ?>"><font face="arial,sans-serif" color="#FFFFFF"><b><?php echo $error['serverProtocol']; ?> <?php echo $error['code']; ?></b></font>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
</table>
<blockquote>
<h2><?php echo $error['code']; ?> <?php echo $error['codeMsg']; ?></h2>
<p>
<?php
 if ($error['code'] >= 500 && !$error['body']) {
    echo "The server encountered temporary error.";
 } else {
     echo $error['body'];
 }
?>
</p>
</blockquote>

<hr size=4 color="<?php echo $error['color'] ?>">
</body>
</html>

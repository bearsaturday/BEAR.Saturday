<?php
ob_start();
phpinfo();
$info = ob_get_clean();
$info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
echo '<body><div id="bearinfo">' . $info . '</div></body>';

?>

<style type="text/css">
#bearinfo body,td,th,h1,h2 {
	font-family: sans-serif;
}

#bearinfo pre {
	margin: 0px;
	font-family: monospace;
}

#bearinfo a:link {
	color: #000099;
	text-decoration: none;
	background-color: #ffffff;
}

#bearinfo a:hover {
	text-decoration: underline;
}

#bearinfo table {
	border-collapse: collapse;
}

#bearinfo .center {
	text-align: center;
}

#bearinfo .center table {
	margin-left: auto;
	margin-right: auto;
	text-align: left;
}

#bearinfo .center th {
	text-align: center !important;
}

#bearinfo td,th {
	border: 1px solid #000000;
	font-size: 75%;
	vertical-align: baseline;
}

#bearinfo h1 {
	font-size: 150%;
}

#bearinfo h2 {
	font-size: 125%;
}

#bearinfo .p {
	text-align: left;
}

#bearinfo .e {
	background-color: #ccccff;
	font-weight: bold;
	color: #000000;
}

#bearinfo .h {
	background-color: #9999cc;
	font-weight: bold;
	color: #000000;
}

#bearinfo .v {
	background-color: #cccccc;
	color: #000000;
}

#bearinfo .vr {
	background-color: #cccccc;
	text-align: right;
	color: #000000;
}

#bearinfo img {
	float: right;
	border: 0px;
}

#bearinfo hr {
	width: 600px;
	background-color: #cccccc;
	border: 0px;
	height: 1px;
	color: #000000;
}
</style>

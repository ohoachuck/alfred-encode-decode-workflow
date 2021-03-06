<?php

//header ("Content-Type:text/xml");
//syslog(LOG_ERR, "message to send to log");

// alfred encode.php test\"\'<é

// ****************

function str_split_unicode($str, $l = 0) {
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}

require_once('workflows.php');

$w = new Workflows();
if (!isset($query)) {
	$query = <<<EOD
{query}
EOD;
}
$chars = str_split_unicode($query);

if (0) {
	echo "".$query." - ".implode("", $chars)."\n";
	echo "urlencode = ".urlencode($query)."\n";
	echo "utf8_encode = ".utf8_encode($query)."\n";
	echo "htmlentities = ".htmlentities($query, ENT_QUOTES, 'UTF-8', false)."\n";
	$html_encode = '';
	$table = get_html_translation_table(HTML_ENTITIES);
	for ($i = 0, $l = sizeof($chars); $i < $l; $i++) {
		echo $chars[$i]." -> ".$table[$chars[$i]]."\n";
		$html_encode .= $table[$chars[$i]] ? $table[$chars[$i]] : $chars[$i];
	}
	$html_encode = htmlentities($html_encode, ENT_QUOTES, 'UTF-8', false);
	echo " = ".$html_encode."\n";
	echo "base64_encode = ".base64_encode($query)."\n";
	
	exit();
}


$encodes = array();

// url
$url_encode = urlencode($query);
if ($url_encode != $query) $encodes["URL Encoded"] = $url_encode;

// utf8
$utf8_encode = utf8_encode($query);
if ($utf8_encode != $query) $encodes["UTF8 Encoded"] = $utf8_encode;

// HTML
/*$html_encode = '';
$table = get_html_translation_table(HTML_ENTITIES);
for ($i = 0, $l = sizeof($chars); $i < $l; $i++) {
	$html_encode .= $table[$chars[$i]] ? $table[$chars[$i]] : $chars[$i];
}
$html_encode = htmlentities($html_encode, ENT_QUOTES, 'UTF-8', false);*/
$html_encode = htmlentities($query, ENT_QUOTES, 'UTF-8', false);
if ($html_encode != $query) $encodes["HTML Encoded"] = $html_encode;

// base64
$base64_encode = base64_encode($query);
if ($base64_encode != $query) $encodes["base64 Encoded"] = $base64_encode;

foreach($encodes as $key => $value) {
	$w->result( $key, $value, $value, $key, 'icon.png', 'yes' );
}

if ( count( $w->results() ) == 0 ) {
	$w->result( 'encode', $query, 'Nothing useful resulted', 'The encoded strings were the same as your query', 'terminal.png', 'yes' );
}

echo $w->toxml();
// ****************
?>
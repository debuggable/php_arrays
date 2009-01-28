#!/usr/bin/php
<?php
define('ROOT', dirname(dirname(__FILE__)));

$html = file_get_contents('http://www.mimetype.org/');
$regex = '/<tr><td>([^<]+)<\/td><td>([^<]+)<\/td><\/tr>/';
preg_match_all($regex, $html, $matches, PREG_SET_ORDER);

$items = array();
foreach ($matches as $match) {
	list(, $mimeType, $extensions) = $match;
	$extensions = preg_split('/\s+/', $extensions);
	foreach ($extensions as $ext) {
		$items[$ext] = $mimeType;
	}
}

$template = "<?php\n\$items = %s;\n?>";

$out = sprintf($template, var_export($items, true));
$out = preg_replace('/array \(/', 'array(', $out);
file_put_contents(ROOT . '/ext2mime.php', $out);

$items = array_flip($items);
$out = sprintf($template, var_export($items, true));
$out = preg_replace('/array \(/', 'array(', $out);
file_put_contents(ROOT . '/mime2ext.php', $out);

?>
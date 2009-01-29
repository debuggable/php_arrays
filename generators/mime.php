#!/usr/bin/php
<?php
require_once('libs/php_array.php');

$src = 'http://www.mimetype.org/';
$html = file_get_contents($src);
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


$out = PhpArray::generate(compact('src', 'items'));
file_put_contents(ROOT . '/extensions.php', $out);

$items = array_flip($items);
$out = PhpArray::generate(compact('src', 'items'));
file_put_contents(ROOT . '/mime_types.php', $out);

?>
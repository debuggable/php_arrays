<?php
define('ROOT', dirname(dirname(dirname(__FILE__))));
define('TMP', ROOT . '/generators/tmp');

class PhpArray{
	public static function generate($vars) {
		extract($vars);

		// Make var_export real pretty
		$items =  preg_replace('/(\n\s+)?array \(/', 'array(', var_export($items, true));
		$template = <<<EOD
<?php
/**
 * @copyright $src
 * @link http://github.com/debuggable/php_arrays/tree
 * @author Felix Geisendörfer (felix@debuggable.com)
 **/
\$items = $items;
?>
EOD;
		return $template;
	}

	public static function fetch($src, $file) {
		$srcFile = TMP . '/' . $file;
		if (file_exists($srcFile)) {
			return $srcFile;
		}

		$tmpDirOk = is_dir(TMP) || mkdir(TMP);
		if (!$tmpDirOk) {
			trigger_error('Could not create tmp dir', E_USER_ERROR);
		}

		$downloadFile = TMP . '/' . basename($src);
		if (!file_exists($downloadFile)) {
			echo sprintf("-> Downloading %s\n", basename($src));
			$cmd = sprintf('curl -o %s %s', escapeshellarg($downloadFile), escapeshellarg($src));
			system($cmd, $error);
			if ($error) {
				trigger_error('Could not download file', E_USER_ERROR);
			}
		}

		if (pathinfo($src, PATHINFO_EXTENSION) != 'zip') {
			return $downloadFile;
		}

		echo sprintf("-> Unzipping %s\n", basename($downloadFile));
		$cmd = sprintf('unzip -o %s -d %s', escapeshellarg($downloadFile), escapeshellarg(TMP));		
		exec($cmd, $stdout, $error);
		if ($error) {
			trigger_error('Could not unzip downloaded file', E_USER_ERROR);
		}

		if (!preg_match_all('/\s+inflating:\s(.+)$/m', join("\n", $stdout), $matches, PREG_SET_ORDER)) {
			trigger_error('Empty archive ', E_USER_ERROR);
		}

		foreach ($matches as $match) {
			$regex = sprintf('/%s$/', preg_quote($file, '/'));
			if (preg_match($regex, $match[1])) {
				return $match[1];
			}
		}
		trigger_error('File was not found in archive', E_USER_ERROR);
	}
}


?>
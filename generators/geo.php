#!/usr/bin/php
<?php
require_once('libs/php_array.php');


class GeoName{
	public static $files = array(
		'countries.php' => array(
			'src' => 'http://download.geonames.org/export/dump/countryInfo.txt',
			'srcFile' => 'countryInfo.txt',
			'map' => '_mapCountry'
		),
		'big_cities.php' => array(
			'src' => 'http://download.geonames.org/export/dump/cities15000.zip',
			'srcFile' => 'cities15000.txt',
			'map' => '_mapCity'
		),
		'time_zones.php' => array(
			'src' => 'http://download.geonames.org/export/dump/timeZones.txt',
			'srcFile' => '.txt',
			'map' => '_mapTimeZone'
		),
	);

	public static function generate($file) {
		extract(GeoName::$files[$file]);

		$srcFile = PhpArray::fetch($src, $srcFile);
		echo sprintf("-> Parsing %s\n", basename($srcFile));

		$items = array();
		$fp = fopen($srcFile, "r");
		$i = 0;
		while (($row = fgetcsv($fp, 0, "\t")) !== false) {
			if (count($row) == 1 || preg_match('/^#/', $row[0])) {
				continue;
			}

			$i++;
			$item = call_user_func(array('GeoName', $map), $row, $i);
			if ($item) {
				$items[] = $item;
			}
		}

		echo sprintf("-> Writing %s\n", $file);

		$out = PhpArray::generate(compact('src', 'items'));
		file_put_contents(ROOT . '/' . $file, $out);
	}

	protected static function _mapTimeZone($row, $i) {
		static $header;
		if ($i == 1) {
			$header = $row;
			foreach ($header as $key => $val) {
				if (!preg_match('/(gmt|dst).+(\d+\. \w+ \d{4})$/i', $val, $match)) {
					continue;
				}
				$day = date('Y-m-d', strtotime($match[2]));
				$header[$key] = array('type' => strtolower($match[1]), 'day' => $day);
			}
			return false;
		}
		return $row[0];
		// return array(
			// 'timezone_id' => $row[0],
			// ($header[1]['type'].'_offset') => array(
			// 	$header[1]['day'] => $row[1]
			// ),
			// ($header[2]['type'].'_offset') => array(
			// 	$header[2]['day'] => $row[1],
			// ),
		// );
	}

	protected static function _mapCountry($row) {
		return array(
			'name' => $row[4],
			'iso' => $row[0],
			'iso3' => $row[1],
			'iso_numeric' => $row[2],
			'fips' => $row[3],
			'captial' => $row[5],
			'area' => $row[6],
			'population' => $row[7],
			'continent' => $row[8],
			'tld' => $row[9],
			'currency_code' => $row[10],
			'currency_name' => $row[11],
			'phone_code' => $row[12],
			'postal_code_format' => $row[13],
			'postal_code_regex' => $row[14],
			'languages' => $row[15],
			'geoname_id' => $row[16],
			'neighbours' => $row[17],
		);
	}

	protected static function _mapCity($row) {
		return array(
			'geo_name_id' => $row[0],
			'name' => $row[1],
			'ascii_name' => $row[2],
			'alternate_name' => array_filter(preg_split('/(?<!\\\\),/', $row[3])),
			'latitude' => $row[4],
			'longitude' => $row[5],
			'feature_class' => $row[6],
			'feature_code' => $row[7],
			'country_iso' => $row[8],
			'alternate_country_iso' => array_filter(preg_split('/(?<!\\\\),/', $row[9])),
			// 'admin1_code' => $row[10],
			// 'admin2_code' => $row[11],
			// 'admin3_code' => $row[12],
			// 'admin4_code' => $row[13],
			'population' => $row[14],
			// 'elevation' => $row[15],
			'average_elevation' => $row[16],
			'timezone_id' => $row[17],
			'modified' => $row[18],
		);
	}
}

GeoName::generate('time_zones.php');
GeoName::generate('countries.php');
GeoName::generate('big_cities.php');



?>
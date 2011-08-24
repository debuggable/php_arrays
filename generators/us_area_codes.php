<?php
/**
 * Copy the table from http://en.wikipedia.org/wiki/List_of_North_American_Numbering_Plan_area_codes#United_States
 * into the heredoc and run
 */
$data = <<<END

END;

foreach ( explode( "\n", trim( $data ) ) as $line ) {

	list( $state, $area_codes ) = array_map( function( $v ){ return str_replace( ',', '', $v ); }, preg_split( '~(?<=[a-zA-Z])\s+(?=\d)~s', $line ) );
	$area_codes = preg_split( '~\s+~', trim( $area_codes ) );
	foreach( $area_codes as $area_code ) {
		$lines[] = sprintf( "\t$area_code => '$state'" );
	}
}
sort($lines);
echo sprintf( "\$items = array(\n%s\n);", implode( ",\n", $lines ) );
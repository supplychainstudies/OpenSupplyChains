<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

class Sourcemap_csv {
	public static function parse($kml) { }
	
	public static function make($supplychain) {
        var_dump($supplychain);
		$data = json_decode(Sourcemap_Geojson::make($supplychain));
		$points  = 'Name,Location,Address,Description,Percentage,vimeo:title,vimeo:link,youtube:title,youtube:link,flickr:setid,qty,CO2e,color,size';
		$lines = '';
		foreach($data->features as $ftr) {
			if($ftr->geometry->type == "Point") {
			    $points .= $ftr->properties->title.',';
				$points .= isset($ftr->properties->description) ? $ftr->properties->description : "";
			    $points .= '<coordinates>'.implode($ftr->geometry->coordinates,",").'</coordinates>';
			}
			else {	
			    $lines .= '<name>'.$ftr->properties->title.'</name>';
				$lines .= isset($ftr->properties->description) ? $ftr->properties->description : "";
			
			    $lines .= '<coordinates>'.implode($ftr->geometry->coordinates[0],",").' '.implode($ftr->geometry->coordinates[1],",").'</coordinates>';
			}
		}
		return $points;
	}
}

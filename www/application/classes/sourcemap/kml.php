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

class Sourcemap_Kml {
	public static function parse($kml) { }
	
	public static function make($supplychain) {
		$data = json_decode(Sourcemap_Geojson::make($supplychain));
		$k  = '<?xml version="1.0" encoding="UTF-8"?>';
		$k .= '<kml xmlns="http://www.opengis.net/kml/2.2"><Document>';
		
		$k .= '<name>'.$data->properties->title.'</name>';
	    $k .= '<description>';
	    $k .= '<![CDATA[';
		$k .= isset($data->properties->description) ? $data->properties->description : "";
	    $k .= ']]>';
	    $k .= '</description>';
	
		$k .= '<Style id="defaultline">';
		$k .= '<LineStyle>';
		$k .= '<color>aaffffff</color>';
		$k .= '<width>6</width>';
		$k .= '</LineStyle>';
		$k .= '</Style>';
		$k .= '<Style id="defaultpoint">';
		$k .= '<IconStyle>';
		$k .= '<scale>1.0</scale>';
		$k .= '<Icon><href>http://maps.google.com/mapfiles/kml/pal2/icon18.png</href></Icon>';
		$k .= '</IconStyle>';
		$k .= '</Style>';
		
		foreach($data->features as $ftr) {
			if($ftr->geometry->type == "Point") {
				$k .= '<Placemark>';
			    $k .= '<name>'.$ftr->properties->title.'</name>';
			    $k .= '<styleUrl>#defaultpoint</styleUrl>';								
			    $k .= '<description>';
			    $k .= '<![CDATA[';
				$k .= isset($ftr->properties->description) ? $ftr->properties->description : "";
			    $k .= ']]>';
			    $k .= '</description>';
			    $k .= '<Point>';
			    $k .= '<coordinates>'.implode($ftr->geometry->coordinates,",").'</coordinates>';
			    $k .= '</Point>';
			    $k .= '</Placemark>';
			}
			else {	
				$k .= '<Placemark>';
			    $k .= '<name>'.$ftr->properties->title.'</name>';
			    $k .= '<visibility>1</visibility>';		
			    $k .= '<styleUrl>#defaultline</styleUrl>';					
			    $k .= '<description>';
			    $k .= '<![CDATA[';
				$k .= isset($ftr->properties->description) ? $ftr->properties->description : "";
			    $k .= ']]>';
			    $k .= '</description>';
			    $k .= '<LineString>';
			    $k .= '<extrude>1</extrude>';
			    $k .= '<tessellate>1</tessellate>';
			    $k .= '<altitudeMode>clampToGround</altitudeMode>';
			
			    $k .= '<coordinates>'.implode($ftr->geometry->coordinates[0],",").' '.implode($ftr->geometry->coordinates[1],",").'</coordinates>';
			    $k .= '</LineString>';
			    $k .= '</Placemark>';
			}
		}
		$k .= '</Document></kml>';
		return $k;
	}
}
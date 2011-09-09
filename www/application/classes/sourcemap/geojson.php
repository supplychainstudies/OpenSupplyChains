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

class Sourcemap_Geojson {
	public static function parse($geojson) { }
	
	public static function make($supplychain) {
		$geojson = array("type" => "FeatureCollection", "features"=>array());

		foreach($supplychain->attributes as $k => $v) {
			$geojson["properties"][$k] = $v;
		}
		foreach(array_merge($supplychain->stops, $supplychain->hops) as $item) {
			$geom = Sourcemap_Wkt::read($item->geometry);
			switch($geom[0]) {
				case "point":
					$pt = new Sourcemap_Proj_Point($geom[1][0], $geom[1][1]);
		        	$pt = Sourcemap_Proj::transform('EPSG:900913 ', 'WGS84', $pt);
					$geometry = array("type"=>"Point","coordinates"=>array($pt->x,$pt->y));
				break;
				case "multilinestring":
					$return = array();
					array_walk_recursive($geom[1], function($a) use (&$return) { $return[] = $a; });
					$pt1 = new Sourcemap_Proj_Point($return[2], $return[3]);
					$pt2 = new Sourcemap_Proj_Point($return[5], $return[6]);
					
		        	$pt1 = Sourcemap_Proj::transform('EPSG:900913 ', 'WGS84', $pt1);
		        	$pt2 = Sourcemap_Proj::transform('EPSG:900913 ', 'WGS84', $pt2);
		
					$geometry = array("type"=>"LineString","coordinates"=>array(array($pt1->x,$pt1->y), array($pt2->x,$pt2->y)));
				break;
				
				default:
				break;
					
			}
			$props = array();
			foreach($item->attributes as $k => $v) {
				$props[$k] = $v;
			}
			array_push($geojson["features"], array("type"=>"Feature", "geometry"=>$geometry, "properties"=>$props));
			
		}
		return json_encode($geojson);
	}
}
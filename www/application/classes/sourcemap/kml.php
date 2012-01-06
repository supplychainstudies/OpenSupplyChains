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
		
		$k .= '<name>'.Sourcemap_Kml::xmlentities($data->properties->title).'</name>';
	    $k .= '<description>';
	    $k .= '<![CDATA[';
		$k .= isset($data->properties->description) ? Sourcemap_Kml::xmlentities($data->properties->description) : "";
	    $k .= ']]>';
	    $k .= '</description>';
		
		foreach($data->features as $ftr) {
			if($ftr->geometry->type == "Point") {
				$k .= '<Placemark>';
			    $k .= '<name>';
                $k .= isset($ftr->properties->title) ? Sourcemap_Kml::xmlentities($ftr->properties->title) : "Untitled Point";
                $k .= '</name>';
				$k .= '<Style>';
		        $k .= '<LabelStyle>';
		        $k .= '<color>ffffffff</color>';
		        $k .= '<scale>1.5</scale>';
		        $k .= '</LabelStyle>';
				$k .= '<IconStyle>';
				$k .= '<scale>1.0</scale>';
				$k .= '<Icon><href>http://maps.google.com/mapfiles/kml/pal2/icon18.png</href></Icon>';
		        $k .= '<color>';
				$k .= isset($ftr->properties->color) ? Sourcemap_Kml::getKMLDotColor($ftr->properties->color) : 'aaffffff';
				$k .= '</color>';
				$k .= '</IconStyle>';
				$k .= '</Style>';												
			    $k .= '<description>';
			    $k .= '<![CDATA[<div class="gebubble">';
				$k .= isset($ftr->properties->description) ? Sourcemap_Kml::xmlentities($ftr->properties->description) : "";
				$k .= isset($ftr->properties->{"url:moreinfo"}) ? '<br/>'.$ftr->properties->{"url:moreinfo"} : "";
				if($data->properties->{"sm:ui:co2e"} == 1 && $data->properties->{"sm:ui:weight"}) {
					// Should be smart units.
					$k .= isset($ftr->properties->co2e) ? '<br/><strong>Footprint: '.$ftr->properties->co2e." kg co2e " : "";					
					$k .= isset($ftr->properties->weight) ? 'Weight: '.$ftr->properties->weight." kg.</strong>" : "";				
				}
				if(isset($ftr->properties->{"youtube:link"})) {
					$subject = $ftr->properties->{"youtube:link"};
					$pattern = '%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';					
					
					preg_match($pattern, $subject, $matches);					
					//if(isset($matches[1])) { $k .= '<iframe width="500" height="360" class="youtube-link" type="text/html" src="http://www.youtube.com/embed/'.$matches[1].'" frameborder="0" allowfullscreen></iframe>'; }		
				}
				if(isset($ftr->properties->{"vimeo:link"})) {
					$subject = $ftr->properties->{"vimeo:link"};
					$pattern = '/^http:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/';
				
					preg_match($pattern, $subject, $matches);
					//if(isset($matches[3])) { $k .= '<iframe width="500" height="360" class="vimeo-link" type="text/html" src="http://player.vimeo.com/video/'.$matches[3].'?title=0&amp;byline=0&amp;portrait=0&amp;frameborder=00&amp;allowfullscreen=0"></iframe>'; }	  
				}
           
				
			    $k .= '</div>]]>';
			    $k .= '</description>';
			    $k .= '<Point>';
			    $k .= '<coordinates>'.implode($ftr->geometry->coordinates,",").'</coordinates>';
			    $k .= '</Point>';
			    $k .= '</Placemark>';
			}
			else {	
				$k .= '<Placemark>';
			    $k .= '<name>';
                $k .= isset($ftr->properties->title) ? Sourcemap_Kml::xmlentities($ftr->properties->title) : "Untitled Line";
                $k .= '</name>';
			    $k .= '<visibility>1</visibility>';		
				$k .= '<Style id="defaultline">';
				$k .= '<LineStyle>';
		        $k .= '<color>';
				$k .= isset($ftr->properties->color) ? Sourcemap_Kml::getKMLLineColor($ftr->properties->color) : 'aaffffff';
				$k .= '</color>';
				$k .= '<width>6</width>';
				$k .= '</LineStyle>';
				$k .= '</Style>';
			    $k .= '<description>';
			    $k .= '<![CDATA[<div class="gebubble">';
				$k .= isset($ftr->properties->description) ? Sourcemap_Kml::xmlentities($ftr->properties->description) : "";
				$k .= isset($ftr->properties->{"url:moreinfo"}) ? '<br/>'.$ftr->properties->{"url:moreinfo"} : "";
				if($data->properties->{"sm:ui:co2e"} == 1 && $data->properties->{"sm:ui:weight"}) {
					// Should be smart units.
					$k .= isset($ftr->properties->co2e) ? '<br/><strong>Footprint: '.$ftr->properties->co2e." kg co2e " : "";					
					$k .= isset($ftr->properties->weight) ? 'Weight: '.$ftr->properties->weight." kg.</strong>" : "";				
				}
				
			    $k .= '</div>]]>';
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
	
	private static function getKMLDotColor($color) {
		$color = substr($color,1);
		$r = substr($color, 0, 2);
		$g = substr($color, 2, 2);
		$b = substr($color, 4, 2);
		return "ff".$b.$g.$r;			
	}
	private static function getKMLLineColor($color) {
		$color = substr($color,1);
		$r = substr($color, 0, 2);
		$g = substr($color, 2, 2);
		$b = substr($color, 4, 2);
		return "aa".$b.$g.$r;			
	}
	private static function xmlentities($string) {
    	return str_replace(array("<", ">", "\"", "'", "&"), array("&lt;", "&gt;", "&quot;", "&apos;", "&amp;"), $string);
	}	
}

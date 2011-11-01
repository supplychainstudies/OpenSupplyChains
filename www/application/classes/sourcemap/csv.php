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

	    const FSTA = 'FSTA';
	    const FEND = 'FEND';
	    const FVAL = 'FVAL';
	    const FESC = 'FESC';
	    const FQUO = 'FQUO';

	    public static function parse($csv) {
	        $lines = explode("\n", $csv);
	        $data = array();
	        foreach($lines as $i => $line) {
	            $data[] = self::parse_csv_row($line);
	        }
	        return $data;
	    }

	    public static function parse_csv_row($row) {
	        $row = trim($row);
	        $vs = array();
	        $b = '';
	        $st = self::FSTA;
	        for($i=0; $i<strlen($row); $i++) {
	            $c = $row[$i];
	            switch($st) {
	                case self::FSTA:
	                    switch($c) {
	                        case '"':
	                            $st = self::FQUO;
	                            $b = '';
	                            break;
	                        case ',':
	                            $vs[] = '';
	                            break;
	                        case ' ':
	                            break;
	                        default:
	                            $b .= $c;
	                            $st = self::FVAL;
	                            break;
	                    }
	                    break;
	                case self::FQUO:
	                    switch($c) {
	                        case '"':
	                            $st = self::FEND;
	                            break;
	                        case '\\':
	                            $st = self::FESC;
	                            break;
	                        default:
	                            $b .= $c;
	                            break;
	                    }
	                    break;
	                case self::FVAL:
	                    switch($c) {
	                        case ',':
	                            $vs[] = $b;
	                            $b = '';
	                            $st = self::FSTA;
	                            break;
	                        default:
	                            $b .= $c;
	                            break;
	                    }
	                    break;
	                case self::FESC:
	                    switch($c) {
	                        case '"':
	                            $b .= $c;
	                            $st = self::FQUO;
	                            break;
	                        default:
	                            $b .= '\\';
	                            $b .= $c;
	                            $st = self::FQUO;
	                            break;
	                    }
	                    break;
	                case self::FEND:
	                    switch($c) {
	                        case ',':
	                            $vs[] = $b;
	                            $b = '';
	                            $st = self::FSTA;
	                            break;
	                        default:
	                            break;
	                    }
	                    break;
	                default:
	                    break;
	            }
	        }
	        if(strlen($b)) $vs[] = $b;
	        return $vs;
	    }

	    public static function make_csv_row($arr, $delim=',', $encap='"') {
	        $csv_arr = array();
	        $mink = min(array_keys($arr));
	        $maxk = max(array_keys($arr));
	        for($i=$mink; $i<=$maxk; $i++) {
	            $s = isset($arr[$i]) ? $arr[$i] : '';
	            if($encap) $s = str_replace($encap, '\\'.$encap, $s);
	            $csv_arr[] = (string)$s == '' ? '' : $encap.$s.$encap;
	        }
	        return implode($delim, $csv_arr);
	    }

	    public static function arr2csv($arr, $delim=',', $encap='"') {
	        $csv = '';
	        for($i=0; $i<count($arr); $i++)
	            $csv .= self::make_csv_row($arr[$i], $delim, $encap)."\n";
	        return $csv;
	    }
	
	
	public static function make($supplychain) {
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
	
} // End Class

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
//require(MODPATH."PHPExcel/classes/PHPExcel.php");
class Sourcemap_xls {
	//public static function parse($kml) { }
	//git@codebasehq.com:sourcemap/sourcemap/sourcemap.git
	public static function make($supplychain) {
		
		$stopswriter = new PHPExcel();
		$stopswriter->createSheet();
		$stopswriter->setActiveSheetIndex(0);
		$stopswriter->getActiveSheet()->setTitle("Upstream");
		$stopswriter->createSheet();
		$stopswriter->setActiveSheetIndex(1);
		$stopswriter->getActiveSheet()->setTitle("Downstream");
		
		// We have to figure out the paths/tiers
		// To do that, we can traverse hops and push stuff onto paths
		$tree = array();
		// Upstream Hierarchy
		$uh = array();
		// Downstream Hierarchy
		$dh = array();
		$umax_level = 0;
		$dmax_level = 0;
		// Find 0th points
		//$start_stops = array();
		//foreach($supplychain->hops as $hop) {
		//	if (isset($start_stops[$hop->to_stop_id]) == false) $start_stops[$hop->to_stop_id]=1; else $start_stops[$hop->to_stop_id]++;
		//	if (isset($start_stops[$hop->from_stop_id]) == false) $start_stops[$hop->from_stop_id]=1; else $start_stops[$hop->from_stop_id]++;
		//}
		//foreach($start_stops as $meow) {
		//	
		//}
		foreach($supplychain->hops as $hop) {
			// First, we have to see if the to stop is already in the tree
			// http://192.168.1.39/services/supplychains/211?f=xls
			$found = false;
			foreach ($tree as $position=>$t) {
				if ($t['id'] == $hop->to_stop_id) {
					//$first_half = array_slice($tree,0,$position);
					//$middle = array('id'=>$hop->from_stop_id,'level'=>($t['level']+1));
					//$max_level = max($max_level, ($t['level']+1));
					//$last_half = array_slice($tree,$position);
					//array_unshift($last_half,$middle);
					//$tree = array_merge($first_half,$last_half);
					$stops_hierarchy[$hop->from_stop_id] = array('level'=>($t['level']+1), 'row'=>$position);
					$found = true;
					break 1;
				}
			} 
			// If it is upstream
			if(isset($uh[$hop->from_stop_id]) == false) {	
				if(isset($uh[$hop->to_stop_id]) == false) {
					$uh[$hop->to_stop_id] = array('level'=>0, 'row'=>count($uh)+1);
				}		
				$uh[$hop->from_stop_id] = array('level'=>($uh[$hop->to_stop_id]['level']+1), 'row'=>$uh[$hop->to_stop_id]['row']+1);
				$umax_level = max($umax_level, ($uh[$hop->to_stop_id]['level']+1));
				foreach ($uh as $num=>$st) {
					if ($st['row'] >= $uh[$hop->from_stop_id]['row']&& $num != $hop->from_stop_id) {
						$uh[$num]['row']++;
					}
				} 
			} else {
               // Has to be downstream, therefore: 
	           // 1) Take it out of the upstream series 
	           // Anything that is a child needs to be moved up one level 	                
                $trip = false; 
                foreach ($uh as $num=>$st) { 
                    if ($st['row'] >= $uh[$hop->from_stop_id]['row']) { 
                        $uh[$num]['row']--; 
                        if ($uh[$num]['level'] <= $uh[$hop->from_stop_id]['level']) { 
                            $trip = true; 
                        } 
                        if ($trip == false) { 
                            $uh[$num]['level']--; 
                        } 
                    } 
                }     
                unset($uh[$hop->from_stop_id]);     
                                        
	            // 2) Pop it in the downstream series
				if(isset($dh[$hop->from_stop_id]) == false) {
					$dh[$hop->from_stop_id] = array('level'=>0, 'row'=>count($dh)+1);
				}
				$dh[$hop->to_stop_id] = array('level'=>($dh[$hop->from_stop_id]['level']+1), 'row'=>$dh[$hop->from_stop_id]['row']+1);
				$dmax_level = max($dmax_level, ($dh[$hop->from_stop_id]['level']+1));
				foreach ($dh as $num=>$st) {
					if ($st['row'] >= $dh[$hop->to_stop_id]['row']&& $num != $hop->to_stop_id) {
						$dh[$num]['row']++;
					}
				}
			}
		}	
		
		$ucolumns = array();
		for($i=0;$i<=$umax_level;$i++) {
			$ucolumns[] = "Tier ".$i;
		}
		$dcolumns = array();
		for($i=0;$i<=$dmax_level;$i++) {
			$dcolumns[] = "Tier ".$i;
		} 
		foreach($supplychain->stops as $stop) {
			if (isset($uh[$stop->id]) == true) {
				$stopswriter->setActiveSheetIndex(0);
				if (isset($stop->attributes->title) == true) {
					$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($uh[$stop->id]['level'],$uh[$stop->id]['row']+1,$stop->attributes->title);
				}
				foreach ($stop->attributes as $attribute_name=>$attribute_value) {
					if (in_array($attribute_name,$ucolumns) == false) {
						$ucolumns[] = $attribute_name;
					}
					$stopswriter->getActiveSheet()->setCellValueByColumnAndRow(array_search($attribute_name,$ucolumns),$uh[$stop->id]['row']+1,$attribute_value);					
				}
			}
			if (isset($dh[$stop->id]) == true) {
				$stopswriter->setActiveSheetIndex(1);
				if (isset($stop->attributes->title) == true) {
					$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($dh[$stop->id]['level'],$dh[$stop->id]['row']+1,$stop->attributes->title);
				}
				foreach ($stop->attributes as $attribute_name=>$attribute_value) {
					if (in_array($attribute_name,$dcolumns) == false) {
						$dcolumns[] = $attribute_name;
					}
					$stopswriter->getActiveSheet()->setCellValueByColumnAndRow(array_search($attribute_name,$dcolumns),$dh[$stop->id]['row']+1,$attribute_value);					
				}
			}
			$vals = array(
				'title' => "",
				'placename' => "",
				'address' => "",
				'description' => "",
				'percentage' => "",
				'youtube:title' => "",
				'youtube:link' => "",
				'flickr:setid' => "",
				'qty' => "",
				'co2e' => "",
				'color' => ""
			);
			//$stops_hierarchy[$stop->id]['level'];
			if (isset($stop->attributes->title) == true) {
				$vals['title'] = $stop->attributes->title;
				$vals['placename'] = $stop->attributes->title;
			}
			if (isset($stop->attributes->name) == true) {
				$vals['title'] = $stop->attributes->name;
			}				
			if (isset($stop->attributes->placename) == true) {
				$vals['placename'] = $stop->attributes->placename;
			}
			if (is_array($stop->geometry) == true) {
				$vals['address'] = $stop->geometry;
			}
			if (isset($stop->attributes->address) == true) {
				$vals['address'] = $stop->attributes->address;
			}
			if (isset($stop->attributes->description) == true) {
				$vals['description'] = $stop->attributes->description;
			}
			if (isset($stop->attributes->{"youtube-url"}) == true) {
				$vals['youtube:link'] = $stop->attributes->{"youtube-url"};
			}
			if (isset($stop->attributes->{"youtube-title"}) == true) {
				$vals['youtube:title'] = $stop->attributes->{"youtube-title"};
			}
			if (isset($stop->attributes->{"flickr-setid"}) == true) {
				$vals['flickr:setid'] = $stop->attributes->{"flickr-setid"};
			}		
			if (isset($stop->attributes->qty) == true) {
				$vals['qty'] = $stop->attributes->qty;
			}
			if (isset($stop->attributes->co2e) == true) {
				$vals['co2e'] = $stop->attributes->co2e;
			}
			if (isset($stop->attributes->color) == true) {
				$vals['color'] = $stop->attributes->color;
			}
			if (isset($stop->attributes->{"pct:vol"}) == true) {
				$vals['percentage'] = $stop->attributes->{"pct:vol"};
			}
			if (isset($stop->attributes->unit) == true) {
				$vals['unit'] = $stop->attributes->unit;
			}
			$points[] = array(
					$vals['title'],
					$vals['placename'],
					$vals['address'],
					$vals['description'],
					$vals['percentage'],
					$vals['youtube:title'],
					$vals['youtube:link'],
					$vals['flickr:setid'],
					$vals['qty'],
					$vals['co2e'],
					$vals['color']
				);	
		}
		
		$stopswriter->setActiveSheetIndex(0);
		foreach ($ucolumns as $num=>$column) {
			$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($num,1,$column);
		}
		$stopswriter->setActiveSheetIndex(1);
		foreach ($dcolumns as $num=>$column) {
			$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($num,1,$column);
		}
		
		// Done! Open on the Client side
		
		$sWriter = new PHPExcel_Writer_Excel5($stopswriter);	
        //$writer->setPreCalculateFormulas(true);
        $request = Request::instance();
        $request->headers['Content-Type'] = "application/excel";
        $request->headers['Content-Disposition'] = 'attachment;filename="sourcemap.xls"';
        $request->headers['Cache-Control'] = 'max-age=0';
        $request->send_headers();
        $sWriter->save('php://output');
		
	}
	
}


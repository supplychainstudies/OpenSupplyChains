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
	
	public static function addChildren($supplychain, $stop, $direction, $hierarchy) {
		foreach($supplychain->hops as $hop) {
			if ($direction = "upstream") {
				if ($stop == $hop->to_stop_id) {
					$hierarchy[$hop->from_stop_id] = array("level"=>$hierarchy[$stop]['level']+1,"row"=>$hierarchy[$stop]['row']+1);
					foreach ($hierarchy as $st=>$h) {
						if ($h['row']>=$hierarchy[$hop->from_stop_id]['row']) {
							$hierarchy[$st]['row']++;
						}
					}
					$hierarchy = addChildren($supplychain, $hop->from_stop_id, $direction, $hierarchy);
				}
			}
			if ($direction = "downstream") {
				if ($stop == $hop->from_stop_id) {
					$hierarchy[$hop->to_stop_id] = array("level"=>$hierarchy[$stop]['level']+1,"row"=>$hierarchy[$stop]['row']+1);
					foreach ($hierarchy as $st=>$h) {
						if ($h['row']>=$hierarchy[$hop->to_stop_id]['row']) {
							$hierarchy[$st]['row']++;
						}
					}
					$hierarchy = addChildren($supplychain, $hop->from_stop_id, $direction, $hierarchy);
				}
			}
		}
		
		return $hierarchy;
	}
	
	
	public static function findDivergence($stopid, $supplychain, $stop_tally) {
		//find the next stop
		if (isset($stop_tally[$stopid]['from']) == true) {
			if ($stop_tally[$stopid]['from'] > 1) {
				return $stopid;
			} else {
				if (isset($stop_tally[$stopid]['next']) == true) {
					return self::findDivergence($stop_tally[$stopid]['next'], $supplychain, $stop_tally);
				} else {
					return $stopid;
				}
			}
		} elseif (isset($stop_tally[$stopid]['from']) == false && isset($stop_tally[$stopid]['to']) == true) {
			return $stopid;
		} 
		/*
		if (isset($stop_tally[$stopid]['next']) == true) {
			if (isset($stop_tally[$stop_tally[$stopid]['next']]['from']) == true) {
				if ($stop_tally[$stop_tally[$stopid]['next']]['from'] > 1) {
					return $stop_tally[$stopid]['next'];
				} else {
					self::findDivergence($stop_tally[$stopid]['next'], $supplychain, $stop_tally);
				}
			} else {
				return $stopid;
			}
		} else {
			return $stopid;
		}
		*/
	}
	
	public static function traverseForward($stopid, $tier, $supplychain, $tree) { 
		array_push($tree, array("id"=>$stopid, "tier"=>$tier));		
		// Find all the next stops
		foreach ($supplychain->hops as $hop) {
			if ($stopid == $hop->from_stop_id) {
				$tree = self::traverseForward($hop->to_stop_id, $tier+1, $supplychain, $tree);
			}
		}
		return $tree; 
	}
	
	public static function traverseBackward($stopid, $tier, $supplychain, $tree ) { 
		array_push($tree, array("id"=>$stopid, "tier"=>$tier));		
		// Find all the next stops
		foreach ($supplychain->hops as $hop) {
			if ($stopid == $hop->to_stop_id) {
				$tree = self::traverseBackward($hop->from_stop_id, $tier+1, $supplychain, $tree);
			}
		}
		return $tree; 
	}
	
	public static function getStopInfo($id, $supplychain) {
		foreach ($supplychain->stops as $stop) {
			if ($id == $stop->id) {
				return $stop;
			}
		}
	}
	
	public static function make($supplychain) {
	
		// We have to figure out the paths/tiers
		// To do that, we can traverse hops and push stuff onto paths
		$tree = array();
		// Upstream Hierarchy
		$uh = array();
		// Downstream Hierarchy
		$dh = array();
		$umax_level = 0;
		$dmax_level = 0;
		/*
		foreach($supplychain->hops as $hop) {
			// http://192.168.1.39/services/supplychains/211?f=xls
			// If it is upstream
			if(isset($dh[$hop->from_stop_id]) == false && isset($uh[$hop->from_stop_id]['f']) == false && isset($uh[$hop->from_stop_id]) == false) {	
				if(isset($uh[$hop->to_stop_id]) == false) {
					$uh[$hop->to_stop_id] = array('level'=>0, 'row'=>count($uh)+1);
				}		
				$uh[$hop->from_stop_id] = array('level'=>($uh[$hop->to_stop_id]['level']+1), 'row'=>$uh[$hop->to_stop_id]['row']+1);
				$umax_level = max($umax_level, ($uh[$hop->to_stop_id]['level']+1));
				foreach ($uh as $num=>$st) {
					if ($st['row'] >= $uh[$hop->from_stop_id]['row'] && $num != $hop->from_stop_id) {
						$uh[$num]['row']++;
					}
				} 
			} elseif (isset($dh[$hop->from_stop_id]) == false && isset($uh[$hop->from_stop_id]['f']) == false && isset($uh[$hop->from_stop_id]) == true) {
				$uh[$hop->from_stop_id]['f'] = "f";
				// for from and every child of from, move it over a level
				$leveltrip = $uh[$hop->from_stop_id]['level'];
				$level = $leveltrip+1;
				$uh[$hop->from_stop_id]['level']++;
				for ($i=$uh[$hop->from_stop_id]['row']+1; $level>$leveltrip && $i < count($uh); $i++) {
					foreach ($uh as $num=>$st) {
						if ($uh[$num]['row'] == $i) {
							if ($uh[$num]['level']>$leveltrip) {
								$level = $uh[$num]['level'];
								$uh[$num]['level']++;
							}
							break 1;
						}
					}
				}
				if(isset($uh[$hop->to_stop_id]) == false) {
					$uh[$hop->to_stop_id] = array('level'=>0, 'row'=> $uh[$hop->from_stop_id]['row']);
					foreach ($uh as $num=>$st) {
						if ($st['row'] >= $uh[$hop->to_stop_id]['row'] && $num != $hop->to_stop_id) {
							$uh[$num]['row']++;
						}
					}					
				}				
			} else {
                //  First, check if the to point is already there 
	            //  Pop it in the downstream series
				if(isset($dh[$hop->from_stop_id]) == false) {
					$dh[$hop->from_stop_id] = array('level'=>0, 'row'=>count($dh)+1);
				
					// Anything that is a parent needs to be made into a child on downstream sheet
					$level = 3;
					$start =$uh[$hop->from_stop_id]['row']-1;
					// travel up the rows from where fromstopid is to grab all the parents
					for ($r = $start; $level>0 && $r>0; $r--) {
						// loop through all the upstream rows to find the one that is row-1
						foreach ($uh as $num=>$st) {
							// If this is the previous row
							if ($uh[$num]['row'] == $r) {
								// If the stop isn't already in the downstream sheet, put it in at the bottom
								if (isset($dh[$num]) == false) {
									$level = abs($uh[$hop->from_stop_id]['level'] - $st['level']);
									$dh[$num] = array('level'=>$level, 'row'=>count($dh)+1);
									foreach ($uh as $num2=>$st2) { 
					                    if ($uh[$num2]['row'] >= $uh[$num]['row']) { 
					                        $uh[$num2]['row']--; 
					                    } 
					                }
								} else {
								// if the stop is in there already, grab it and everything between it and the next row at the same level (that would be all of its children) and move it and its children to the bottom
									$r2 = $dh[$num]['row'];
									// The loop should run till the level of the current stop in dh is leveltrip
									$leveltrip = $dh[$num]['level'];
									$level2 = $leveltrip+1;
									while ($level2>$leveltrip) {
										foreach ($dh as $num2=>$st2) {
											if ($st2['row'] == $r2) {
												$level2 = $dh[$num2]['level'];
												$dh[$num2]['level'] = $dh[$num]['level']++;
												$dh[$num2]['row'] = count($dh)+1;
												foreach ($uh as $num3=>$st3) { 
								                    if ($st3['row'] >= $r2) { 
								                        $uh[$num3]['row']--; 
								                    } 
								                }
												break 1;		
											}
										}
									} 
									$level = 0;									
								}
								unset($uh[$num]);
								break 1;	
							}
						}
					}
					// we have to loop and pull stuff back levels
					
				}
				// If the to stop isn't there, put it in underneath the from stop and move everything else down
				if (isset($dh[$hop->to_stop_id]) == false) {
					$dh[$hop->to_stop_id] = array('level'=>($dh[$hop->from_stop_id]['level']+1), 'row'=>$dh[$hop->from_stop_id]['row']+1);						
					$dmax_level = max($dmax_level, ($dh[$hop->to_stop_id]['level']+1));
					foreach ($dh as $num=>$st) {
						if ($st['row'] >= $dh[$hop->to_stop_id]['row'] && $num != $hop->to_stop_id) {
							$dh[$num]['row']++;
						}
					}
				} else {
					// If the to stop is in there, move it and its children underneath the from stop and move everything else up a couple of rows
					$r = $dh[$hop->to_stop_id]['row'];
					$leveltrip = $dh[$hop->to_stop_id]['level'];
					//$maxloops = count($dh)- $r +1;
					//$maxcount = 0;
					$level = $leveltrip+1;
					while ($level>$leveltrip && $maxcount<$maxloops) {
						foreach ($dh as $num=>$st) {
							if ($st['row'] == $r) {
								$level = $dh[$num]['level'];
								$dh[$num]['level']++;
								$dh[$num]['row'] = count($dh) +1;
								foreach ($uh as $num2=>$st2) { 
				                    if ($st2['row'] >= $r) { 
				                        $uh[$num2]['row']--; 
				                    } 
				                }
								$maxcount++;
								break 1;		
							}
						}
					}
				}
			}
			/*
			var_dump($hop->from_stop_id);
			var_dump($hop->to_stop_id);
			var_dump($uh);
			var_dump($dh);
			
		}	
		*/
		$uh = array();
		$dh = array();
		$stop_tally = array();
		foreach($supplychain->hops as $hop) {
			if (isset($stop_tally[$hop->to_stop_id]['to']) == false) {
				$stop_tally[$hop->to_stop_id]['to'] = 1;
			} else {
				$stop_tally[$hop->to_stop_id]['to']++;
			}
			if (isset($stop_tally[$hop->from_stop_id]['from']) == false) {
				$stop_tally[$hop->from_stop_id]['from'] = 1;
			} else {
				$stop_tally[$hop->from_stop_id]['from']++;
			}
			$stop_tally[$hop->from_stop_id]['next'] = $hop->to_stop_id;	
		}
		$middles = array();
		$downstream_tree = array();
		$upstream_tree = array();
		foreach($supplychain->stops as $stop) {
			if (isset($stop_tally[$stop->id]) == true) {
				// then, this is a starting point in the tree
				if (isset($stop_tally[$stop->id]['to']) == false && isset($stop_tally[$stop->id]['from']) == true){
					// iterate through and find the first spot where it diverges
					$middles[] = self::findDivergence($stop->id, $supplychain, $stop_tally);
				} 
			} else {
				$upstream_tree[] = array("id"=>$stop->id, "tier"=>0);
			}
		}
		$middles = array_unique($middles);
		foreach($middles as $middle) {
			$downstream_tree = self::traverseForward($middle,0, $supplychain, $downstream_tree);
			$upstream_tree = self::traverseBackward($middle,0, $supplychain, $upstream_tree);
		}
		$ucolumns = array();
		$dcolumns = array();
		foreach ($upstream_tree as $h) {
			if (isset($ucolumns[$h['tier']]) == false) {
				$ucolumns[$h['tier']] = "Tier ".$h['tier'];
			}
		}
		foreach ($downstream_tree as $h) {
			if (isset($dcolumns[$h['tier']]) == false) {
				$dcolumns[$h['tier']] = "Tier ".$h['tier'];
			}
		}
		
		// If there's only 1 tier in the downstream sheet, it means that we dont need one
		if (count($dcolumns) == 1) {
			unset($downstream_tree);
		}
		
		$stopswriter = new PHPExcel();
		if (isset($upstream_tree) == true){
			$stopswriter->createSheet();
			$stopswriter->setActiveSheetIndex(0);
			$stopswriter->getActiveSheet()->setTitle("Upstream");
			foreach ($upstream_tree as $num=>$row) {
				$stop = self::getStopInfo($row['id'], $supplychain);
				if (isset($stop->attributes->title) == true) {
					$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($row['tier'],$num+2,$stop->attributes->title);
				}
			
				foreach ($stop->attributes as $attribute_name=>$attribute_value) {
					if (in_array($attribute_name,$ucolumns) == false) {
						$ucolumns[] = $attribute_name;
					}
					$stopswriter->getActiveSheet()->setCellValueByColumnAndRow(array_search($attribute_name,$ucolumns),$num+2,$attribute_value);					
				} 
			}
			foreach ($ucolumns as $num=>$column) {
				$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($num,1,$column);
			}	
		}
		if (isset($downstream_tree) == true) {	
			$stopswriter->createSheet();
			$stopswriter->setActiveSheetIndex(1);
			$stopswriter->getActiveSheet()->setTitle("Downstream");
			foreach ($downstream_tree as $num=>$row) {
				$stop = self::getStopInfo($row['id'], $supplychain);
				if (isset($stop->attributes->title) == true) {
					$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($row['tier'],$num+2,$stop->attributes->title);
				}
			
				foreach ($stop->attributes as $attribute_name=>$attribute_value) {
					if (in_array($attribute_name,$dcolumns) == false) {
						$dcolumns[] = $attribute_name;
					}
					$stopswriter->getActiveSheet()->setCellValueByColumnAndRow(array_search($attribute_name,$dcolumns),$num+2,$attribute_value);					
				} 
			} 
			foreach ($dcolumns as $num=>$column) {
				$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($num,1,$column);
			}
		}
		/*
		foreach($supplychain->stops as $stop) {
			if (isset($stop_tally[$stop->id]) == true) {
				if (isset($stop_tally[$stop->id]['to']) == true && isset($stop_tally[$stop->id]['from']) == true){
					if ($stop_tally[$stop->id]['from']>1 && $stop_tally[$stop->id]['to']>1) {
						$dh[$stop->id] = array("level"=>0,"row"=>count($dh)+1);
						$uh[$stop->id] = array("level"=>0,"row"=>count($uh)+1);
						$uh = $this->addChildren($supplychain, $stop->id, "upstream", $uh);
						$dh = $this->addChildren($supplychain, $stop->id, "downstream", $dh);
					}
				}
			} else {
				$dh[$stop->id] = array("level"=>0,"row"=>count($dh)+1);
			}
		}
		$ucolumns = array();
		$dcolumns = array();
		foreach ($uh as $h) {
			if (isset($ucolumns[$h['level']]) == false) {
				$ucolumns[$h['level']] = "Tier ".$h['level'];
			}
		}
		foreach ($dh as $h) {
			if (isset($dcolumns[$h['level']]) == false) {
				$dcolumns[$h['level']] = "Tier ".$h['level'];
			}
		}
		*/
		/*
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
		*/

		$sWriter = new PHPExcel_Writer_Excel5($stopswriter);	
        //$writer->setPreCalculateFormulas(true);
        $request = Request::instance();
        $request->headers['Content-Type'] = "application/excel";
        $request->headers['Content-Disposition'] = 'attachment;filename="sourcemap.xls"';
        $request->headers['Cache-Control'] = 'max-age=0';
        $request->send_headers();
        $sWriter->save('php://output');
		// Done! Open on the Client side
				
	}
	
}


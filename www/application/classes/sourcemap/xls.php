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
			// http://192.168.1.39/services/supplychains/149?f=xls
			//$found = false;
			/*
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
			} */
			
			
			if(isset($uh[$hop->from_stop_id]) == false) {	
				if(isset($uh[$hop->to_stop_id]) == false) {
					$uh[$hop->to_stop_id] = array('level'=>0, 'row'=>count($uh)+1);
				}		
				$uh[$hop->from_stop_id] = array('level'=>($uh[$hop->to_stop_id]['level']+1), 'row'=>$uh[$hop->to_stop_id]['row']+1);
				$umax_level = max($umax_level, ($uh[$hop->to_stop_id]['level']+1));
				foreach ($uh as $num=>$st) {
					if ($st['row'] >= $uh[$hop->from_stop_id]['row']) {
						$uh[$num]['row']++;
					}
				} 
			} else {
				// Has to be downstream. Pop it in there, man
				if(isset($dh[$hop->from_stop_id]) == false) {
					$dh[$hop->from_stop_id] = array('level'=>0, 'row'=>count($dh)+1);
				}
				$dh[$hop->to_stop_id] = array('level'=>($dh[$hop->from_stop_id]['level']+1), 'row'=>$dh[$hop->from_stop_id]['row']+1);
				$dmax_level = max($dmax_level, ($dh[$hop->from_stop_id]['level']+1));
				foreach ($dh as $num=>$st) {
					if ($st['row'] >= $dh[$hop->to_stop_id]['row']) {
						$dh[$num]['row']++;
					}
				}
			}
			/*
			else {	
				$stops_hierarchy[$hop->to_stop_id] = array('level'=>0, 'row'=>count($stops_hierarchy));
			} */
			//var_dump($stops_hierarchy);
		}
		
		
		//var_dump($stops_hierarchy);
		/*
        $points = array(
        1 => array()
		); 
		for($i=0;$i<=$max_level;$i++) {
			$points[1][] = "Tier ". $i;
		}
		array_push($points[1],
			"Location",
			"Address",
			"Description",
			"Percentage",
			"youtube:title",
			"youtube:link",
			"flickr:setid",
			"qty",
			"CO2e",
			"CO2e-Reference",
			"Water",
			"Water-Reference",
			"Energy",
			"Energy-Reference",
			"color",
			"size"
			);
		*/
		
		
		
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
				
				/*
				$stops_hierarchy[$stop->id]['level'];
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
			
			foreach($supplychain->hops as $hop) {
				$vals = array(
					"from" => "",
					"to" => "",	
					"description" => "",	
					"color" => "",	
					"transport" => "",	
					"qty" => "",	
					"unit" => "",	
					"co2e" => ""
				);
		
				if (isset($hop->from_stop_id) == true && isset($hop->to_stop_id) == true) {
					$vals['from'] = $hop->from_stop_id;
					$vals['to'] = $hop->to_stop_id;
					//$vals['from'] = $ftr->geometry->coordinates[0][0].", ".$ftr->geometry->coordinates[0][1];
					//$vals['to'] = $ftr->geometry->coordinates[1][0].", ".$ftr->geometry->coordinates[1][1];
				}
				if (isset($hop->attributes->description) == true) {
					$vals['description'] = $hop->attributes->description;
					if ($hop->attributes->description != "") {
						// Lotus thinks that this should look like "From - To" if its blank
					}
				} 
				if (isset($hop->attributes->color) == true) {
					$vals['color'] = $hop->attributes->color;
				}
				if (isset($hop->attributes->qty) == true) {
					$vals['qty'] = $hop->attributes->qty;
				}
				if (isset($hop->attributes->unit) == true) {
					$vals['unit'] = $hop->attributes->unit;
				}		
				if (isset($hop->attributes->co2e) == true) {
					$vals['co2e'] = $hop->attributes->co2e;
				}		
				if (isset($hop->attributes->{"youtube-url"}) == true) {
					$vals['youtube:link'] = $hop->attributes->{"youtube-url"};
				}
				if (isset($hop->attributes->{"youtube-title"}) == true) {
					$vals['youtube:title'] = $hop->attributes->{"youtube-title"};
				}
				if (isset($hop->attributes->{"flickr-setid"}) == true) {
					$vals['flickr:setid'] = $hop->attributes->{"flickr-setid"};
				}
			    $hops[] = array(
						$vals['from'],
						$vals['to'],
						$vals["description"],	
						$vals['color'],	
						"",	
						$vals['qty'],
						$vals['unit'],
						$vals['co2e']
					);
			}
		
		
		$spreadsheet_attributes = array(
	        'author'       => 'Sourcemap Incorporated',
	        'title'        => $supplychain->attributes->title,
	        'subject'      => $supplychain->attributes->title,			
		);
		if (isset($supplychain->attributes->description) == true) {
			$spreadsheet_attributes['description'] = $supplychain->attributes->description;
		}
		
		$ws = new Spreadsheet($spreadsheet_attributes);

        $ws->set_active_sheet(0);
        $as = $ws->get_active_sheet();
        $as->setTitle('Stops');
        $as->getDefaultStyle()->getFont()->setSize(12);
        $as->getColumnDimension('A')->setWidth(20); // Name
        $as->getColumnDimension('B')->setWidth(20); // Location
        $as->getColumnDimension('C')->setWidth(20); // Address
        $as->getColumnDimension('D')->setWidth(20); // Description
        $as->getColumnDimension('E')->setWidth(7); // Percentage
        $as->getColumnDimension('F')->setWidth(20); // youtube:title
        $as->getColumnDimension('G')->setWidth(20); // youtube:link
        $as->getColumnDimension('H')->setWidth(20); // flickr:setid
        $as->getColumnDimension('I')->setWidth(10); // qty
        $as->getColumnDimension('J')->setWidth(10); // CO2e
        $as->getColumnDimension('K')->setWidth(10); // Color
        $as->getColumnDimension('L')->setWidth(10); // Size
        $as->getColumnDimension('M')->setWidth(20); // Code
        // Working Validator - Leo doesnt want anymore //$as->getColumnDimension('M')->setVisible(false); // Make To invisible
        // Percentage
        // Working Validator - Leo doesnt want anymore //$ws->set_column_validation('E',array(0,100), "DECIMAL", "Entry", "Enter a number between 0 and 100","Invalid Entry", "Enter a number between 0 and 100");
        // Change E so that its in between 0 and 100    
        // Working Validator - Leo doesnt want anymore //$ws->set_column_validation('G','IF(MID(cell,1,7)="http://", TRUE, FALSE)', "CUSTOM", "Enter a URL", "Enter a valid website address in a similar format to 'http://www.example.com'.", "Enter a URL", "Enter a valid website address in a similar format to 'http://www.example.com'.");
        // Working Validator - Leo doesnt want anymore //$ws->set_column_validation('I',"", "DECIMAL", "Entry", "Enter a number","Invalid Entry", "Enter a number");        
        // Working Validator - Leo doesnt want anymore //$ws->set_column_validation('J',"", "DECIMAL", "Entry", "Enter a number","Invalid Entry", "Enter a number");
        // lock I (Size)        
        // Working Validator - Leo doesnt want anymore //$ws->set_column_validation('K','=IF(LEN(cell)=7,IF(MID(cell,1,1)="#",IF(ISERROR(HEX2DEC(MID(cell,2,6)))=FALSE,TRUE,FALSE),FALSE),FALSE)',"CUSTOM","Here you must put a hexadecimal color value preceded by the pound sign (for example, '#DFDFDF'). You can use a color picker like (http://www.colorpicker.com/) to get these values.", "Incorrect Input","That is not a hexadecimal color value. Refer to a tool like (http://www.colorpicker.com/) to get a value in the form '#DFDFDF'");
        // Add All the points to the Sheet
        $ws->set_data($points, false);
        // Add a unique name row
        // Working Validator - Leo doesnt want anymore //$ws->set_column_formula('M', '=IF(Acounter<>"","#"&Acounter& " - "&Bcounter&"","")');

        // Calculate the size column
        // Working Validator - Leo doesnt want anymore //$ws->set_column_formula('L', '=IF(isNumber(Ecounter)=TRUE,SQRT((MIN(100,MAX(10,Ecounter)))/3.14),"")');
        // set M to apply formula
        //$ws->freezeTopRow();

        // HOPS       
        $ws->create_active_sheet();
        $as = $ws->get_active_sheet();
        $as->setTitle('Hops');
        $as->getDefaultStyle()->getFont()->setSize(12);
        $as->getColumnDimension('A')->setWidth(20); // From
        $as->getColumnDimension('B')->setWidth(20); // To
        // Working Validator - Leo doesnt want anymore //$as->getColumnDimension('C')->setWidth(20); // Origin Point (From)
        // Working Validator - Leo doesnt want anymore //$as->getColumnDimension('D')->setWidth(20); // Destination Point (To)
        // Working Validator - Leo doesnt want anymore //$as->getColumnDimension('A')->setVisible(false); // Make From invisible
        // Working Validator - Leo doesnt want anymore //$as->getColumnDimension('B')->setVisible(false); // Make To invisible
        $as->getColumnDimension('C')->setWidth(20); // Description
        $as->getColumnDimension('D')->setWidth(20); // Color
        $as->getColumnDimension('E')->setWidth(20); // Transport
        $as->getColumnDimension('F')->setWidth(20); // Qty
        $as->getColumnDimension('G')->setWidth(20); // Unit
        $as->getColumnDimension('H')->setWidth(20); // CO2e

		$ws->set_data($hops, false);
		
		// Set the Workbook to the first sheet
		$ws->set_active_sheet(0);
		$title = "A Sourcemap";
		if (isset($supplychain->attributes->title) == true) {
			$title = $supplychain->attributes->title;
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
		*/
	}
	
}


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

class Sourcemap_Import_Hviz extends Sourcemap_Import_Xls{
    public static $default_options = array(
        'headers' => true, 'latcol' => null,
        'loncol' => null, 'addresscol' => null,
        'idcol' => null, 'fromcol' => null,
        'tocol' => null
    );

	public static function returnSize($val) {
		$min = 30;
		$max = 365;
		$percent_min = 100*($min/$max);
		$percent_max = 100*($max/$max);
		$percent_val = 100*($val/$max);
		//sqrt((min(array(max(array(($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue()), 10)), 365)))/3.14)
		return sqrt((min(array(max(array(($percent_val), $percent_min)), $percent_max)))/3.14);
	}
	

    public static function hviz2sc($xls=null, $o=array()) {
        $options = array();
        if(!is_array($o)) $o = (array)$o;
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);
        $sc = new stdClass();

		//get upstream sheet
		$contentReader = new PHPExcel_Reader_Excel5Contents();
		$contentReader->setReadDataOnly(true);
		$contentPHPExcel = $contentReader->loadContents($xls);
		$sheets = $contentPHPExcel->getSheetNames();
		$maxvarort = 0;
		// If you have an upstream template
		$tier_start = 0;
		$tier_end = 0;
		$stops = array();
		$hops = array();
		$forecast_sheet = "";
		$upstream_sheet = "";
		$downstream_sheet = "";
		$recovery_sheet = "";
		foreach($sheets as $key => $val) {
			if(strpos(strtolower($val), "forecast") !== false) 
				$forecast_sheet = $val;
			elseif(strpos(strtolower($val), "upstream") !== false)
				$upstream_sheet = $val;
			elseif(strpos(strtolower($val), "downstream") !== false)
				$downstream_sheet = $val;	
			elseif(strpos(strtolower($val), "recovery") !== false)
				$recovery_sheet = $val;			
		}
		// Get Forecast Info
		if ($forecast_sheet != "") {
			$contentReader->setLoadSheetsOnly($forecast_sheet);
			$contentPHPExcel = $contentReader->loadContents($xls);
			$fh = array();
			$forecast_values = array();
			$rows = $contentPHPExcel->getActiveSheet();
			// Figure out where the title row is
			for ($starting_row = 1; $starting_row<20; $starting_row++) {
				for ($i = 0; $i<20; $i++) {
					$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
					if (strpos($value,"part") !== false) {	
						break 2; 
					}
				}
			}
			for ($i = $i; $rows->cellExistsByColumnAndRow($i,$starting_row) == true; $i++) {
				$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
				$column = $rows->getCellByColumnAndRow($i,$starting_row)->getColumn();
				if (strpos($value,"part") !== false)
					$fh["part"] = $column;
				elseif (strpos($value,"forecast") !== false)
					$fh["forecast"] = $column;
			}
			foreach ($rows->getRowIterator() as $row) {
				$rowIndex = $row->getRowIndex();
				if ($rowIndex > $starting_row && $rows->getCell($fh["forecast"] . $rowIndex)->getCalculatedValue() != NULL) {
					$forecast_values[$rows->getCell($fh["part"] . $rowIndex)->getCalculatedValue()] = $rows->getCell($fh["forecast"] . $rowIndex)->getCalculatedValue(); 
				}
			}
			if (count($forecast_values) == 0) { 
				unset($forecast_values); 
			}
		}
		// Get Recovery Info
		if ($recovery_sheet != "") {
			$contentReader->setLoadSheetsOnly($recovery_sheet);
			$contentPHPExcel = $contentReader->loadContents($xls);
			$recovery_values = array();
			$rh = array();
			$rows = $contentPHPExcel->getActiveSheet();
			// Figure out where the title row is
			for ($starting_row = 1; $starting_row<20; $starting_row++) {
				for ($i = 0; $i<20; $i++) {
					$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
					if (strpos($value,"days") !== false) {	
						break 2; 
					}
				}
			}
			for ($i = 1; $rows->cellExistsByColumnAndRow($i,$starting_row) == true; $i++) {
				$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
				$column = $rows->getCellByColumnAndRow($i,$starting_row)->getColumn();
				if (strpos($value,"days") !== false)
					$rh["days"] = $column;
				elseif (strpos($value,"origin") !== false)
					$rh["origin"] = $column;
				elseif (strpos($value,"material") !== false)
					$rh["material"] = $column;
			}
			foreach ($rows->getRowIterator() as $row) {
				$rowIndex = $row->getRowIndex();
				if ($rowIndex > $starting_row && $rows->getCell($rh["days"] . $rowIndex)->getCalculatedValue() != NULL) {
					if ($rh["material"] != "" && $rh["origin"] != "") {
						if ($rows->getCell($rh["material"] . $rowIndex)->getCalculatedValue() != "*") {
							$recovery_values[$rows->getCell($rh["origin"] . $rowIndex)->getCalculatedValue()."-".$rows->getCell($rh["material"] . $rowIndex)->getCalculatedValue()] = $rows->getCell($rh["days"] . $rowIndex)->getCalculatedValue(); 
						} else {
							$recovery_values[$rows->getCell($rh["origin"] . $rowIndex)->getCalculatedValue()] = $rows->getCell($rh["days"] . $rowIndex)->getCalculatedValue(); 
							
						}
					} else {
						$recovery_values[$rows->getCell($rh["origin"] . $rowIndex)->getCalculatedValue()] = $rows->getCell($rh["days"] . $rowIndex)->getCalculatedValue(); 
					}
				}
			}
			if (count($recovery_values) == 0) { 
				unset($recovery_values); 
			}
		}
		var_dump($recovery_values);
		if ($upstream_sheet != "") {
			$contentReader->setLoadSheetsOnly($upstream_sheet);
			$contentPHPExcel = $contentReader->loadContents($xls);
			$rows = $contentPHPExcel->getActiveSheet();
		
			// Figure out where all the columns are
			$h = array(
				"BOM-Level" => "",
				"Part-Name"	=> "",
				"Part-Number"	=> "",
				"Description" => "",	
				"Qty" => "",	
				"Unit" => "",	
				"Source-Name" => "",	
				"Source-Split" => "",	
				"Street Address",
				"City" => "",	
				"Country" => "",	
				"Postal-Code" => "",	
				"Latitude" => "",	
				"Longitude" => "",	
				"Risk Recovery Days" => ""	
			);
			
			//$description = array();
			// Find title row
			for ($starting_row = 1; $starting_row < 20; $starting_row++) {
				for ($i = 0; $i<20; $i++) {
					if ($rows->cellExistsByColumnAndRow($i,$starting_row) == true){
						$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
						$column = $rows->getCellByColumnAndRow($i,$starting_row)->getColumn();
						if (strpos($value,"bom") !== false) {	
							break 2; 
						}
					}
				}
			}
			for ($i = 0; $rows->cellExistsByColumnAndRow($i,$starting_row) == true; $i++) {
				$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
				$column = $rows->getCellByColumnAndRow($i,$starting_row)->getColumn();
				if (strpos($value,"part-name") !== false || strpos($value,"part name") !== false)
					$h["Part-Name"] = $column;
				elseif (strpos($value,"part") !== false && (strpos($value,"num") !== false || strpos($value,"#") !== false))
					$h["Part-Number"] = $column;
				elseif (strpos($value,"bom-level") !== false || strpos($value,"bom level") !== false )
					$h["BOM-Level"] = $column;
				elseif (strpos($value,"descr") !== false)
					$h["Description"] = $column;
				elseif (strpos($value,"qty") !== false || strpos($value,"quantity") !== false)	
					$h["Qty"] = $column;
				elseif (strpos($value,"unit") !== false || strpos($value,"uom") !== false)
					$h["Unit"] = $column;
				elseif (strpos($value,"name") !== false)
					$h["Source-Name"] = $column;
				elseif (strpos($value,"split") !== false)
					$h["Source-Split"] = $column;
				elseif (strpos($value,"street") !== false)	
					$h["Street Address"] = $column;
				elseif (strpos($value,"city") !== false)	
					$h["City"] = $column;
				elseif (strpos($value,"country") !== false)	
					$h["Country"] = $column;
				elseif (strpos($value,"postal") !== false || strpos($value,"zip") !== false)
					$h["Postal-Code"] = $column;
				elseif (strpos($value,"lat") !== false)	
					$h["Latitude"] = $column;
				elseif (strpos($value,"lon") !== false)	
					$h["Longitude"] = $column;
				elseif (strpos($value,"time") !== false)
					$h["Risk Recovery Days"] = $column;
			}
			if ($h["Part-Number"] == "") { 
				$h["Part-Number"] = $h["Part-Name"]; 
			}
			$count = 1;
			$boms = array();
			foreach ($rows->getRowIterator() as $row) {
				$rowIndex = $row->getRowIndex();
				if ($rowIndex>$starting_row) {
					$uuid = $rows->getCell($h["Source-Name"] . $rowIndex)->getCalculatedValue() . " (" . $rows->getCell($h["City"] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h["Country"] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h["Postal-Code"] . $rowIndex)->getCalculatedValue() . ")";
					if (isset($stops[$uuid]) == false) {						
						$stops[$uuid] = array (
								'num' => $count,
								'Name' =>  $rows->getCell($h["Source-Name"] . $rowIndex)->getCalculatedValue(),	
								'Location' => $rows->getCell($h["City"] . $rowIndex)->getCalculatedValue(), 
								'Address' => $rows->getCell($h["City"] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h["Country"] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h["Postal-Code"] . $rowIndex)->getCalculatedValue(),	
								'Description' => "",	
								'color' => "#1B49E0",
								'lat' => "",
								'long' => "",
								'varort' => "",
								'partnumbers' => array($rows->getCell($h["Part-Number"] . $rowIndex)->getCalculatedValue()),
								'parts' => array(array('name'=>$rows->getCell($h["Part-Name"] . $rowIndex)->getCalculatedValue(),'split'=>$rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue())),
								'tier' => $rows->getCell($h["BOM-Level"] . $rowIndex)->getCalculatedValue(),
								'days' => ""						
						);
						if ($h['Street Address'] != "") {
							$stops[$uuid]['Address'] = $rows->getCell($h["Street Address"] . $rowIndex)->getCalculatedValue() . " " . $stops[$uuid]['Address'];
						}
						if ($h['Risk Recovery Days'] != "") {
							if ($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue() != "") {
								$stops[$uuid]['days'] = $rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue();
							} else {
								if (isset($recovery_values) == true) {
									if (isset($recovery_values[$stops[$uuid]['Name']]) == true) {
										$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name']];
									} elseif (isset($recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['parts'][0]["name"]]) == true) {
										$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['parts'][0]["name"]];
									}
								}
							}
						} elseif (isset($recovery_values) == true) {
							if (isset($recovery_values[$stops[$uuid]['Name']]) == true) {
								$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name']];
							} elseif (isset($recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['Part-Name']]) == true) {
								$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['Part-Name']];
							}
						}
						$tier_start = min($rows->getCell($h["BOM-Level"] . $rowIndex)->getCalculatedValue(),$tier_start);
						$tier_end = max($rows->getCell($h["BOM-Level"] . $rowIndex)->getCalculatedValue(),$tier_end);
						if (isset($forecast_values) == true && isset($stops[$uuid]['days']) == true) {
							if ($rows->getCell($h['BOM-Level'] . $rowIndex)->getCalculatedValue() == 0) {
								if (isset($forecast_values[$stops[$uuid]['parts'][0]['name']]) == true) {
									$stops[$uuid]['varort'] = $forecast_values[$stops[$uuid]['parts'][0]['name']] * ($stops[$uuid]['days']/365) * $rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue();	
								} elseif (isset($forecast_values[$stops[$uuid]['partnumbers'][0]]) == true) {
									$stops[$uuid]['varort'] = $forecast_values[$stops[$uuid]['partnumbers'][0]] * ($stops[$uuid]['days']/365) * $rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue();	
								}
							} else {
								if (isset($boms[0]) == true && isset($stops[$uuid]['days']) == true) {
								foreach ($stops as $stop) {
										if ($stop['num'] == $boms[0]) {
											foreach ($stop['parts'] as $part) {
												foreach($forecast_values as $pn=>$vals) {
													if ($pn == $part['name']) {
														$bom0partvalue = $vals;
														$stops[$uuid]['varort'] = ($stops[$uuid]['days']/365) * ($rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue()) * ($vals);
													}
												}
											}
											foreach ($stop['partnumbers'] as $part) {
												foreach($forecast_values as $pn=>$vals) {
													if ($pn == $part) {
														$bom0partvalue = $vals;
														$stops[$uuid]['varort'] = ($stops[$uuid]['days']/365) * ($rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue()) * ($vals);

													}
												}
											}
										}
									}
								}
								
							}							
							//$stops[$uuid]['Description'] .= ($rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue()*100)."% of part ".$rows->getCell($h["Part-Name"] . $rowIndex)->getCalculatedValue()." ";
						}
						if (trim($stops[$uuid]['Address']) == "") {
							$stops[$uuid]['lat'] = "0";
							$stops[$uuid]['long'] = "0";
						}
						$count++;
					} else {
						// Make sure its the lowest tier
						$stops[$uuid]['tier'] = max($stops[$uuid]['tier'], $rows->getCell($h["BOM-Level"] . $rowIndex)->getCalculatedValue());
							if (isset($forecast_values) == true && isset($stops[$uuid]['days']) == true) {
								foreach ($stops as $stop) {
									if ($stop['num'] == $boms[0]) {
										foreach ($stop['parts'] as $part) {
											foreach($forecast_values as $pn=>$vals) {
												if ($pn == $part['name']) {
													$bom0partvalue = $vals;
													$stops[$uuid]['varort'] = $stops[$uuid]['varort'] + (($stops[$uuid]['days']/365) * ($rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue()) * ($vals));												
												}
											}
											foreach ($stop['partnumbers'] as $part) {
												foreach($forecast_values as $pn=>$vals) {
													if ($pn == $part) {
														$bom0partvalue = $vals;
														$stops[$uuid]['varort'] = $stops[$uuid]['varort'] + (($stops[$uuid]['days']/365) * ($rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue()) * ($vals));												
													}
												}
											}
										}
									}
								}					
							$stops[$uuid]["parts"][] = array('name'=>$rows->getCell($h["Part-Name"] . $rowIndex)->getCalculatedValue(),'split'=>$rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue());
						}	
					}
					if ($stops[$uuid]['varort'] != "") {
						$maxvarort = max($maxvarort, $stops[$uuid]['varort']);
					}
					$hops_from = $stops[$uuid]["num"];
					$hops_to = "";
					// Hops					
					$boms[$rows->getCell($h['BOM-Level'] . $rowIndex)->getCalculatedValue()] = $hops_from;
					if ($rows->getCell($h['BOM-Level'] . $rowIndex)->getCalculatedValue() != 0) {
						$hops_to = $boms[$rows->getCell($h['BOM-Level'] . $rowIndex)->getCalculatedValue() -1];
					}
					if ($hops_from != "" && $hops_to != "" and isset($hops[$hops_from."-".$hops_to]) == false && $hops_from != $hops_to) {
			
						$hops[$hops_from."-".$hops_to] = array(
							'From' => $hops_from,
							'To' => $hops_to,
							'Description' => '',
							'color'=>'#1B49E0'
						);			
						if (isset($forecast_values) == true) {		
							$hops[$hops_from."-".$hops_to]['varort'] = (7/365)* $rows->getCell($h['Source-Split'] . $rowIndex)->getCalculatedValue() * $bom0partvalue;
						}					
						foreach ($stops as $u=>$stop) {
							if ($stop["num"] == $hops_from) { 
								$hops[$hops_from."-".$hops_to]['Description'] .= "From: " . $stop['Location'];
								$hops[$hops_from."-".$hops_to]['fuuid'] = $u;
								continue;
							}
						}
						foreach ($stops as $u=>$stop) {
							if ($stop["num"] == $hops_to) { 
								$hops[$hops_from."-".$hops_to]['Description'] .= " To: " . $stop['Location'];
								$hops[$hops_from."-".$hops_to]['tuuid'] = $u;
								continue;
							}
						}
						
					} // 	if ($hops_from != "" && $hops_to != "" and isset($hops[$hops_from."-".$hops_to]) == false && $hops_from != $hops_to) {
				} // If line isn't blank, etc
			} // Foreach
		} // If theres a template 1
		


		/* 
		
		
		
		Now we have to parse through the downstream sheet and add it to the stops and hops
		
		The columns should be :
		
		Part name	Origin Name	Origin city	Origin Country	Origin Postal code	Origin Latitude	Origin Longitude	Destn Name	Destn City	Destn Country	Destn Postal code	Destn Latitude	Destn Longitude	Share Percent of Flow
		
		
		*/
		
		if ($downstream_sheet != "") {	
			$contentReader->setLoadSheetsOnly($downstream_sheet);
			$contentPHPExcel = $contentReader->loadContents($xls);
			$rows = $contentPHPExcel->getActiveSheet();
			// Figure out where all the columns are
			$h = array(
				"Part-Name" => "",
				"O-Name" => "",
				"O-Street Address" => "",
				"O-City" => "",	
				"O-Country" => "",	
				"O-Postal-Code" => "",	
				"O-Latitude" => "",	
				"O-Longitude" => "",	
				"D-Name" => "",	
				"D-Street Address" => "",
				"D-City" => "",	
				"D-Country" => "",	
				"D-Postal-Code" => "",	
				"D-Latitude" => "",	
				"D-Longitude" => "",	
				"flow" => "",
				"Risk Recovery Days" => ""		
			);
		for ($starting_row = 1; $starting_row<10; $starting_row++) {
			$check_title = false;
			$check_address = false;
			for ($i = 0; $i<20; $i++) {
				if ($rows->cellExistsByColumnAndRow($i,$starting_row) == true){
					$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
					$column = $rows->getCellByColumnAndRow($i,$starting_row)->getColumn();
					if (strpos($value,"location") !== false || strpos($value,"address") !== false || strpos($value,"coordinate") !== false) {	
						break 2; 
					}
				}
			}
		}
			for ($i = 0; $rows->cellExistsByColumnAndRow($i,$starting_row) == true; $i++) {
				$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getCalculatedValue());
				$column = $rows->getCellByColumnAndRow($i,$starting_row)->getColumn();
				if (strpos($value,"part-name") !== false || strpos($value,"part name") !== false || strpos($value,"part number") !== false || strpos($value,"part-number") !== false)
					$h["Part-Name"] = $column;
				elseif (strpos($value,"origin") !== false && strpos($value,"name") !== false)
					$h["O-Name"] = $column;
				elseif (strpos($value,"origin") !== false && strpos($value,"street") !== false)
					$h["O-Street Address"] = $column;
				elseif (strpos($value,"origin") !== false && strpos($value,"city") !== false)
					$h["O-City"] = $column;
				elseif (strpos($value,"origin") !== false && strpos($value,"country") !== false)
					$h["O-Country"] = $column;
				elseif (strpos($value,"origin") !== false && (strpos($value,"postal") !== false || strpos($value,"zip") !== false))
					$h["O-Postal-Code"] = $column;
				elseif (strpos($value,"origin") !== false && strpos($value,"lat") !== false)
					$h["O-Latitude"] = $column;
				elseif (strpos($value,"origin") !== false && strpos($value,"lon") !== false)
					$h["O-Longitude"] = $column;
				elseif (strpos($value,"dest") !== false && strpos($value,"name") !== false)
					$h["D-Name"] = $column;
				elseif (strpos($value,"dest") !== false && strpos($value,"street") !== false)
					$h["D-Street Address"] = $column;
				elseif (strpos($value,"dest") !== false && strpos($value,"city") !== false)
					$h["D-City"] = $column;
				elseif (strpos($value,"dest") !== false && strpos($value,"country") !== false)
					$h["D-Country"] = $column;
				elseif (strpos($value,"dest") !== false && (strpos($value,"postal") !== false || strpos($value,"zip") !== false))
					$h["D-Postal-Code"] = $column;
				elseif (strpos($value,"dest") !== false && strpos($value,"lat") !== false)
					$h["D-Latitude"] = $column;
				elseif (strpos($value,"dest") !== false && strpos($value,"lon") !== false)
					$h["D-Longitude"] = $column;
				elseif (strpos($value,"flow") !== false)
					$h["flow"] = $column;	
				elseif (strpos($value,"recovery-time") !== false || strpos($value,"days") !== false || strpos($value,"risk") !== false)
					$h["Risk Recovery Days"] = $column;			
			}
			$boms = array();
			foreach ($rows->getRowIterator() as $row) {
				$rowIndex = $row->getRowIndex();
				if ($rowIndex>$starting_row) {
					$uuid = $rows->getCell($h['O-Name'] . $rowIndex)->getCalculatedValue() . " (" . $rows->getCell($h['O-City'] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h['O-Country'] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h['O-Postal-Code'] . $rowIndex)->getCalculatedValue() . ")";

					if (isset($stops[$uuid]) == false) {
						$stops[$uuid] = array (
								'num' => $count,
								'Name' => $rows->getCell($h['O-Name'] . $rowIndex)->getCalculatedValue(),	
								'Location' => $rows->getCell($h['O-Name'] . $rowIndex)->getCalculatedValue() . " - " . $rows->getCell($h['O-City'] . $rowIndex)->getCalculatedValue(), 
								'Address' => $rows->getCell($h['O-City'] . $rowIndex)->getCalculatedValue() + " " + $rows->getCell($h['O-Country'] . $rowIndex)->getCalculatedValue() + " " + $rows->getCell($h['O-Postal-Code'] . $rowIndex)->getCalculatedValue(),	
								'Description' => $uuid,
								'color' =>	"#1B49E0",	
								'varort' => "",
								'tier' => ""
								
							);
							if ($h['Risk Recovery Days'] != "") {
								if ($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue() != "") {
									$stops[$uuid]['days'] = $rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue();
								} else {
									if (isset($recovery_values) == true) {
										if (isset($recovery_values[$stops[$uuid]['Name']]) == true) {
											$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name']];
										} elseif (isset($recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['parts'][0]["name"]]) == true) {
											$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['parts'][0]["name"]];
										}
									}
								}
							} elseif (isset($recovery_values) == true) {
								if (isset($recovery_values[$stops[$uuid]['Name']]) == true) {
									$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name']];
								} elseif (isset($recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['Part-Name']]) == true) {
									$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['parts'][0]["name"]];
								}
							}
							$stops[$uuid]['parts'][] = array('name'=>$rows->getCell($h['Part-Name'] . $rowIndex)->getCalculatedValue(), 'split'=> $rows->getCell($h['flow'] . $rowIndex)->getCalculatedValue());	
							$count++;
					} 
					$prev = $uuid;
					$hops_from = $stops[$uuid]["num"];					
					$uuid = $rows->getCell($h['D-Name'] . $rowIndex)->getCalculatedValue() . " (" . $rows->getCell($h['D-City'] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h['D-Country'] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h['D-Postal-Code'] . $rowIndex)->getCalculatedValue() . ")";

					if (isset($stops[$uuid]) == false) {
						$stops[$uuid] = array (
								'num' => $count,
								'Name' => $rows->getCell($h['D-Name'] . $rowIndex)->getCalculatedValue(),	
								'Location' => $rows->getCell($h['D-Name'] . $rowIndex)->getCalculatedValue(), 
								'Address' => $rows->getCell($h['D-City'] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h['D-Country'] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h['D-Postal-Code'] . $rowIndex)->getCalculatedValue(),	
								'Description' => $uuid,
								'color'=>'#1B49E0',
								'varort'=>'',
								'tier' => ($stops[$prev]['tier'] -1),
								'days' => "",
								'parts' => array(array("name"=>$rows->getCell($h['Part-Name'] . $rowIndex)->getCalculatedValue(), "split"=>$rows->getCell($h['flow'] . $rowIndex)->getCalculatedValue()))
							);
							if ($h['Risk Recovery Days'] != "") {
								if ($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue() != "") {
									$stops[$uuid]['days'] = $rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue();
								} else {
									if (isset($recovery_values) == true) {
										if (isset($recovery_values[$stops[$uuid]['Name']]) == true) {
											$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name']];
										} elseif (isset($recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['parts'][0]["name"]]) == true) {
											$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['parts'][0]["name"]];
										}
									}
								}
							} elseif (isset($recovery_values) == true) {
								if (isset($recovery_values[$stops[$uuid]['Name']]) == true) {
									$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name']];
								} elseif (isset($recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['parts'][0]["name"]]) == true) {
									$stops[$uuid]['days'] = $recovery_values[$stops[$uuid]['Name'] . "-" . $stops[$uuid]['parts'][0]["name"]];
								}
							}
							if (isset($forecast_values) == true && isset($stops[$uuid]['days']) == true) {	
								$bomval = $forecast_values[$rows->getCell($h['Part-Name'] . $rowIndex)->getCalculatedValue()];					
								$stops[$uuid]['varort'] = ($stops[$uuid]['days']/365) * $rows->getCell($h['flow'] . $rowIndex)->getCalculatedValue() * $bomval;
							} else {
									$stops[$uuid]['parts'][] = array('name'=>$rows->getCell($h['Part-Name'] . $rowIndex)->getCalculatedValue(), 'split'=> $rows->getCell($h['flow'] . $rowIndex)->getCalculatedValue());
							}
							if ($stops[$uuid]['varort'] != "") {
								$maxvarort = max($maxvarort, $stops[$uuid]['varort']);
							}
							$stops[$uuid]['parts'][] = array('name'=>$rows->getCell($h['Part-Name'] . $rowIndex)->getCalculatedValue(), 'split'=> $rows->getCell($h['flow'] . $rowIndex)->getCalculatedValue());	
							$tier_start = min($stops[$uuid]['tier'],$tier_start);
							$tier_end = max($stops[$uuid]['tier'],$tier_end);
							$count++;
					} 
					$hops_to = $stops[$uuid]["num"];
						$hops[$hops_from."-".$hops_to] = array(
							'From' => $hops_from,
							'To' => $hops_to,
							'Description' => "",
							'color'=>'#1B49E0'
						);
						if (isset($forecast_values) == true) {		
							$bomval = $forecast_values[$rows->getCell($h['Part-Name'] . $rowIndex)->getCalculatedValue()];					
							$hops[$hops_from."-".$hops_to]['varort'] = (7/365)* $rows->getCell($h['flow'] . $rowIndex)->getCalculatedValue() * $bomval;							
						}
						foreach ($stops as $u=>$stop) {
							if ($stop["num"] == $hops_from) { 
								$hops[$hops_from."-".$hops_to]['Description'] .= "From: " . $stop['Location'];
								$hops[$hops_from."-".$hops_to]['fuuid'] = $u;
								continue;
							}
						}
						foreach ($stops as $u=>$stop) {
							if ($stop["num"] == $hops_to) { 
								$hops[$hops_from."-".$hops_to]['Description'] .= " To: " . $stop['Location'];
								$hops[$hops_from."-".$hops_to]['tuuid'] = $u;
								continue;
							}
						}	
				}
				
			}
			
		}
		
		
		
		
		
		// Loop through and bump any children down
		// for each hop
		// if the FROM is less than or equal to the TO, bump the FROM down and grab all the hops that it is TO

		$extratier = false;
		for ($i=$tier_start;$i<=$tier_end; $i++) {
			$extratier = false;
			foreach ($hops as $hop) {
				if ($stops[$hop['tuuid']]['tier'] == $i) {
					if ($stops[$hop['fuuid']]['tier'] <= $stops[$hop['tuuid']]['tier']) {
						$stops[$hop['fuuid']]['tier'] = $stops[$hop['fuuid']]['tier'] + 1;
						$extratier = true;
					}
				}
			}
			if ($extratier == true)	$tier_end++;		
		}

		foreach ($stops as $id=>$stop) {
			if (isset($stops[$id]['days']) == true) {
				$stops[$id]['Description'] = "This site requires " . $stops[$id]['days']. " days to recover.";
			}
			foreach($stops[$id]['parts'] as $count=>$part) {
				if (count($stops[$id]['parts']) == $count) {
					$stops[$id]['Description'] .= "and " . ($part['spilt']*100) ."% of part '".$part['name']."'.";
				} else {
					$stops[$id]['Description'] .= ($part['split']*100)."% of part '".$part['name']."', ";	
				}
			}
			if ($stop['varort'] != "") {
				if ($stops[$id]['varort'] >= $maxvarort) {
					$stops[$id]['color'] = "#ff0000";
				} elseif ($stops[$id]['varort'] < $maxvarort && $stops[$id]['varort'] >= $maxvarort/2) {
					$stops[$id]['color'] = "#ffc000";
				} elseif ($stops[$id]['varort'] < $maxvarort/2 && $stops[$id]['varort'] >= $maxvarort/4) {
					$stops[$id]['color'] = "#ffff00";
				} else {
					$stops[$id]['color'] = "#92d050";
				}
			}		
		}	
		foreach ($hops as $id=>$hop) {
			if (isset($hop['varort']) == true) {
				if ($hop['varort'] >= $maxvarort/20) {
					$hops[$id]['color'] = "#ff0000";
				} elseif ($hop['varort'] < $maxvarort/20 && $hop['varort'] >= $maxvarort/40) {
					$hops[$id]['color'] = "#ffff00";
				} else {
					$hops[$id]['color'] = "#92d050";
				}
			}
		}
		
		$description = "";
		/*
		if (isset($maxvarort) == true) {
			$description = '\<div style="width:10px;height:10px;background-color:#ff0000;"\>&nbsp;\</div\>' . 'Over ' . $maxvarort . '\<br /\>';
			$description .= '\<div style="width:10px;height:10px;background-color:#ffc000;"\>&nbsp;\</div\>' . 'Between ' . $maxvarort . " and ". ($maxvarort/2) . '<br /\>';
			$description .= '\<div style="width:10px;height:10px;background-color:#ffff00;"\>&nbsp;\</div\>' . 'Between ' . $maxvarort/2 . " and ". ($maxvarort/4) . '<br /\>';
			$description .= '\<div style="width:10px;height:10px;background-color:#92d050;"\>&nbsp;\</div\>' . 'Under ' . ($maxvarort/4) . '\<br /\>';
		}
		*/
		/*
		if (isset($maxvarort) == true) {
			$description = 'Red is over ' . floor($maxvarort). ". ";
			$description .= 'Orange is between ' . floor($maxvarort) . " and ". floor($maxvarort/2) . '. ';
			$description .= 'Yellow is between ' . floor($maxvarort/2) . " and ". floor($maxvarort/4) . '. ';
			$description .= 'Green is under ' . floor($maxvarort/4) . '. ';
			$description .= 'Blue do not have VARORT values. ';
		}
		*/
		/*
		
		
		
		Now we convert the array to a phpexcel object and then to a csv output
		
		

		*/
		
		var_dump($stops);
		var_dump($hops);
		// new PHPExcel Object
		$stopswriter = new PHPExcel();
		$stopswriter->createSheet();
		$stopswriter->setActiveSheetIndex(0);
		
		$stopswriter->getActiveSheet()->setCellValue("A1", 'Title');
		$stopswriter->getActiveSheet()->setCellValue("B1", 'Location');
		$stopswriter->getActiveSheet()->setCellValue("C1", 'Address');
		$stopswriter->getActiveSheet()->setCellValue("D1", 'Description');
		/*
		$stopswriter->getActiveSheet()->setCellValue("E1", 'color');
		$stopswriter->getActiveSheet()->setCellValue("F1", 'varort');
		$stopswriter->getActiveSheet()->setCellValue("G1", 'tier');
		$stopswriter->getActiveSheet()->setCellValue("H1", 'size');
		$stopswriter->getActiveSheet()->setCellValue("I1", 'lat');
		$stopswriter->getActiveSheet()->setCellValue("J1", 'long');	
		*/

		$stopswriter->getActiveSheet()->setCellValue("E1", 'varort');
		$stopswriter->getActiveSheet()->setCellValue("F1", 'tier');
		$stopswriter->getActiveSheet()->setCellValue("G1", 'size');
		$stopswriter->getActiveSheet()->setCellValue("H1", 'lat');
		$stopswriter->getActiveSheet()->setCellValue("I1", 'long');
		
		$count = 1;	
		foreach ($stops as $num=>$stop) {
			$stopswriter->getActiveSheet()->setCellValue("A".($count+1), $stop['Name']);
			$stopswriter->getActiveSheet()->setCellValue("B".($count+1), $stop['Location']);
			$stopswriter->getActiveSheet()->setCellValue("C".($count+1), $stop['Address']);
			$stopswriter->getActiveSheet()->setCellValue("D".($count+1), $stop['Description']);
			/*
			$stopswriter->getActiveSheet()->setCellValue("E".($count+1), $stop['color']);
			$stopswriter->getActiveSheet()->setCellValue("F".($count+1), $stop['varort']);
			$stopswriter->getActiveSheet()->setCellValue("G".($count+1), $stop['tier']);
			$stopswriter->getActiveSheet()->setCellValue("H".($count+1), "0.5");
			*/
			$stopswriter->getActiveSheet()->setCellValue("E".($count+1), $stop['varort']);
			$stopswriter->getActiveSheet()->setCellValue("F".($count+1), $stop['tier']);
			$stopswriter->getActiveSheet()->setCellValue("G".($count+1), "0.5");
			
			//$stopswriter->getActiveSheet()->setCellValue("J".($count+1), $stop['lat']);
			//$stopswriter->getActiveSheet()->setCellValue("K".($count+1), $stop['long']);
			$count++;
		}
	
		// new PHPExcel Object
		$hopswriter = new PHPExcel();
		$hopswriter->createSheet();
		$hopswriter->setActiveSheetIndex(0);
		$hopswriter->getActiveSheet()->setCellValue("A1", 'From');
		$hopswriter->getActiveSheet()->setCellValue("B1", 'To');
		$hopswriter->getActiveSheet()->setCellValue("C1", 'Description');
		//$hopswriter->getActiveSheet()->setCellValue("D1", 'Color');
		$count = 2;
		foreach ($hops as $num=>$hop) {
			$hopswriter->getActiveSheet()->setCellValue("A".($count), trim($hop['From']));
			$hopswriter->getActiveSheet()->setCellValue("B".($count), trim($hop['To']));
			$hopswriter->getActiveSheet()->setCellValue("C".($count), trim($hop['Description']));
			//$hopswriter->getActiveSheet()->setCellValue("D".($count), trim($hop['color']));
			$count++;
		} 
		$sWriter = new PHPExcel_Writer_CSVContents($stopswriter);
		$stop_csv = $sWriter->returnContents();
		$hWriter = new PHPExcel_Writer_CSVContents($hopswriter);
		$hop_csv = $hWriter->returnContents();
		var_dump($stop_csv);
		var_dump($hop_csv);
		
        $sc->stops = self::csv2stops($stop_csv, $options);
        $sc->hops = $hop_csv ? self::csv2hops($hop_csv, $sc->stops, $options) : array();
        $sc->attributes = array("description"=>$description);
        return $sc;	
    }

}

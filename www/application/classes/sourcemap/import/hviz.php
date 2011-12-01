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
		// If you have an upstream template
		$tier_start = 0;
		$tier_end = 0;
		if (in_array("Template 1- Upstream", $sheets) == true) {
			$contentReader->setLoadSheetsOnly("Template 1- Upstream");
			$contentPHPExcel = $contentReader->loadContents($xls);
			$stops = array();
			$hops = array();
			$rows = $contentPHPExcel->getActiveSheet();
		
			// Figure out where all the columns are
			$h = array(
				"BOM-Level" => "",
				"Part-Name"	=> "",
				"Description" => "",	
				"Qty" => "",	
				"Unit" => "",	
				"Source-Name" => "",	
				"Source-Split" => "",	
				"City" => "",	
				"Country" => "",	
				"Postal-Code" => "",	
				"Latitude" => "",	
				"Longitude" => "",	
				"Risk Recovery Days" => ""	
			);
			
			//$description = array();
		

			for ($i = 0; $rows->cellExistsByColumnAndRow($i,2) == true; $i++) {
				$value = strtolower($rows->getCellByColumnAndRow($i,2)->getValue());
				$column = $rows->getCellByColumnAndRow($i,2)->getColumn();
				if (strpos($value,"part-name") !== false || strpos($value,"part name") !== false)
					$h["Part-Name"] = $column;
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
			$count = 1;
			$boms = array();
			foreach ($rows->getRowIterator() as $row) {
				$rowIndex = $row->getRowIndex();
				if ($rows->getCell($h["Part-Name"] . $rowIndex)->getCalculatedValue() != NULL && $rows->getCell($h["Part-Name"] . $rowIndex)->getCalculatedValue() != "Part-Number" && $rows->getCell($h["Part-Name"] . $rowIndex)->getCalculatedValue() != "Part-Name" && $rows->getCell($h["Part-Name"] . $rowIndex)->getCalculatedValue() != "Note:" && $rows->getCell("A" . $rowIndex)->getCalculatedValue() != "Sort Order") {
					$uuid = $rows->getCell($h["Source-Name"] . $rowIndex)->getCalculatedValue() . " (" . $rows->getCell($h["City"] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h["Country"] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h["Postal-Code"] . $rowIndex)->getCalculatedValue() . ")";
					if (isset($stops[$uuid]) == false) {						
						$stops[$uuid] = array (
								'num' => $count,
								'Name' =>  $rows->getCell($h["Source-Name"] . $rowIndex)->getCalculatedValue(),	
								'Location' => $rows->getCell($h["City"] . $rowIndex)->getCalculatedValue(), 
								'Address' => $rows->getCell($h["City"] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h["Country"] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h["Postal-Code"] . $rowIndex)->getCalculatedValue(),	
								'Description' => "",
								'Percentage' =>	"",	
								'qty' => "",	
								'color' => "",
								'size' => "1",
								'lat' => "",
								'long' => "",
								'Risk Recovery Days' => "",
								'parts' => array($rows->getCell($h["Part-Name"] . $rowIndex)->getCalculatedValue()),
								'tier' => $rows->getCell($h["BOM-Level"] . $rowIndex)->getCalculatedValue()							
						);
						$tier_start = min($rows->getCell($h["BOM-Level"] . $rowIndex)->getCalculatedValue(),$tier_start);
						$tier_end = max($rows->getCell($h["BOM-Level"] . $rowIndex)->getCalculatedValue(),$tier_end);
						if (isset($h['Risk Recovery Days']) == true) {
							if ($rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue() != "") {
								$stops[$uuid]['Risk Recovery Days'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue();
								$stops[$uuid]['Description'] = "This site requires " . $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue() . " days to return to 100% production. It supplies the following parts:<br />";
								if ($h["Source-Split"] != "" && $h["Description"] != "") {
									$stops[$uuid]['Description'] .= ($rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue()*100)."% of part ".$rows->getCell($h["Description"] . $rowIndex)->getCalculatedValue()."<br />";
								}
								$stops[$uuid]['size'] = self::returnSize($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue());
								$stops[$uuid]['Percentage'] = 100*$rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue()/365;
							}
						}
						if (trim($stops[$uuid]['Address']) == "") {
							$stops[$uuid]['lat'] = "0";
							$stops[$uuid]['long'] = "0";
						}
						if (trim($stops[$uuid]['Description']) == "") {
							$stops[$uuid]['Description'] = "INCOMPLETE";
						}
						if (trim($stops[$uuid]['Percentage']) == "") {
							$stops[$uuid]['Percentage'] = "100";
						}
						//$hops_from = $count;
						$count++;
					} else {
						// Make sure its the lowest tier
						$stops[$uuid]['tier'] = max($stops[$uuid]['tier'], $rows->getCell($h["BOM-Level"] . $rowIndex)->getCalculatedValue());
						
					/*
						if (isset($h['Risk Recovery Days']) == true) {
							if ($stops[$uuid]['Risk Recovery Days'] < $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue()) {	 
								if ($stops[$uuid]['Description'] == "") {
									$stops[$uuid]['Description'] = "This site requires " . $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue() . " days to return to 100% production. It supplies the following parts: - ";
								}
								$stops[$uuid]['Description'] = str_replace($stops[$uuid]["Risk Recovery Days"], $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue(), $stops[$uuid]['Description'] ) ;
								$stops[$uuid]['Risk Recovery Days'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue();
								$stops[$uuid]['size'] = self::returnSize($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue());
								$stops[$uuid]['Percentage'] = 100*$rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue()/365;
						}
						if (in_array($rows->getCell($h["Part-Name"] . $rowIndex)->getCalculatedValue(),$stops[$uuid]["parts"]) === false && $h["Source-Split"] != "" && $h["Description"] != "") {
							$stops[$uuid]['Description'] .= ($rows->getCell($h["Source-Split"] . $rowIndex)->getCalculatedValue())."% of part ".$rows->getCell($h["Description"] . $rowIndex)->getCalculatedValue()."<br />";	
						}	
					*/	
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
							'Description' => ''
						);
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
		
		if (in_array("Template 2 - Downstream", $sheets) == true) {	
			$contentReader->setLoadSheetsOnly("Template 2 - Downstream");
			$contentPHPExcel = $contentReader->loadContents($xls);
			$rows = $contentPHPExcel->getActiveSheet();
			// Figure out where all the columns are
			$h = array(
				"Part-Name" => "",
				"O-Name" => "",
				"O-City" => "",	
				"O-Country" => "",	
				"O-Postal-Code" => "",	
				"O-Latitude" => "",	
				"O-Longitude" => "",	
				"D-Name" => "",	
				"D-City" => "",	
				"D-Country" => "",	
				"D-Postal-Code" => "",	
				"D-Latitude" => "",	
				"D-Longitude" => "",	
				"flow" => "",
				"Risk Recovery Days" => ""		
			);

			for ($i = 0; $rows->cellExistsByColumnAndRow($i,3) == true; $i++) {
				$value = strtolower($rows->getCellByColumnAndRow($i,3)->getCalculatedValue());
				$column = $rows->getCellByColumnAndRow($i,3)->getColumn();
				if (strpos($value,"part-name") !== false || strpos($value,"part name") !== false || strpos($value,"part number") !== false || strpos($value,"part-number") !== false)
					$h["Part-Name"] = $column;
				elseif (strpos($value,"origin") !== false && strpos($value,"name") !== false)
					$h["O-Name"] = $column;
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
				if ($rows->getCell("A" . $rowIndex)->getCalculatedValue() != NULL && $rows->getCell("A" . $rowIndex)->getCalculatedValue() != "Part-Number" && $rows->getCell("A" . $rowIndex)->getCalculatedValue() != "Part Number" && $rows->getCell("A" . $rowIndex)->getCalculatedValue() != "Part name" && $rows->getCell("A" . $rowIndex)->getCalculatedValue() != "Note:") {
					$uuid = $rows->getCell($h['O-Name'] . $rowIndex)->getCalculatedValue() . " (" . $rows->getCell($h['O-City'] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h['O-Country'] . $rowIndex)->getCalculatedValue() . " " . $rows->getCell($h['O-Postal-Code'] . $rowIndex)->getCalculatedValue() . ")";

					if (isset($stops[$uuid]) == false) {
						$stops[$uuid] = array (
								'num' => $count,
								'Name' => $rows->getCell($h['O-Name'] . $rowIndex)->getCalculatedValue(),	
								'Location' => $rows->getCell($h['O-Name'] . $rowIndex)->getCalculatedValue() . " - " . $rows->getCell($h['O-City'] . $rowIndex)->getCalculatedValue(), 
								'Address' => $rows->getCell($h['O-City'] . $rowIndex)->getCalculatedValue() + " " + $rows->getCell($h['O-Country'] . $rowIndex)->getCalculatedValue() + " " + $rows->getCell($h['O-Postal-Code'] . $rowIndex)->getCalculatedValue(),	
								'Description' => $uuid,
								'Percentage' =>	"",	
								'qty' => "0",	
								'size' => "1",
								'Risk Recovery Days' => "",
								'tier' => ""
							);
							/*
							if (isset($h['Risk Recovery Days']) == true) {
								$stops[$uuid]['Description'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue() . " Days at ". $stops[$uuid]['Description'];
								$stops[$uuid]['Risk Recovery Days'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue();
								$stops[$uuid]['size'] = self::returnSize($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue());
								$stops[$uuid]['Percentage'] = 100*$rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue()/365;
							}
							*/
							//$hops_from = $count;
							$count++;
					} else {
						/*
						if (isset($h['Risk Recovery Days']) == true) {
							if ($stops[$uuid]['Risk Recovery Days'] < $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue()) {
								$stops[$uuid]['Description'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue() . " Days at ". $stops[$uuid]['Description'];
								$stops[$uuid]['Risk Recovery Days'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue();
								$stops[$uuid]['size'] = self::returnSize($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue());
								$stops[$uuid]['Percentage'] = 100*$rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue()/365;
							}
						}
						*/	
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
								'Percentage' =>	"",	
								'qty' => "0",	
								'Risk Recovery Days' => "",
								'size' => "1",
								'tier' => ($stops[$prev]['tier'] -1)
							);
							$tier_start = min($stops[$uuid]['tier'],$tier_start);
							$tier_end = max($stops[$uuid]['tier'],$tier_end);
							/*
							if (isset($h['Risk Recovery Days']) == true) {
								$stops[$uuid]['Description'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue() . " Days at ". $stops[$uuid]['Description'];
								$stops[$uuid]['Risk Recovery Days'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue();
								$stops[$uuid]['size'] = self::returnSize($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue());
								$stops[$uuid]['Percentage'] = 100*$rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue()/365;
							}
							*/
							//$hops_to = $count;
							$count++;
					} else {
						/*
						if (isset($h['Risk Recovery Days']) == true) {
							if ($stops[$uuid]['Risk Recovery Days'] < $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue()) {
								$stops[$uuid]['Description'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue() . " Days at ". $stops[$uuid]['Description'];
								$stops[$uuid]['Risk Recovery Days'] = $rows->getCell($h["Risk Recovery Days"] . $rowIndex)->getCalculatedValue();
								$stops[$uuid]['size'] = self::returnSize($rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue());															}
								$stops[$uuid]['Percentage'] = 100*$rows->getCell($h['Risk Recovery Days'] . $rowIndex)->getCalculatedValue()/365;
						}
						*/
						
					}
					$hops_to = $stops[$uuid]["num"];
						$hops[$hops_from."-".$hops_to] = array(
							'From' => $hops_from,
							'To' => $hops_to,
							'Description' => ""
						);
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
		$stopswriter->getActiveSheet()->setCellValue("E1", 'Percentage');
		$stopswriter->getActiveSheet()->setCellValue("F1", 'qty');
		//$stopswriter->getActiveSheet()->setCellValue("G1", 'color');
		$stopswriter->getActiveSheet()->setCellValue("G1", 'size');
		$stopswriter->getActiveSheet()->setCellValue("H1", 'Risk Recovery Days');
		$stopswriter->getActiveSheet()->setCellValue("I1", 'tier');
		$stopswriter->getActiveSheet()->setCellValue("J1", 'lat');
		$stopswriter->getActiveSheet()->setCellValue("K1", 'long');	
		$count = 1;	
		foreach ($stops as $num=>$stop) {
			$stopswriter->getActiveSheet()->setCellValue("A".($count+1), $stop['Name']);
			$stopswriter->getActiveSheet()->setCellValue("B".($count+1), $stop['Location']);
			$stopswriter->getActiveSheet()->setCellValue("C".($count+1), $stop['Address']);
			$stopswriter->getActiveSheet()->setCellValue("D".($count+1), $stop['Description']);
			$stopswriter->getActiveSheet()->setCellValue("E".($count+1), $stop['Percentage']);
			$stopswriter->getActiveSheet()->setCellValue("F".($count+1), $stop['qty']);
			//$stopswriter->getActiveSheet()->setCellValue("G".($count+1), $stop['color']);
			$stopswriter->getActiveSheet()->setCellValue("G".($count+1), $stop['size']);
			$stopswriter->getActiveSheet()->setCellValue("H".($count+1), $stop['Risk Recovery Days']);
			$stopswriter->getActiveSheet()->setCellValue("I".($count+1), $stop['tier']);
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
			//$hopswriter->getActiveSheet()->setCellValue("D".($count), trim($hop['Color']));
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
        $sc->attributes = array();
        return $sc;
		
    }

}


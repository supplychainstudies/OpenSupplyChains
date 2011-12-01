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

class Sourcemap_Import_Xls extends Sourcemap_Import_Csv{
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

    public static function xls2sc($xls=null, $o=array()) {
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
		$sheets = array();
		// If you have an upstream template
		
		$contentReader->setLoadSheetsOnly("Upstream");
		$contentPHPExcel = $contentReader->loadContents($xls);
		$sheets['Upstream'] = $contentPHPExcel->getActiveSheet();
		$contentReader->setLoadSheetsOnly("Downstream");
		$contentPHPExcel = $contentReader->loadContents($xls);
		$sheets['Downstream'] = $contentPHPExcel->getActiveSheet();
		
		$stops = array();
		$hops = array();
		$count = 1;
		foreach($sheets as $sheetname=>$sheet) {
			// Figure out where all the columns are

			// These two variables are for keeping track whether there is any content in the columns
			// if there is at any point, that column in these variables will exist
			$sh_columns = array();
			$hh_columns = array();
			$sh = array(
				"Title" => "x",
				"Address" => "",
				"Description" => "",
				"url:moreinfo" => "",
				"urltitle:moreinfo" => "",	
				"youtube:link" => "",
				"flickr:setid" => "",
				"Weight" => "",	
				"Unit" => "",	
				"Co2e" => "",
				"Co2e-Reference" => "",		
				"Latitude" => "",	
				"Longitude" => "",	
				"Color" => "",
				"Size" => ""
			);
			$hh = array(
				"To" => "x",
				"From" => "x",
				"Weight" => "",
				"Transportation" => "",
				"Co2e" => "",
				"Co2e-Reference" => ""
			);
			$rows = $sheet;
			$tiers = array();
			// Figure out where the title row is
			for ($starting_row = 1; $rows->cellExistsByColumnAndRow(1,$starting_row) == true; $starting_row++) {
				$check_title = false;
				$check_address = false;
				for ($i = 0; $rows->cellExistsByColumnAndRow($i,$starting_row) == true; $i++) {
					$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
					$column = $rows->getCellByColumnAndRow($i,$starting_row)->getColumn();
					if (strpos($value,"location") !== false || strpos($value,"address") !== false || strpos($value,"coordinate") !== false) {	
						break 2; 
					}
				}
			}
			// Figure out what each of the columns represents. if its not recognizable, it gets its own column (becomes and attribute)
			for ($i = 0; $rows->cellExistsByColumnAndRow($i,$starting_row) == true; $i++) {
				$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
				$column = $rows->getCellByColumnAndRow($i,$starting_row)->getColumn();
				if ($sh["Title"] == "" && (strpos($value,"name") !== false || strpos($value,"title") !== false || strpos($value,"placename") !== false) && (strpos($value,"youtube") === false && strpos($value,"flickr") === false && strpos($value,"link") === false && strpos($value,"optional") === false))
					$sh["Title"] = $column;
				elseif ($sh["Address"] == "" && (strpos($value,"location") !== false || strpos($value,"address") !== false || strpos($value,"coordinate") !== false || (strpos($value,"lat") !== false && strpos($value,"lon") !== false)))
					$sh["Address"] = $column;
				elseif ($sh["Description"] == "" && strpos($value,"descr") !== false)
					$sh["Description"] = $column;
				elseif ($sh["url:moreinfo"] == "" && strpos($value,"link") !== false)	
					$sh["url:moreinfo"] = $column;
				elseif ($sh["urltitle:moreinfo"] == "" && strpos($value,"link") !== false && (strpos($value,"title") !== false || strpos($value,"name") !== false))	
					$sh["urltitle:moreinfo"] = $column;
				elseif ($sh["youtube:link"] == "" && strpos($value,"youtube") !== false)	
					$sh["youtube:link"] = $column;
				elseif ($sh["flickr:setid"] == "" && strpos($value,"flickr") !== false)	
					$sh["flickr:setid"] = $column;
				elseif ($sh["Weight"] == "" && strpos($value,"weight") !== false && strpos($value,"trans") === false)	
					$sh["Weight"] = $column;
				elseif ($sh["Unit"] == "" && strpos($value,"unit") !== false || strpos($value,"uom") !== false)
					$sh["Unit"] = $column;
				elseif ($sh["Color"] == "" && strpos($value,"color") !== false)
					$sh["Color"] = $column;
				elseif ($sh["Size"] == "" && strpos($value,"size") !== false)
					$sh["Size"] = $column;
				elseif ($sh["Co2e"] == "" && strpos($value,"co2e") !== false)
					$sh["Co2e"] = $column;
				elseif ($sh["Co2e-Reference"] == "" && strpos($value,"co2e") !== false && strpos($value,"ref") !== false)
					$sh["Co2e-Reference"] = $column;
				elseif ($hh["Weight"] == "" && strpos($value,"trans") !== false && strpos($value,"weight") !== false)
					$hh["Weight"] = $column;				
				elseif ($hh["Transportation"] == "" && strpos($value,"trans") !== false)
					$hh["Transportation"] = $column;
				elseif ($hh["Co2e"] == "" && strpos($value,"trans") !== false && strpos($value,"co2e") !== false)
					$hh["Co2e"] = $column;
				elseif ($hh["Co2e-Reference"] == "" && strpos($value,"trans") !== false && strpos($value,"co2e") !== false && strpos($value,"ref") !== false)
					$hh["Co2e-Reference"] = $column;
				elseif (strpos($value,"bom") !== false || strpos($value,"level") !== false || strpos($value,"tier") !== false) {
					//Find a number somewhere in there
					$pattern = '/\d/';
					$instances = array();
					preg_match($pattern, $value, $instances);
					$tiers[$instances[0]] = $column;
				}
				else
					$sh[$value] = $column;
			}
			
			// Unset Columns that don't exist
			foreach ($sh as $field=>$column) {
				if ($column == "") {
					unset($sh[$field]);
				} 
			}			
			foreach ($hh as $field=>$column) {
				if ($column == "") {
					unset($hh[$field]);
				} 
			}
			
			$current_path = array();
			
			// Loop through all the rows
			foreach ($rows->getRowIterator() as $row) {
				$rowIndex = $row->getRowIndex();
				if ($rowIndex != 1) {
					$name = "";
					// Find the name
					foreach ($tiers as $num=>$column) {
						if ($rows->getCell($column . $rowIndex)->getCalculatedValue() != "") {
							$name = $rows->getCell($column . $rowIndex)->getCalculatedValue();							
						}
					}
					if (isset($stops[$name]) == false) {
						// create a stop 						
						$stops[$name] = array (
							"id" => $count,
							"Title" => $name
						);
						$sh_columns['Title'] = true;
						// go through all the existing columns and add the values
						foreach($sh as $field=>$column) {
							if ($column != "x") {
								$stops[$name][$field] = trim($rows->getCell($column . $rowIndex)->getCalculatedValue());
								if ($stops[$name][$field] != "" && isset($sh_columns[$field]) != true) {
									$sh_columns[$field] = true; 
								}
							}
							
						}
						$count++;
					}
					// Hops, now
					if ($sheetname == "Upstream") {
						$hops_from = $stops[$name]["id"];
						$hops_to = "";
						foreach ($tiers as $num=>$column) {
							if ($rows->getCell($column . $rowIndex)->getCalculatedValue() != "") {
								if ($num != 0) {
									$hops_to = $current_path[$num-1];
								}
								$current_path[$num] = $hops_from;
								continue;							
							}
						}	
					} else {
						$hops_to = $stops[$name]["id"];
						$hops_from = "";
						foreach ($tiers as $num=>$column) {
							if ($rows->getCell($column . $rowIndex)->getCalculatedValue() != "") {
								if ($num != 0) {
									$hops_from = $current_path[$num-1];
								}
								$current_path[$num] = $hops_to;
								continue;							
							}
						}						
					}
										
					if ($hops_from != "" && $hops_to != "" && $hops_from != $hops_to) {
						$new_num = count($hops);
					
						$hops[$new_num] = array(
							"From"=>$hops_from,
							"To"=>$hops_to
						);
						$hh_columns['From'] = true;$hh_columns['To'] = true;
						foreach($hh as $field=>$column) {
							if ($column != "x") {
								$hops[$new_num][$field] = trim($rows->getCell($column . $rowIndex)->getCalculatedValue());
								if ($hops[$new_num][$field] != "" && isset($hh_columns[$field]) != true) {
									$hh_columns[$field] = true; 
								}
							}
						}
					} // 	if ($hops_from != "" && $hops_to != "" and isset($hops[$hops_from."-".$hops_to]) == false && $hops_from != $hops_to) {
				
				} // If line isn't blank, etc
			} // Foreach
		} // foreach($sheets as $sheet=>$rows)

		

		
		/*
		
		
		
		Now we convert the array to a phpexcel object and then to a csv output
		
		

		*/
		
		var_dump($stops);
		var_dump($hops);
		
		// new PHPExcel Object
		$stopswriter = new PHPExcel();
		$stopswriter->createSheet();
		$stopswriter->setActiveSheetIndex(0);
		
		// Create the Stops CSV
		// Create the Stop headers
		$column_iterator = 0;
		foreach ($sh as $name=>$value) {
			if (isset($sh_columns[$name]) == true) {
				$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($column_iterator,1,$name);
				$column_iterator++;
			} else {
				unset($sh[$name]);
			}
		}
		// Add the Data
		$row_iterator = 2;
		foreach ($stops as $num=>$stop) {
			$column_iterator = 0;
			foreach ($sh as $name=>$value) {
					if (isset($stop[$name]) == true) {
						$stopswriter->getActiveSheet()->setCellValueByColumnAndRow($column_iterator,$row_iterator,$stop[$name]);		
					}
					$column_iterator++;
			}
			$row_iterator++;
		}
	
		// new PHPExcel Object
		$hopswriter = new PHPExcel();
		$hopswriter->createSheet();
		$hopswriter->setActiveSheetIndex(0);
		// Create the Stop headers
		$column_iterator = 0;
		foreach ($hh as $name=>$value) {
			if (isset($hh_columns[$name]) == true) {
				$hopswriter->getActiveSheet()->setCellValueByColumnAndRow($column_iterator,1,$name);
				$column_iterator++;
			} else {
				unset($sh[$name]);
			}		
		}
		// Add the Data
		$row_iterator = 2;
		foreach ($hops as $num=>$hop) {
			$column_iterator = 0;
			foreach ($hh as $name=>$value) {
				if (isset($hop[$name]) == true) {
				$hopswriter->getActiveSheet()->setCellValueByColumnAndRow($column_iterator,$row_iterator,$hop[$name]);
				}
				$column_iterator++;
			}
			$row_iterator++;
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


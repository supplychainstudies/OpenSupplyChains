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


    public static function xls2sc($xls=null, $o=array()) {
        $options = array();
        if(!is_array($o)) $o = (array)$o;
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);
        $sc = new stdClass();

		// Row cap - Maximum Number of Rows
		$row_cap = 5000;

		//get upstream sheet
		$contentReader = new PHPExcel_Reader_Excel5Contents();
		$contentReader->setReadDataOnly(true);
		$contentPHPExcel = $contentReader->loadContents($xls);
		$sheets = $contentPHPExcel->getSheetNames();
		$contentReader->setLoadSheetsOnly(0);
		$contentPHPExcel = $contentReader->loadContents($xls);
		$stops = array();
		$hops = array();
		$rows = $contentPHPExcel->getActiveSheet();
						
			// Figure out where all the columns are
			$sh = array(
				"Title" => "",
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
				"To" => "",
				"From" => "",
				"Weight" => "",
				"Transportation" => "",
				"Co2e" => "",
				"Co2e-Reference" => ""
			);
			// Title row isn't always at row 1
			for ($starting_row = 1; $rows->cellExistsByColumnAndRow(1,$starting_row) == true; $starting_row++) {
				$check_title = false;
				$check_address = false;
				for ($i = 0; $rows->cellExistsByColumnAndRow($i,$starting_row) == true; $i++) {
					$value = strtolower($rows->getCellByColumnAndRow($i,$starting_row)->getValue());
					$column = $rows->getCellByColumnAndRow($i,$starting_row)->getColumn();
					if ((strpos($value,"name") !== false || strpos($value,"title") !== false || strpos($value,"placename") !== false) && (strpos($value,"youtube") === false && strpos($value,"flickr") === false && strpos($value,"link") === false && strpos($value,"optional") === false)){	
						$check_title = true;
						if ($check_title == true && $check_address == true) { break 2; }
					}
					elseif (strpos($value,"location") !== false || strpos($value,"address") !== false || strpos($value,"coordinate") !== false) {	
						$check_address = true;
						if ($check_title == true && $check_address == true) { break 2; }
					}
				}
				if ($check_title == true && $check_address == true) { break 2; }
			}
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
				elseif ($sh["Title"] == "" && strpos($value,"flickr") !== false)	
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
				elseif ($hh['To'] == "" && (strpos($value,"connect") !== false || strpos($value,"destin") !== false)) {
					$hh["To"] = $column; $hh["From"] = "x"; 
				}	
				elseif ($hh["Weight"] == "" && strpos($value,"trans") !== false && strpos($value,"weight") !== false)
					$hh["Weight"] = $column;				
				elseif ($hh["Transportation"] == "" && strpos($value,"trans") !== false)
					$hh["Transportation"] = $column;
				elseif ($hh["Co2e"] == "" && strpos($value,"trans") !== false && strpos($value,"co2e") !== false)
					$hh["Co2e"] = $column;
				elseif ($hh["Co2e-Reference"] == "" && strpos($value,"trans") !== false && strpos($value,"co2e") !== false && strpos($value,"ref") !== false)
					$hh["Co2e-Reference"] = $column;
				else
					$sh[$value] = $column;
				var_dump($value);
				var_dump($column);
				var_dump($sh);
			}
			$count = 1;
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
			// These two variables are for keeping track whether there is any content in the columns
			// if there is at any point, that column in these variables will exist
			$sh_columns = array();
			$hh_columns = array();
			var_dump($sh);
			var_dump($hh);
			foreach ($rows->getRowIterator() as $row) {
				$rowIndex = $row->getRowIndex();
				if ($rows->getCell("A" . $rowIndex)->getCalculatedValue() != NULL && $rows->getCell("A" . $rowIndex)->getCalculatedValue() != "" && strpos($rows->getCell("A" . $rowIndex)->getCalculatedValue(), "name") !== true && $rowIndex != 1) {				
					$stops[$count] = array (
						"id" => $count
					);
					foreach($sh as $field=>$column) {
						if ($column != "x") {
							$stops[$count][$field] = trim($rows->getCell($column . $rowIndex)->getCalculatedValue());
							if ($stops[$count][$field] != "" && isset($sh_columns[$field]) != true) {
								$sh_columns[$field] = true; 
							}
						}
					}
					// We have to figure out whether the value in the location/address field is an address or a lat/long
					// a lat/long can look like 0.00,-0.00, so if we explode it and get two numbers, its a lat/lon
					// otherwise, we'll have to try to geocode it
					$test_ll = explode(",",$stops[$count]["Address"]);
					if (count($test_ll) == 2) {
						if (is_float($test_ll[0]) == true && is_float($test_ll[0]) == true) {
							$sh_columns['lat'] = true; $sh['lat'] = "x";
							$sh_columns['lon'] = true; $sh['lon'] = "x";
							$stops[$count]["lat"] = $test_ll[0];
							$stops[$count]["lon"] = $test_ll[1];
						}
					}
					if (isset($hh['To']) != false) {
						if ($rows->getCell($hh["To"] . $rowIndex)->getCalculatedValue() != "") {
							$new_num = count($hops);
						
							$hops[$new_num] = array();
							$hh_columns['From'] = true;
							foreach($hh as $field=>$column) {
								$hops[$new_num][$field] = trim($rows->getCell($column . $rowIndex)->getCalculatedValue());
								if ($hops[$new_num][$field] != "" && isset($hh_columns[$field]) != true) {
									$hh_columns[$field] = true; 
								}
							}
							$hops[$new_num]['From'] = $count;
							$hops[$new_num]['To-Name'] = trim($rows->getCell($hh["To"] . $rowIndex)->getCalculatedValue());
						}
					} 
					$count++;
				}
				if ($count > $row_cap) {
					break;
				}
			}
		// Hops
		foreach ($hops as $num=>$hop) {	
			foreach ($stops as $stop) {
				if ($hops[$num]['To-Name'] == $stop['Title'] || $hops[$num]['To-Name'] == $stop['Address']) {						
						$hops[$num]['To'] = $stop['id'];
						unset($hops[$num]['To-Name']);
						break;
				}
			}
		}
		
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

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
		$contentReader->setLoadSheetsOnly("Upstream");
		$contentPHPExcel = $contentReader->loadContents($xls);
		$stops = array();
		$hops = array();
		$rows = $contentPHPExcel->getActiveSheet();
		$count = 1;
		$boms = array();
		foreach ($rows->getRowIterator() as $row) {
			$rowIndex = $row->getRowIndex();
			if ($rows->getCell("C" . $rowIndex)->getValue() != NULL && $rows->getCell("C" . $rowIndex)->getValue() != "Part-Number" && $rows->getCell("C" . $rowIndex)->getValue() != "Note:") {
				$uuid = $rows->getCell("C" . $rowIndex)->getValue() . " - " . $rows->getCell("N" . $rowIndex)->getValue() . " (" . $rows->getCell("P" . $rowIndex)->getValue() . ")";

				if (isset($stops[$uuid]) == false) {
					$stops[$uuid] = array (
							'num' => $count,
							'Name' => $rows->getCell("C" . $rowIndex)->getValue() + " (" + $rows->getCell("N" . $rowIndex)->getValue() + ")",	
							'Location' => $rows->getCell("P" . $rowIndex)->getValue(), 
							'Address' => $rows->getCell("P" . $rowIndex)->getValue() + ", " + $rows->getCell("R" . $rowIndex)->getValue() + " " + $rows->getCell("S" . $rowIndex)->getValue(),	
							'Description' => $rows->getCell("D" . $rowIndex)->getValue(),
							'Percentage' =>	$rows->getCell("O" . $rowIndex)->getValue(),	
							'qty' => $rows->getCell("L" . $rowIndex)->getValue(),	
							'color' => "#30ac9c",
							'size' => sqrt((min(array(max(array($rows->getCell("L" . $rowIndex)->getValue(), 10)), 100)))/3.14)
						);
						$hops_to = $count;
						$count++;
				} else {
					$hops_to = $stops[$uuid]["num"];
				}
				$hops_from = "";
				// Hops
				if ($rows->getCell("E" . $rowIndex)->getCalculatedValue() != "-") {
					$boms[0] = $hops_to;
				}
				if ($rows->getCell("F" . $rowIndex)->getCalculatedValue() != "-") {
					$boms[1] = $hops_to;
					$hops_from = $boms[0];
				}
				if ($rows->getCell("G" . $rowIndex)->getCalculatedValue() != "-") {
					$boms[2] = $hops_to;
					$hops_from = $boms[1];
				}
				if ($rows->getCell("H" . $rowIndex)->getCalculatedValue() != "-") {
					$boms[3] = $hops_to;
					$hops_from = $boms[2];
				}
				if ($rows->getCell("I" . $rowIndex)->getCalculatedValue() != "-") {
					$boms[4] = $hops_to;
					$hops_from = $boms[3];
				}
				if ($rows->getCell("J" . $rowIndex)->getCalculatedValue() != "-") {
					$boms[5] = $hops_to;
					$hops_from = $boms[4];
				}
				if ($rows->getCell("K" . $rowIndex)->getCalculatedValue() != "-") {
					$boms[6] = $hops_to;
					$hops_from = $boms[5];
				}
				if ($hops_from != "" && $hops_to != "" and isset($hops[$hops_from."-".$hops_to]) == false && $hops_from != $hops_to) {
					$description = "";
					foreach ($stops as $stop) {
						if ($stop["num"] == $hops_from) { 
							$description .= "From: " . $stop['Location'];
							continue;
						}
					}
					foreach ($stops as $stop) {
						if ($stop["num"] == $hops_to) { 
							$description .= " To: " . $stop['Location'];
							continue;
						}
					}
					$hops[$hops_from."-".$hops_to] = array(
						'From' => $hops_from,
						'To' => $hops_to,
						'Description' => $description,
						'Color' => '#cccccc'
					);
				}
			}
		}
		
		
		/* 
		
		
		
		Now we have to parse through the downstream sheet and add it to the stops and hops
		
		The columns should be :
		
		Part name	Origin Name	Origin city	Origin Country	Origin Postal code	Origin Latitude	Origin Longitude	Destn Name	Destn City	Destn Country	Destn Postal code	Destn Latitude	Destn Longitude	Share Percent of Flow
		
		*/
		
			$contentReader->setLoadSheetsOnly("Downstream");
			$contentPHPExcel = $contentReader->loadContents($xls);
			$rows = $contentPHPExcel->getActiveSheet();
			$count = 1;
			$boms = array();
			foreach ($rows->getRowIterator() as $row) {
				$rowIndex = $row->getRowIndex();
				if ($rows->getCell("A" . $rowIndex)->getValue() != NULL && $rows->getCell("A" . $rowIndex)->getValue() != "Part-Number" && $rows->getCell("A" . $rowIndex)->getValue() != "Note:") {
					$uuid = $rows->getCell("A" . $rowIndex)->getValue() . " - " . $rows->getCell("B" . $rowIndex)->getValue() . " (" . $rows->getCell("C" . $rowIndex)->getValue() . ")";

					if (isset($stops[$uuid]) == false) {
						$stops[$uuid] = array (
								'num' => $count,
								'Name' => $rows->getCell("A" . $rowIndex)->getValue() + " (" + $rows->getCell("N" . $rowIndex)->getValue() + ")",	
								'Location' => $rows->getCell("C" . $rowIndex)->getValue(), 
								'Address' => $rows->getCell("C" . $rowIndex)->getValue() + ", " + $rows->getCell("D" . $rowIndex)->getValue() + " " + $rows->getCell("E" . $rowIndex)->getValue(),	
								'Description' => $uuid,
								'Percentage' =>	$rows->getCell("N" . $rowIndex)->getValue(),	
								'qty' => $rows->getCell("N" . $rowIndex)->getValue(),	
								'color' => "#e2a919",
								'size' => sqrt((min(array(max(array($rows->getCell("N" . $rowIndex)->getValue(), 10)), 100)))/3.14)
							);
							$hops_from = $count;
							$count++;
					} else {
						$hops_from = $stops[$uuid]["num"];
					}
					
					
					
					$uuid = $rows->getCell("A" . $rowIndex)->getValue() . " - " . $rows->getCell("H" . $rowIndex)->getValue() . " (" . $rows->getCell("I" . $rowIndex)->getValue() . ")";

					if (isset($stops[$uuid]) == false) {
						$stops[$uuid] = array (
								'num' => $count,
								'Name' => $rows->getCell("A" . $rowIndex)->getValue() + " (" + $rows->getCell("H" . $rowIndex)->getValue() + ")",	
								'Location' => $rows->getCell("I" . $rowIndex)->getValue(), 
								'Address' => $rows->getCell("I" . $rowIndex)->getValue() + ", " + $rows->getCell("J" . $rowIndex)->getValue() + " " + $rows->getCell("K" . $rowIndex)->getValue(),	
								'Description' => $uuid,
								'Percentage' =>	$rows->getCell("N" . $rowIndex)->getValue(),	
								'qty' => $rows->getCell("N" . $rowIndex)->getValue(),	
								'color' => "#e2a919",
								'size' => sqrt((min(array(max(array($rows->getCell("N" . $rowIndex)->getValue(), 10)), 100)))/3.14)
							);
							$hops_to = $count;
							$count++;
					} else {
						$hops_to = $stops[$uuid]["num"];
					}

						$description = "";
						foreach ($stops as $stop) {
							if ($stop["num"] == $hops_from) { 
								$description .= "From: " . $stop['Location'];
								continue;
							}
						}
						foreach ($stops as $stop) {
							if ($stop["num"] == $hops_to) { 
								$description .= " To: " . $stop['Location'];
								continue;
							}
						}
						$hops[$hops_from."-".$hops_to] = array(
							'From' => $hops_from,
							'To' => $hops_to,
							'Description' => $description,
							'Color' => '#cccccc'
						);
				}
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
		
		$stopswriter->getActiveSheet()->setCellValue("A1", 'Name');
		$stopswriter->getActiveSheet()->setCellValue("B1", 'Location');
		$stopswriter->getActiveSheet()->setCellValue("C1", 'Address');
		$stopswriter->getActiveSheet()->setCellValue("D1", 'Description');
		$stopswriter->getActiveSheet()->setCellValue("E1", 'Percentage');
		$stopswriter->getActiveSheet()->setCellValue("F1", 'qty');
		$stopswriter->getActiveSheet()->setCellValue("G1", 'color');
		$stopswriter->getActiveSheet()->setCellValue("H1", 'size');
		
		$count = 1;	
		foreach ($stops as $num=>$stop) {
			$stopswriter->getActiveSheet()->setCellValue("A".($count+1), $stop['Name']);
			$stopswriter->getActiveSheet()->setCellValue("B".($count+1), $stop['Location']);
			$stopswriter->getActiveSheet()->setCellValue("C".($count+1), $stop['Address']);
			$stopswriter->getActiveSheet()->setCellValue("D".($count+1), $stop['Description']);
			$stopswriter->getActiveSheet()->setCellValue("E".($count+1), $stop['Percentage']);
			$stopswriter->getActiveSheet()->setCellValue("F".($count+1), $stop['qty']);
			$stopswriter->getActiveSheet()->setCellValue("G".($count+1), $stop['color']);
			$stopswriter->getActiveSheet()->setCellValue("H".($count+1), $stop['size']);
			$count++;
		}
	
		// new PHPExcel Object
		$hopswriter = new PHPExcel();
		$hopswriter->createSheet();
		$hopswriter->setActiveSheetIndex(0);
		$hopswriter->getActiveSheet()->setCellValue("A1", 'To');
		$hopswriter->getActiveSheet()->setCellValue("B1", 'From');
		$hopswriter->getActiveSheet()->setCellValue("C1", 'Description');
		$hopswriter->getActiveSheet()->setCellValue("D1", 'Color');
		$count = 2;
		foreach ($hops as $num=>$hop) {
			$hopswriter->getActiveSheet()->setCellValue("A".($count), trim($hop['To']));
			$hopswriter->getActiveSheet()->setCellValue("B".($count), trim($hop['From']));
			$hopswriter->getActiveSheet()->setCellValue("C".($count), trim($hop['Description']));
			$hopswriter->getActiveSheet()->setCellValue("D".($count), trim($hop['Color']));
			$count++;
		}
		$sWriter = new PHPExcel_Writer_CSVContents($stopswriter);
		$stop_csv = $sWriter->returnContents();
		$hWriter = new PHPExcel_Writer_CSVContents($hopswriter);
		$hop_csv = $hWriter->returnContents();
		//var_dump($stop_csv);
		//var_dump($hop_csv);
        $sc->stops = self::csv2stops($stop_csv, $options);
        $sc->hops = $hop_csv ? self::csv2hops($hop_csv, $sc->stops, $options) : array();
        $sc->attributes = array();
        return $sc;

    }

}

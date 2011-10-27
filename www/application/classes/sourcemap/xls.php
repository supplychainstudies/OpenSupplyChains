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
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
		            ->setCreator("Bianca Sayan")
		            ->setLastModifiedBy("Bianca Sayan")
		            ->setTitle("Office 2007 XLSX Test Document")
		            ->setSubject("Office 2007 XLSX Test Document")
		            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		            ->setKeywords("office 2007 openxml php")
		            ->setCategory("Test result file");
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hello');
				$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
				$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
				$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');
		$objPHPWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		
		$titles  = array( 
			"Name",
			"Location",
			"Address",
			"Description",
			"Percentage",
			"vimeo:title",
			"vimeo:link",
			"youtube:title",
			"youtube:link",
			"flickr:setid",
			"qty",
			"CO2e",
			"color",
			"size");
		foreach ($titles as $num=>$title) {
			$objPHPExcel->getActiveSheet()->SetCellValue("A".$num, $title);
		}
		$data = json_decode(Sourcemap_Geojson::make($supplychain));
		foreach($data->features as $num=>$ftr) {
			if($ftr->geometry->type == "Point") {
				$objPHPExcel->getActiveSheet()->SetCellValue("B".$num, $ftr->properties->title);
				
				//$points .= isset($ftr->properties->description) ? $ftr->properties->description : "";
			    //$points .= '<coordinates>'.implode($ftr->geometry->coordinates,",").'</coordinates>';
			}
			else {	
			    //$lines .= '<name>'.$ftr->properties->title.'</name>';
				//$lines .= isset($ftr->properties->description) ? $ftr->properties->description : "";
			
			    //$lines .= '<coordinates>'.implode($ftr->geometry->coordinates[0],",").' '.implode($ftr->geometry->coordinates[1],",").'</coordinates>';
			}
		}
		$objPHPWriter->save("/home/sourcemap/sourcemap/www/assets/downloads/05featuredemo.xls");
	}
	
}

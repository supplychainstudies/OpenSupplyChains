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
        $points = array(
        1 => array( 
			"Name",
			"Location",
			"Address",
			"Description",
			"Percentage",
			"youtube:title",
			"youtube:link",
			"flickr:setid",
			"qty",
			"CO2e",
			"color",
			"size"
			)
        );
		$hops = array( 
			1 => array(
				"From",
				"To",	
				//"Origin Point (From)",
				//"Destination Point (To)",
				"Description",	
				"Color",	
				"Transport",	
				"Qty",	
				"unit",	
				"CO2e"	
				)
		);
		foreach($supplychain->stops as $stop) {
				$vals = array(
					"title" => "",
					"placename" => "",
					"address" => "",
					"coordinates" => "",
					"description" => "",
					"percentage" => "",
					"youtube:title" => "",
					"youtube:link" => "",
					"flickr:setid" => "",
					"qty" => "",
					"co2e" => "",
					"color" => "",
					"size" => "0",
					"unit" => ""
				);	
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
		
		$transport = array (
			1 => array ("Air (Long Distance)", 0.115558),
			2 => array ("Air (Regional)", 0.219842),
			3 => array ("Air Freight (Intercontinental)",	0.000450959),
			4 => array ("Air Freight (Regional)",	0.000789178),
			5 => array ("Automobile (20 mpg)",	6.76E-05),
			6 => array ("Automobile (50 mpg)",	2.65E-05),
			7 => array ("Container ship",	4.79E-05),
			8 => array ("Freighter (Inland)",	2.54E-05),
			9 => array ("Helicopter",	0.725748),
			10 => array ("Oceanic Freight Ship",	4.23E-06),
			11 => array ("Tanker ship (Oceanic)",	0.000171928),
			12 => array ("Train (Freight)",	5.64E-06),
			13 => array ("Train (long distance)",	0.00366404),
			14 => array ("Train (Regional)",	0.00676438),
			15 => array ("Tram",	0.259301),
			16 => array ("Truck (16 ton)",	4.51E-05),
			17 => array ("Truck (28 ton)",	3.38E-05),
			18 => array ("Truck (40 ton)",	3.10E-05),
			19 => array ("Van (3.5 ton)",	5.36E-05)			
			);
			
		$units = array (
			1 => array("kg"),
			2 => array("lbs"),
			3 => array("pax")
			);
		
		$ws = new Spreadsheet(array(
        'author'       => 'Sourcemap Incorporated',
        'title'        => $supplychain->attributes->title,
        'subject'      => $supplychain->attributes->title,
        'description'  => $supplychain->attributes->description,
        ));

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
		// Set a dropdown for From
		// Working Validator - Leo doesnt want anymore //$ws->set_column_validation('C',"=Stops!M:M", "LIST", "Limited to list", "This Column is limited to a list of stops.", "Limited to list", "This Column is limited to a list of stops.");
		// Set a dropdown for To
		// Working Validator - Leo doesnt want anymore //$ws->set_column_validation('D',"=Stops!M:M", "LIST", "Limited to list", "This Column is limited to a list of stops.", "Limited to list", "This Column is limited to a list of stops.");
		// Working Validator - Leo doesnt want anymore //$ws->freezeTopRow();	
		
		//Now we set some sheets that contain data, which will be hidden
		
		// Create the transport Co2e Sheet
		// Working Validator - Leo doesnt want anymore //$ws->create_active_sheet();
        // Working Validator - Leo doesnt want anymore //$as = $ws->get_active_sheet();
        // Working Validator - Leo doesnt want anymore //$as->setTitle('Transport CO2e');
		// Working Validator - Leo doesnt want anymore //$ws->set_data($transport, false);
		// Working Validator - Leo doesnt want anymore //$as->setSheetState(PHPExcel_Worksheet::SHEETSTATE_HIDDEN);

		// Create the transport Co2e Sheet
		// Working Validator - Leo doesnt want anymore //$ws->create_active_sheet();
        // Working Validator - Leo doesnt want anymore //$as = $ws->get_active_sheet();
        // Working Validator - Leo doesnt want anymore //$as->setTitle('Units');
		// Working Validator - Leo doesnt want anymore //$ws->set_data($units, false);
		// Working Validator - Leo doesnt want anymore //$as->setSheetState(PHPExcel_Worksheet::SHEETSTATE_HIDDEN);
		
		
		// Working Validator - Leo doesnt want anymore //$ws->set_active_sheet(1);
		// Set a dropdown for To
		// Working Validator - Leo doesnt want anymore //$ws->set_column_validation('G',"='Transport CO2e'!A:A", "LIST", "Limited to list", "This Column is limited to a list of stops.", "Limited to list", "This Column is limited to a list of stops.");
		// Set a dropdown for To
		// Working Validator - Leo doesnt want anymore //$ws->set_column_validation('I',"=Units!A:A", "LIST", "Limited to list", "This Column is limited to a list of stops.", "Limited to list", "This Column is limited to a list of stops.");
	
		
		// Set the Workbook to the first sheet
		$ws->set_active_sheet(0);
		$title = "A Sourcemap";
		if (isset($supplychain->attributes->title) == true) {
			$title = $supplychain->attributes->title;
		}
		// Done! Open on the Client side			
        $ws->send(array('name'=> $title, 'format'=>'Excel5'));
	}
	
}
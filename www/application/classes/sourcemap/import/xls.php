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

class Sourcemap_Import_Xls {
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
		$contentReader = new PHPExcelSourcemap();
		$objPHPExcel = $contentReader->load($xls);
		// Get summary information data
		//$this->_summaryInformation = $ole->getStream($ole->summaryInformation);

		// Get additional document summary information data
		//$this->_documentSummaryInformation = $ole->getStream($ole->documentSummaryInformation);
		/*
		$objReader = new PHPExcel_Reader_Excel5();
		$objReader->setReadDataOnly(true);
		$objReader->setLoadSheetsOnly("Stops");
		$objPHPExcel = $objReader->load("/home/sourcemap/sourcemap/www/assets/downloads/boogieboard.xls");
		$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
		var_dump($objWriter);
		*/
		//$objReader->setLoadSheetsOnly("Hops");
		//$objWriter->save('/home/sourcemap/sourcemap/www/assets/downloads/stops.csv');
		//$objReader->setLoadSheetsOnly("Stops");
		//$writer->save('/home/sourcemap/sourcemap/www/assets/downloads/stops.csv');
		
        //$sc->stops = self::csv2stops($stop_csv, $options);
        //$sc->hops = $hop_csv ? self::csv2hops($hop_csv, $sc->stops, $options) : array();
        //$sc->attributes = array();
        return $sc;
    }

    public static function xls2stops($csv, $o=array()) {
        
        $options = array();
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);

        $csv = Sourcemap_Csv::parse($csv);
        $data = array();
        $raw_headers = array();
        if($headers) {
            $raw_headers = array_shift($csv);
            $headers = array();
        }
        for($i=0; $i<count($raw_headers); $i++) 
            if(strlen(trim($raw_headers[$i])))
                $headers[] = strtolower($raw_headers[$i]);
        foreach($csv as $ri => $row) {
            if($headers && is_array($headers)) {
                $record = array();
                foreach($headers as $hi => $k) {
                    if(isset($row[$hi]))
                        $record[$k] = $row[$hi];
                }
            } else $record = $row;
            if($record)
                $data[] = $record;
        }
        
        if($headers) {
            if(is_null($latcol) || is_null($loncol)) {
                foreach($headers as $i => $h) {
                    if(is_null($latcol) && preg_match('/^lat(itude)?$/i', $h)) {
                        $latcol = $h;
                    } elseif(is_null($loncol) && preg_match('/^(lng)|(lon(g(itude)?)?)$/i', $h)) {
                        $loncol = $h;
                    } elseif((is_null($addresscol) &&  preg_match('/place ?name/i', $h)) || preg_match('/address/i', $h)) {
                        $addresscol = $h;
                    }
                }
                if(is_null($latcol) || is_null($loncol)) {
                    $latcol = $loncol = null;
                    if(is_null($addresscol))
                        if(!isset($this)) {
                            throw new Exception('Missing lat/lon or address column index.');
                        }
                        else{
                            Message::instance()->set('The worksheet you choose may have wrong format, please try again.');
                            $this->request->redirect('/tools/import/google/list');
                        }
                }
            }
            if(is_null($idcol)) {
                foreach($headers as $i => $h) {
                    if(preg_match('/^id$/i', $h)) {
                        $idcol = $h;
                        break;
                    }
                }
            }
        }

        $stops = array();
        foreach($data as $i => $record) {
            if(is_null($addresscol)) {
                if(!isset($record[$latcol], $record[$loncol]))
                    throw new Exception('Missing lat/lon field (record #'.($i+1).').');
            } else {
                if(!isset($record[$addresscol]))
                    throw new Exception('Missing address field (record #'.($i+1).').');
            }
            if($idcol && !isset($record[$idcol]))
                throw new Exception('Missing id field (record #'.($i+1).').');
            elseif($idcol && !is_numeric($record[$idcol]))
                throw new Exception('Id value must be an integer.');
            $new_stop = array(
                'local_stop_id' => $idcol ? (int)$record[$idcol] : $i+1,
                'attributes' => array()
            );
            $lat = null;
            $lon = null;
            foreach($record as $k => $v) {
                if($k == $latcol || $k == $loncol) {
                    if($k == $latcol) $lat = $v;
                    else $lon = $v;
                    continue;
                } elseif($k == $addresscol) {
                    if($results = Sourcemap_Geocoder::geocode($v)) {
                        $result = $results[0];
                        $lat = (float)$result->lat;
                        $lon = (float)$result->lng;
                        if(!isset($record['placename']))
                            $new_stop['attributes']['placename'] = $result->placename;
                    } else {
                        throw new Exception('Could not geocode: "'.$v.'".');
                    }
                }
                $new_stop['attributes'][$k] = $v;
            }
            if(!isset($new_stop['attributes']['placename']) && $lat && $lon) {
                $results = Sourcemap_Geocoder::geocode((new Sourcemap_Proj_Point($lon, $lat)));
                if($results) {
                    $result = $results[0];
                    //$lat = $result->lat;
                    //$lon = $result->lng;
                    if(!isset($record['placename']))
                        $new_stop['attributes']['placename'] = $result->placename;
                }
            }
            if(is_null($lon) || is_null($lat)) throw new Exception('No lat/lon.');
            $from_pt = new Sourcemap_Proj_Point($lon, $lat);
            $new_stop['geometry'] = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', $from_pt)->toGeometry();
            $stops[] = (object)$new_stop;
        }
        return $stops;

    }

    public static function xls2hops($csv, $stops, $o=array()) {
        
        $options = array();
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);

        $csv = Sourcemap_Csv::parse($csv);

        $raw_headers = array();
        if($headers) {
            $raw_headers = array_shift($csv);
            $headers = array();
            for($i=0; $i<count($raw_headers); $i++)
                if(strlen(trim($raw_headers[$i])))
                    $headers[] = strtolower($raw_headers[$i]);
            foreach($headers as $i => $h) {
                if(is_null($fromcol) && preg_match('/^from(_?stop)?$/i', $h)) {
                    $fromcol = $h;
                } elseif(is_null($tocol) && preg_match('/^to(_?stop)?$/i', $h)) {
                    $tocol = $h;
                }
            }
        }

        if(!$fromcol || !$tocol) 
            throw new Exception('To and from columns required.');

        $data = array();

        foreach($csv as $ri => $row) {
            if($headers && is_array($headers)) {
                $record = array();
                foreach($headers as $hi => $k) {
                    if(isset($row[$hi]))
                        $record[$k] = $row[$hi];
                }
            } else $record = $row;
            if($record)
                $data[] = $record;
        }

        $stops_by_id = array();
        foreach($stops as $sti => $st) {
            $stops_by_id[(integer)$st->local_stop_id] = $st;
        }

        $hops = array();
        foreach($data as $i => $record) {
            if(!isset($record[$fromcol]) || !is_numeric($record[$fromcol]))
                throw new Exception('Missing or invalid from field at record #'.($i+1).'.');
            if(!isset($record[$tocol]) || !is_numeric($record[$tocol]))
                throw new Exception('Missing or invalid to field at record #'.($i+1).'.');
            $from = $record[$fromcol];
            $to = $record[$tocol];
            if(!isset($stops_by_id[(integer)$from]))
                throw new Exception('From stop in hop does not exist in record #'.($i+1).'.');
            if(!isset($stops_by_id[(integer)$to]))
                throw new Exception('To stop in hop does not exist in record #'.($i+1).'.');
            list($type, $fromcoords) = Sourcemap_Wkt::read($stops_by_id[$from]->geometry);
            list($type, $tocoords) = Sourcemap_Wkt::read($stops_by_id[$to]->geometry);
            $frompt = new Sourcemap_Proj_Point($fromcoords);
            $topt = new Sourcemap_Proj_Point($tocoords);
            $geometry = Sourcemap_Wkt::write(Sourcemap_Wkt::MULTILINESTRING, array($frompt, $topt));
            $new_hop = (object)array(
                'from_stop_id' => $from,
                'to_stop_id' => $to,
                'geometry' => $geometry,
                'attributes' => new stdClass()
            );
            foreach($record as $k => $v) {
                if($k !== $fromcol && $k !== $tocol)
                    $new_hop->attributes->{$k} = $v;
            }
            $hops[] = $new_hop;
        }


        return $hops;
    }
}

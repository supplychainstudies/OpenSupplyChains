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

class Sourcemap_Import_Csv {
    public static $default_options = array(
        'headers' => true, 'latcol' => null,
        'loncol' => null, 'addresscol' => null,
        'idcol' => null, 'fromcol' => null,
        'tocol' => null
    );

    public static function csv2sc($stop_csv, $hop_csv=null, $o=array()) {
        $options = array();
        if(!is_array($o)) $o = (array)$o;
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);
        $sc = new stdClass();
        $sc->stops = self::csv2stops($stop_csv, $options);
        $sc->hops = $hop_csv ? self::csv2hops($hop_csv, $sc->stops, $options) : array();
        $sc->attributes = array();
        return $sc;
    }

    public static function csv2stops($csv, $o=array()) {
		// create a bug check outputer
		//$ex = new PHPExcel();
		//$ex->createSheet();
		//$ex->setActiveSheetIndex(0);
		//$error_list = array();
        $options = array();
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);
		var_dump($csv);
        $csv = Sourcemap_Csv::parse($csv);
        $data = array();
        $raw_headers = array();
        if($headers) {
            $raw_headers = array_shift($csv);
            $headers = array();
        }
		//for($i=0,$size_raw_headers = count($raw_headers); $i<$size_raw_headers; $i++) 
        for($i=0; $i<count($raw_headers); $i++) {
            if(strlen(trim($raw_headers[$i]))) {
                $headers[] = strtolower($raw_headers[$i]);
				//$ex->getActiveSheet()->setCellValuebyColumnAndRow($i,1,$raw_headers[$i]);
			}
		}
        foreach($csv as $ri => $row) {
			//var_dump($row);
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
                    } elseif((is_null($addresscol) &&  preg_match('/place ?name/i', $h)) || preg_match('/address/i', $h) || preg_match('/location/i', $h)) {
                        $addresscol = $h;
                    }
                }
                if(is_null($latcol) || is_null($loncol)) {
                    $latcol = $loncol = null;
                    if(is_null($addresscol))
                        if(!isset($this)) {
                            throw new Exception('Missing lat/lon or address column index.');
							//$error_list[] = 'Missing lat/lon or address/location column index.';
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
		//var_dump($headers);
		//$ex->getActiveSheet()->fromArray($headers, NULL, "A1");
		//$ex->getActiveSheet()->fromArray($data, NULL, "A2");
        $stops = array();
        foreach($data as $i => $record) {
            if(is_null($addresscol)) {
                if(!isset($record[$latcol], $record[$loncol])) {
					//$error_list[] = 'Missing lat/lon field (record #'.($i+1).')';
					//$ex->getActiveSheet()->getStyle('A'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					//$ex->getActiveSheet()->getStyle('A'.$i)->getFill()->getStartColor()->setARGB('FFFF0000');					
                    throw new Exception('Missing lat/lon field (record #'.($i+1).').');
				}
            } else {
                if(!isset($record[$addresscol])) {
					//$error_list[] = 'Missing address field (record #'.($i+1).').';
					//$ex->getActiveSheet()->getStyle('A'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                    throw new Exception('Missing address field (record #'.($i+1).').');
				}
            }
            if($idcol && !isset($record[$idcol])) {
				//$error_list[] = 'Missing id field (record #'.($i+1).').';
                throw new Exception('Missing id field (record #'.($i+1).').');
			}
            elseif($idcol && !is_numeric($record[$idcol])) {
				//$error_list[] = 'Id value must be an integer.';
                throw new Exception('Id value must be an integer.');
			}
            $new_stop = array(
                'local_stop_id' => $idcol ? (int)$record[$idcol] : $i+1,
                'attributes' => array()
            );
            $lat = null;
            $lon = null;
            foreach($record as $k => $v) {
                if($k == $latcol || $k == $loncol) {
					if ($v != "") {
                    	if($k == $latcol) $lat = $v;
                    	else $lon = $v;
					}
                    continue;
                } elseif($k == $addresscol) {
                    if($results = Sourcemap_Geocoder::geocode($v)) {
                        $result = $results[0];
                        $lat = (float)$result->lat;
                        $lon = (float)$result->lng;
                        if(!isset($record['placename']))
                            $new_stop['attributes']['placename'] = $result->placename;
                    } else {
						//$error_list[] = 'Could not geocode: "'.$v.'".';
                        //throw new Exception('Could not geocode: "'.$v.'".');
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
            if(is_null($lon) || is_null($lat)) {
				$lon = 0.0;
				$lat = 0.0;
				//throw new Exception('No lat/lon in record #'.$i);
			}
			var_dump($lon);
			var_dump($lat);
            $from_pt = new Sourcemap_Proj_Point($lon, $lat);
            $new_stop['geometry'] = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', $from_pt)->toGeometry();
            $stops[] = (object)$new_stop;
        }
		//var_dump($ex);
		/*
		if (count($error_list) > 0) {
			var_dump($error_list);
			$err_html = "";
			$errordump = new PHPExcel_Writer_HTML($ex);
			echo $errordump->generateHTMLHeader(true);
			echo $errordump->generateStyles(true);
			//$errordump->setUseInlineCSS(true);
			
			foreach ($error_list as $err) {
				$err_html .= $err."<br />\n";
			}
			$err_html .= $errordump->generateSheetData();
			$quack = new View('error');
			echo $err_html;
			//Request::instance()->redirect('view/'.$new_sc_id);
		} else {
        	return $stops;
		}
		*/
		
		//return $stops;
    }

    public static function csv2hops($csv, $stops, $o=array()) {
        $error_list = array();
        $options = array();
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);

        $csv = Sourcemap_Csv::parse($csv);
        $raw_headers = array();
        if($headers) {
            $raw_headers = array_shift($csv);
            $headers = array();
            for($i=0,$size_raw_headers = count($raw_headers); $i<$size_raw_headers; $i++)
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

        if(!$fromcol || !$tocol) {
			$error_list[] = 'To and from columns required.';
            //throw new Exception('To and from columns required.');
		}
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
            if(!isset($record[$fromcol]) || !is_numeric($record[$fromcol])) {
				
				//throw new Exception('Missing or invalid from field at record #'.($i+1).'.');
			}
                
            if(!isset($record[$tocol]) || !is_numeric($record[$tocol])) {
				$error_list[] = 'Missing or invalid to field at record #'.($i+1).'.';
                //throw new Exception('Missing or invalid to field at record #'.($i+1).'.');
			}
            $from = $record[$fromcol];
            $to = $record[$tocol];
            if(!isset($stops_by_id[(integer)$from])) {
				$error_list[] = 'From stop in hop does not exist in record #'.($i+1).'.';
                //throw new Exception('From stop in hop does not exist in record #'.($i+1).'.');
			}
            if(!isset($stops_by_id[(integer)$to])) {
				$error_list[] = 'To stop in hop does not exist in record #'.($i+1).'.';
                //throw new Exception('To stop in hop does not exist in record #'.($i+1).'.');
			}
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

		if (count($error_list) == 0) {			 
        	return $hops;
		} else {
			var_dump($error_list);
		}
    }
}

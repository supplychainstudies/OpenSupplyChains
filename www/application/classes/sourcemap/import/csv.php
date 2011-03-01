<?php
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
        
        $options = array();
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);

        $csv = Sourcemap_Csv::parse($csv);
        $data = array();

        if($headers) $headers = array_shift($csv);
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
                    } elseif(is_null($addresscol) && (preg_match('/address/i', $h) || preg_match('/place ?name/i', $h))) {
                        $addresscol = $h;
                    }
                }
                if(is_null($latcol) || is_null($loncol)) {
                    $latcol = $loncol = null;
                    if(is_null($addresscol))
                        return $this->_bad_request('Missing lat/lon or address column index.');
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
                        $lat = $result->lat;
                        $lon = $result->lng;
                        $new_stop['attributes']['placename'] = $result->placename;
                    } else {
                        throw new Exception('Could not geocode: "'.$v.'".');
                    }
                }
                $new_stop['attributes'][$k] = $v;
            }
            if(is_null($addresscol) && $lat && $lon) {
                $results = Sourcemap_Geocoder::geocode((new Sourcemap_Proj_Point($lon, $lat)));
                if($results) {
                    $result = $results[0];
                    $lat = $result->lat;
                    $lon = $result->lng;
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

    public static function csv2hops($csv, $stops, $o=array()) {
        
        $options = array();
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);

        $csv = Sourcemap_Csv::parse($csv);

        if($headers) $headers = array_shift($csv);
        foreach($headers as $i => $h) {
            if(is_null($fromcol) && preg_match('/^from(_?stop)?$/i', $h)) {
                $fromcol = $h;
            } elseif(is_null($tocol) && preg_match('/^to(_?stop)?$/i', $h)) {
                $tocol = $h;
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

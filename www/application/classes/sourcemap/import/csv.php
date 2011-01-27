<?php
class Sourcemap_Import_Csv {
    public static $default_options = array(
        'headers' => true, 'latcol' => null,
        'loncol' => null, 'addresscol' => null
    );

    public static function csv2sc($stop_csv, $hop_csv=null, $o=array()) {
        $options = array();
        if(!is_array($o)) $o = (array)$o;
        foreach(self::$default_options as $k => $v)
            $options[$k] = isset($o[$k]) ? $o[$k] : $v;
        extract($options);
        $sc = new stdClass();
        // todo: look for id column!
        $sc->stops = self::csv2stops($stop_csv, $options);
        $sc->hops = $hop_csv ? self::csv2hops($hop_csv, $options) : array();
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
                    } elseif($h == "address" || $h == "placename") {
                        $addresscol = $h;
                    }
                }
                if(is_null($latcol) || is_null($loncol)) {
                    $latcol = $loncol = null;
                    if(is_null($addresscol))
                        return $this->_bad_request('Missing lat/lon or address column index.');
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
            $new_stop = array(
                'local_stop_id' => $i+1,
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
                    } else {
                        throw new Exception('Could not geocode: "'.$v.'".');
                    }
                }
                $new_stop['attributes'][$k] = $v;
            }
            if(is_null($lon) || is_null($lat)) throw new Exception('No lat/lon.');
            $from_pt = new Sourcemap_Proj_Point($lon, $lat);
            $new_stop['geometry'] = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', $from_pt)->toGeometry();
            $stops[] = (object)$new_stop;
        }
        return $stops;

    }

    public static function csv2hops($csv, $options) {
        return array();
    }
}

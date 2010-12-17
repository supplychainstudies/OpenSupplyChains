<?php
class Sourcemap_Proj_Projection {
    public $title = null;
    public $proj_name = null;
    public $units = null;
    public $datum = null;
    public $x0 = 0;
    public $y0 = 0;

    public $srs_code = null;
    public $srs_auth = null;
    public $srs_projnum = null;

    public function __construct($srs_code) {
        // todo: support urn/url/config file
        $this->setSrsCode($srs_code);
        // todo: url loading?
        $def = isset(Sourcemap_Proj::$defs[$srs_code]) ? Sourcemap_Proj::$defs[$srs_code] : false;
        if(!$def) throw new Exception('Def not found for proj "'.$srs_code.'".');
        $this->init(self::parse_def($def));
    }

    public function init($params) {
        print_r($params);
    }

    public function setSrsCode($srs_code) {
        list($code, $auth, $projnum) = self::parse_srs_code($srs_code);
        $this->srs_code = $code;
        $this->srs_auth = $auth;
        $this->srs_projnum = $projnum;
        return $this;
    }

    public static function parse_srs_code($srs_code) {
        $parsed = array();
        if(strpos($srs_code, 'EPSG') === 0) {
            $parsed[] = $srs_code;
            $parsed[] = 'epsg';
            $parsed[] = substr($srs_code, 5);
        } elseif(strpos($srs_code, 'IGNF') === 0) {
            $parsed[] = $srs_code;
            $parsed[] = 'IGNF';
            $parsed[] = substr($srs_code, 5);
        } elseif(strpos($srs_code, 'CRS') === 0) {
            $parsed[] = $srs_code;
            $parsed[] = 'CRS';
            $parsed[] = substr($srs_code, 4);
        } else {
            $parsed[] = null;
            $parsed[] = '';
            $parsed[] = $srs_code;
        }
        return $parsed;
    }
    
    public function parse_def($def) {
        if(!$def) return $def;
        $params = explode('+', $def);
        $parsed = array();
        for($pi=0; $pi<count($params); $pi++) {
            if(!$params[$pi]) continue;
            if(strstr($params[$pi], '=')) {
                list($pkey, $pval) = explode('=', $params[$pi]);
                $pkey = strtolower(trim($pkey));
                $pval = trim($pval);
            } else {
                $pkey = $params[$pi];
                $pval = null;
            }
            switch($pkey) {
                case '';
                    break;
                case 'x_0':
                case 'y_0':
                case 'k_0':
                case 'k':
                    $pkey = substr($pkey, 0, 1).'0';
                case 'a':
                case 'b':
                case 'rf':
                case 'to_meter':
                    $parsed[$pkey] = (float)$pval;
                    break;
                case 'lat_0':
                case 'lat_1':
                case 'lat_2':
                    $parsed[strtr($pkey, '_', '')] = (float)$pval * Sourcemap_Proj::D2R;
                    break;
                case 'lat_ts':
                case 'lon_0':
                    $parsed['long0'] = (float)$pval * Sourcemap_Proj::D2R;
                    break;
                case 'lonc':
                    $pkey = 'longc';
                case 'alpha':
                case 'from_greenwich':
                    $parsed[$pkey] = (float)$pval * Sourcemap_Proj::D2R;
                    break;
                case 'proj':
                    $pkey['proj_name'] = $pval;
                    break;
                case 'datum':
                    $parsed['datum_code'] = $pval;
                    break;
                case 'no_defs':
                    break;
                case 'zone':
                    $parsed[$pkey] = (int)$pval;
                    break;
                case 'south':
                    $parsed['utm_south'] = true;
                    break;
                case 'towgs84':
                    $parsed['datum_params'] = explode(',', $pval);
                default:
                    $parsed[$pkey] = $pval;
                    break;
            }
        }
        return $parsed;
    }

}

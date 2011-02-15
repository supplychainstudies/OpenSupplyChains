<?php
class Sourcemap_Csv {

    const FSTA = 'FSTA';
    const FEND = 'FEND';
    const FVAL = 'FVAL';
    const FESC = 'FESC';
    const FQUO = 'FQUO';

    public static function parse($csv) {
        $lines = explode("\n", $csv);
        $data = array();
        foreach($lines as $i => $line) {
            $data[] = self::parse_csv_row($line);
        }
        return $data;
    }

    public static function parse_csv_row($row) {
        $row = trim($row);
        $vs = array();
        $b = '';
        $st = self::FSTA;
        for($i=0; $i<strlen($row); $i++) {
            $c = $row[$i];
            switch($st) {
                case self::FSTA:
                    switch($c) {
                        case '"':
                            $st = self::FQUO;
                            $b = '';
                            break;
                        case ',':
                            $vs[] = '';
                            break;
                        case ' ':
                            break;
                        default:
                            $b .= $c;
                            $st = self::FVAL;
                            break;
                    }
                    break;
                case self::FQUO:
                    switch($c) {
                        case '"':
                            $st = self::FEND;
                            break;
                        case '\\':
                            $st = self::FESC;
                            break;
                        default:
                            $b .= $c;
                            break;
                    }
                    break;
                case self::FVAL:
                    switch($c) {
                        case ',':
                            $vs[] = $b;
                            $b = '';
                            $st = self::FSTA;
                            break;
                        default:
                            $b .= $c;
                            break;
                    }
                    break;
                case self::FESC:
                    switch($c) {
                        case '"':
                            $b .= $c;
                            $st = self::FQUO;
                            break;
                        default:
                            $b .= '\\';
                            $b .= $c;
                            $st = self::FQUO;
                            break;
                    }
                    break;
                case self::FEND:
                    switch($c) {
                        case ',':
                            $vs[] = $b;
                            $b = '';
                            $st = self::FSTA;
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    break;
            }
        }
        if($b) $vs[] = $b;
        return $vs;
    }

    function make_csv_row($arr, $delim=',', $encap='"') {
        $csv_arr = array();
        foreach($arr as $i => $s) {
            if($encap) $s = str_replace($encap, '\\'.$encap, $s);
            $csv_arr[] = (string)$s == '' ? '' : $encap.$s.$encap;
        }
        return implode($delim, $csv_arr);
    }
}

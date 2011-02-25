<?php
class Sourcemap_Ip {
    
    public static function parse_dotip($dot_ip) {
        if(!preg_match('/(\d{1,3}\.){3}\d{1,3}/', $dot_ip))
            return false;
        $pts = explode('.', $dot_ip);
        foreach($pts as $i => $pt) {
            $pt = (int)$pt;
            if($pt < 0 || $pt > 255)
                return false;
            else
                $pts[$i] = $pt;
        }
        return $pts;
    }

    public static function dot2dec($dot_ip) {
        if(!($pts = self::parse_dotip($dot_ip)))
            return false;
        $pl = 0;
        $d = 0;
        while(count($pts)) {
            $pt = array_pop($pts);
            $d += $pt * pow(256, $pl++);
        }
        return $d;
    }

    public static function dec2dot($dec_ip) {
        $dec_ip = (int)$dec_ip;
        $d = array();
        $pl = 3;
        $cur = 0;
        do {
            $div = pow(256, $pl--);
            $r = $dec_ip % $div;
            $cur = ($dec_ip-$r) / $div; 
            $d[] = $cur;
            $dec_ip = $r;
        } while($pl>=0);
        return join('.', $d);
    }

    public static function find_ip($ip) {
        return Model::factory('iploc')->find($ip);
    }
}

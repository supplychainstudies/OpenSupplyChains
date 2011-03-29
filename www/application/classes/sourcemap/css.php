<?php
class Sourcemap_CSS {
    
    public static $convert_less = false;

    public static function link_tags() {
        $args = func_get_args();
        $styles = array();
        foreach($args as $ai => $arg) {
            if(is_array($arg)) {
                foreach($arg as $aai => $a) $styles[] = $a;
            } else {
                $styles[] = $arg;
            }
        }
        $tags = array();
        foreach($styles as $si => $style) {
            if(self::$convert_less)
                $style = preg_replace('/\.less$/', '.css', $style);
            
            if(preg_match('/\.less$/', $style)) {
                // add revision as GET param to avoid old, cached css/less
                if($rev = Sourcemap::revision()) {
                    $style .= '?_v='.$rev;
                }
                $tags[] = '<link rel="stylesheet/less" href="'.$style.'" type="text/css" />';
            } else {
                // add revision as GET param to avoid old, cached css/less
                if($rev = Sourcemap::revision()) {
                    $style .= '?_v='.$rev;
                }
                $tags[] = '<link rel="stylesheet" href="'.$style.'" type="text/css"/>';
            }
        }
        $tags = join("\n", $tags);
        return $tags;
    }
}

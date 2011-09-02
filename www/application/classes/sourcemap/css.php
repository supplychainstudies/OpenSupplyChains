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

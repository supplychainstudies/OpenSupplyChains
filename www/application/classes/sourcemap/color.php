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

class Sourcemap_Color {

    public $r = 0x00;
    public $g = 0x00;
    public $b = 0x00;

    public $a = 0xff;

    public static function hex2rgb($hex) {
        if(!preg_match('/^#?([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $hex))
            return false;
        if(substr($hex, 0, 1) === '#') {
            $hex = substr($hex, 1);
        }
        list($r, $g, $b) = str_split($hex, floor(strlen($hex)/3));
        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
        return array($r, $g, $b);
    }

    public static function rgb2hex($r=0x00, $g=0x00, $b=0x00) {
        $hex = sprintf('#%02x%02x%02x', $r, $g, $b);
        return $hex;
    }

    public static function rgbarr2hex($rgb) {
        return call_user_func_array(array('self', 'rgb2hex'), $rgb);
    }

    public function __construct() {
        $args = func_get_args();
        if(count($args) === 1) {
            $this->set_hex($args[0]);
        } elseif(count($args) === 3) {
            $this->set_rgbarr($args);
        }
    }

    public function get_rgb() {
        return array($this->r, $this->g, $this->b);
    }

    public function get_rgba() {
        return array($this->r, $this->g, $this->b, $this->a);
    }

    public function get_hex() {
        return self::rgbarr2hex($this->get_rgb());
    }

    public function set_hex($hex) {
        $this->set_rgbarr(self::hex2rgb($hex));
        return $this;
    }

    public function set_rgb($r, $g, $b) {
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
        $this->a = 0xff;
        return $this;
    }

    public function set_rgbarr($arr) {
        list($r, $g, $b) = $arr;
        $this->set_rgb($r, $g, $b);
        return $this;
    }
}

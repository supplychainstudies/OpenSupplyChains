<?php
class Sourcemap_Qrencode {
   public static function encode($str) {
        return QRcode::png($str);
   }
}

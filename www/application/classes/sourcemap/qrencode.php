<?php
class Sourcemap_Qrencode {
   public static function encode($id, $sz=3) {
        $sz = (int)$sz;
        $sz = min(max(1, $sz), 10);              
        $str = $id; 

        return QRcode::png($str, false, QR_ECLEVEL_L, $sz);
   }
}

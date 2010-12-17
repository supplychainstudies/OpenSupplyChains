<?php
class Sourcemap_Proj_Datum {
    public $datum_type;

    public function __construct(Sourcemap_Proj_Projection $proj) {
        $this->datum_type = Sourcemap_Proj::PJD_WGS84;   //default setting
        if ($proj->datumCode && $proj->datumCode == 'none') {
            $this->datum_type = Sourcemap_Proj::PJD_NODATUM;
        }
        if ($proj && $proj->datum_params) {
            for (var i=0; i<$proj->datum_params.length; i++) {
                $proj->datum_params[i]=parseFloat($proj->datum_params[i]);
            }
            if ($proj->datum_params[0] != 0 || $proj->datum_params[1] != 0 || $proj->datum_params[2] != 0 ) {
                $this->datum_type = Sourcemap_Proj::PJD_3PARAM;
            }
            if ($proj->datum_params.length > 3) {
                if ($proj->datum_params[3] != 0 || $proj->datum_params[4] != 0 ||
                    $proj->datum_params[5] != 0 || $proj->datum_params[6] != 0) {
                    $this->datum_type = Sourcemap_Proj::PJD_7PARAM;
                    $proj->datum_params[3] *= Sourcemap_Proj::SEC_TO_RAD;
                    $proj->datum_params[4] *= Sourcemap_Proj::SEC_TO_RAD;
                    $proj->datum_params[5] *= Sourcemap_Proj::SEC_TO_RAD;
                    $proj->datum_params[6] = ($proj->datum_params[6]/1000000.0) + 1.0;
                }
            }
        }
        if($proj) {
            $this->a = $proj->a;    //datum object also uses these values
            $this->b = $proj->b;
            $this->es = $proj->es;
            $this->ep2 = $proj->ep2;
            $this->datum_params = $proj->datum_params;
        }
    }

    public static function cmp(Sourcemap_Proj_Datum $a, Sourcemap_Proj_Datum $b) {
        if($a->datum_type != $b->datum_type) {
            return false; // false, datums are not equal
        } else if($a->a != $b->a || abs($a->es - $dest->es) > 0.000000000050) {
            // the tolerence for es is to ensure that GRS80 and WGS84
            // are considered identical
            return false;
        } else if($a->datum_type == Sourcemap_Proj::PJD_3PARAM) {
            $eq = true;
            for($i=0; $i<3; $i++) {
                if($a->datum_params[$i] != $b->datum_params[$i]) {
                    $eq = false;
                }
            }
            return $eq;
        } else if($this->datum_type == Sourcemap_Proj::PJD_7PARAM) {
            $eq = true;
            for($i=0; $i<7; $i++) {
                if($a->datum_params[$i] != $b->datum_params[$i]) {
                    $eq = false;
                    break;
                }
            }
            return $eq;
        } else if($this->datum_type == Sourcemap_Proj::PJD_GRIDSHIFT) {
            throw new Exception('Gridshift not implemented.');
        } else {
            return true; // datums are equal
        }
    }
}

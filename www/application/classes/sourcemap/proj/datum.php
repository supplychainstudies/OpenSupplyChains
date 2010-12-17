<?php
class Sourcemap_Proj_Datum {
    public $datum_type;

    public function __construct(Sourcemap_Proj_Projection $proj) {
        $this->datum_type = Sourcemap_Proj::PJD_WGS84;   //default setting
        if(isset($proj->datum_code) && $proj->datum_code == 'none') {
            $this->datum_type = Sourcemap_Proj::PJD_NODATUM;
        }
        if(isset($proj->datum_params) && $proj->datum_params) {
            for($i=0; $i<count($proj->datum_params); $i++) {
                $proj->datum_params[$i] = (float)$proj->datum_params[$i];
            }
            if ($proj->datum_params[0] != 0 || $proj->datum_params[1] != 0 || $proj->datum_params[2] != 0 ) {
                $this->datum_type = Sourcemap_Proj::PJD_3PARAM;
            }
            if (count($proj->datum_params)> 3) {
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
            $this->datum_params = isset($proj->datum_params) ? $proj->datum_params : null;
        }
    }

    public static function cmp(Sourcemap_Proj_Datum $a, Sourcemap_Proj_Datum $b) {
        if($a->datum_type != $b->datum_type) {
            return false; // false, datums are not equal
        } else if($a->a != $b->a || abs($a->es - $b->es) > 0.000000000050) {
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
        } else if($a->datum_type == Sourcemap_Proj::PJD_GRIDSHIFT) {
            throw new Exception('Gridshift not implemented.');
        } else {
            return true; // datums are equal
        }
    }

    public function geodetic_to_geocentric($pt) {
        $longitude = $p->x;
        $latitude = $p->y;
        $height = $p->z ? $p->z : 0;   //Z value not always supplied
        /*
         ** Don't blow up if Latitude is just a little out of the value
         ** range as it may just be a rounding issue.  Also removed longitude
         ** test, it should be wrapped by Math.cos() and Math.sin().  NFW for PROJ.4, Sep/2001.
         */
        if($latitude < -Sourcemap_Proj::HALF_PI && $latitude > -1.001 * Sourcemap_Proj::HALF_PI ) {
            $latitude = -Sourcemap_Proj::HALF_PI;
        } else if( $latitude > Sourcemap_Proj::HALF_PI && $latitude < 1.001 * Sourcemap_Proj::HALF_PI ) {
            $latitude = Sourcemap_Proj::HALF_PI;
        } else if (($latitude < -Sourcemap_Proj::HALF_PI) || ($latitude > Sourcemap_Proj::HALF_PI)) {
            /* $latitude out of range */
            throw new Exception('Latitude out of range.');
        }

        if ($longitude > Sourcemap_Proj::PI) $longitude -= (2*Sourcemap_Proj::PI);
        $sin_lat = sin($latitude);
        $cos_lat = cos($latitude);
        $sin2_lat = $sin_lat * $sin_lat;
        $Rn = $this->a / (sqrt(1.0e0 - $this->es * $sin2_lat));
        $X = ($Rn + $height) * $cos_lat * cos($longitude);
        $Y = ($Rn + $height) * $cos_lat * sin($longitude);
        $Z = (($Rn * (1 - $this->es)) + $height) * $sin_lat;

        $p->x = X;
        $p->y = Y;
        $p->z = Z;
        return $this;
    } // cs_geodetic_to_geocentric()


    public function geocentric_to_geodetic($pt) {
        /* local defintions and variables */
        /* end-criterium of loop, accuracy of sin(Latitude) */
        var genau = 1.E-12;
        var genau2 = (genau*genau);
        var maxiter = 30;

        var P;        /* distance between semi-minor axis and location */
        var RR;       /* distance between center and location */
        var CT;       /* sin of geocentric latitude */
        var ST;       /* cos of geocentric latitude */
        var RX;
        var RK;
        var RN;       /* Earth radius at location */
        var CPHI0;    /* cos of start or old geodetic latitude in iterations */
        var SPHI0;    /* sin of start or old geodetic latitude in iterations */
        var CPHI;     /* cos of searched geodetic latitude */
        var SPHI;     /* sin of searched geodetic latitude */
        var SDPHI;    /* end-criterium: addition-theorem of sin(Latitude(iter)-Latitude(iter-1)) */
        var At_Pole;     /* indicates location is in polar region */
        var iter;        /* # of continous iteration, max. 30 is always enough (s.a.) */

        var X = p.x;
        var Y = p.y;
        var Z = p.z ? p.z : 0.0;   //Z value not always supplied
        var Longitude;
        var Latitude;
        var Height;

        At_Pole = false;
        P = Math.sqrt(X*X+Y*Y);
        RR = Math.sqrt(X*X+Y*Y+Z*Z);

        /*      special cases for latitude and longitude */
        if (P/this.a < genau) {

            /*  special case, if P=0. (X=0., Y=0.) */
            At_Pole = true;
            Longitude = 0.0;

            /*  if (X,Y,Z)=(0.,0.,0.) then Height becomes semi-minor axis
             *  of ellipsoid (=center of mass), Latitude becomes PI/2 */
            if (RR/this.a < genau) {
                Latitude = Proj4js.common.HALF_PI;
                Height   = -this.b;
                return;
            }
        } else {
            /*  ellipsoidal (geodetic) longitude
             *  interval: -PI < Longitude <= +PI */
            Longitude=Math.atan2(Y,X);
        }

        /* --------------------------------------------------------------
         * Following iterative algorithm was developped by
         * "Institut für Erdmessung", University of Hannover, July 1988.
         * Internet: www.ife.uni-hannover.de
         * Iterative computation of CPHI,SPHI and Height.
         * Iteration of CPHI and SPHI to 10**-12 radian resp.
         * 2*10**-7 arcsec.
         * --------------------------------------------------------------
         */
        CT = Z/RR;
        ST = P/RR;
        RX = 1.0/Math.sqrt(1.0-this.es*(2.0-this.es)*ST*ST);
        CPHI0 = ST*(1.0-this.es)*RX;
        SPHI0 = CT*RX;
        iter = 0;

        /* loop to find sin(Latitude) resp. Latitude
         * until |sin(Latitude(iter)-Latitude(iter-1))| < genau */
        do
        {
            iter++;
            RN = this.a/Math.sqrt(1.0-this.es*SPHI0*SPHI0);

            /*  ellipsoidal (geodetic) height */
            Height = P*CPHI0+Z*SPHI0-RN*(1.0-this.es*SPHI0*SPHI0);

            RK = this.es*RN/(RN+Height);
            RX = 1.0/Math.sqrt(1.0-RK*(2.0-RK)*ST*ST);
            CPHI = ST*(1.0-RK)*RX;
            SPHI = CT*RX;
            SDPHI = SPHI*CPHI0-CPHI*SPHI0;
            CPHI0 = CPHI;
            SPHI0 = SPHI;
        }
        while (SDPHI*SDPHI > genau2 && iter < maxiter);

        /*      ellipsoidal (geodetic) latitude */
        Latitude=Math.atan(SPHI/Math.abs(CPHI));

        p.x = Longitude;
        p.y = Latitude;
        p.z = Height;
        return p;
  } // cs_geocentric_to_geodetic()

  /** Convert_Geocentric_To_Geodetic
   * The method used here is derived from 'An Improved Algorithm for
   * Geocentric to Geodetic Coordinate Conversion', by Ralph Toms, Feb 1996
   */
    public static function geocentric_to_geodetic_noniter($pt) {
        var X = p.x;
        var Y = p.y;
        var Z = p.z ? p.z : 0;   //Z value not always supplied
        var Longitude;
        var Latitude;
        var Height;

        var W;        /* distance from Z axis */
        var W2;       /* square of distance from Z axis */
        var T0;       /* initial estimate of vertical component */
        var T1;       /* corrected estimate of vertical component */
        var S0;       /* initial estimate of horizontal component */
        var S1;       /* corrected estimate of horizontal component */
        var Sin_B0;   /* Math.sin(B0), B0 is estimate of Bowring aux variable */
        var Sin3_B0;  /* cube of Math.sin(B0) */
        var Cos_B0;   /* Math.cos(B0) */
        var Sin_p1;   /* Math.sin(phi1), phi1 is estimated latitude */
        var Cos_p1;   /* Math.cos(phi1) */
        var Rn;       /* Earth radius at location */
        var Sum;      /* numerator of Math.cos(phi1) */
        var At_Pole;  /* indicates location is in polar region */

        X = parseFloat(X);  // cast from string to float
        Y = parseFloat(Y);
        Z = parseFloat(Z);

        At_Pole = false;
        if (X != 0.0)
        {
            Longitude = Math.atan2(Y,X);
        }
        else
        {
            if (Y > 0)
            {
                Longitude = Proj4js.common.HALF_PI;
            }
            else if (Y < 0)
            {
                Longitude = -Proj4js.common.HALF_PI;
            }
            else
            {
                At_Pole = true;
                Longitude = 0.0;
                if (Z > 0.0)
                {  /* north pole */
                    Latitude = Proj4js.common.HALF_PI;
                }
                else if (Z < 0.0)
                {  /* south pole */
                    Latitude = -Proj4js.common.HALF_PI;
                }
                else
                {  /* center of earth */
                    Latitude = Proj4js.common.HALF_PI;
                    Height = -this.b;
                    return;
                }
            }
        }
        W2 = X*X + Y*Y;
        W = Math.sqrt(W2);
        T0 = Z * Proj4js.common.AD_C;
        S0 = Math.sqrt(T0 * T0 + W2);
        Sin_B0 = T0 / S0;
        Cos_B0 = W / S0;
        Sin3_B0 = Sin_B0 * Sin_B0 * Sin_B0;
        T1 = Z + this.b * this.ep2 * Sin3_B0;
        Sum = W - this.a * this.es * Cos_B0 * Cos_B0 * Cos_B0;
        S1 = Math.sqrt(T1*T1 + Sum * Sum);
        Sin_p1 = T1 / S1;
        Cos_p1 = Sum / S1;
        Rn = this.a / Math.sqrt(1.0 - this.es * Sin_p1 * Sin_p1);
        if (Cos_p1 >= Proj4js.common.COS_67P5)
        {
            Height = W / Cos_p1 - Rn;
        }
        else if (Cos_p1 <= -Proj4js.common.COS_67P5)
        {
            Height = W / -Cos_p1 - Rn;
        }
        else
        {
            Height = Z / Sin_p1 + Rn * (this.es - 1.0);
        }
        if (At_Pole == false)
        {
            Latitude = Math.atan(Sin_p1 / Cos_p1);
        }

        p.x = Longitude;
        p.y = Latitude;
        p.z = Height;
        return p;
  } // geocentric_to_geodetic_noniter()

  /****************************************************************/
  // pj_geocentic_to_wgs84( p )
  //  p = point to transform in geocentric coordinates (x,y,z)
    public static function geocentric_to_wgs84($pt) {

        if( this.datum_type == Proj4js.common.PJD_3PARAM )
        {
            // if( x[io] == HUGE_VAL )
            //    continue;
            p.x += this.datum_params[0];
            p.y += this.datum_params[1];
            p.z += this.datum_params[2];

        }
        else if (this.datum_type == Proj4js.common.PJD_7PARAM)
        {
            var Dx_BF =this.datum_params[0];
            var Dy_BF =this.datum_params[1];
            var Dz_BF =this.datum_params[2];
            var Rx_BF =this.datum_params[3];
            var Ry_BF =this.datum_params[4];
            var Rz_BF =this.datum_params[5];
            var M_BF  =this.datum_params[6];
            // if( x[io] == HUGE_VAL )
            //    continue;
            var x_out = M_BF*(       p.x - Rz_BF*p.y + Ry_BF*p.z) + Dx_BF;
            var y_out = M_BF*( Rz_BF*p.x +       p.y - Rx_BF*p.z) + Dy_BF;
            var z_out = M_BF*(-Ry_BF*p.x + Rx_BF*p.y +       p.z) + Dz_BF;
            p.x = x_out;
            p.y = y_out;
            p.z = z_out;
        }
  } // cs_geocentric_to_wgs84

  /****************************************************************/
  // pj_geocentic_from_wgs84()
  //  coordinate system definition,
  //  point to transform in geocentric coordinates (x,y,z)
    public static function geocentric_from_wgs84($pt) {

        if( this.datum_type == Proj4js.common.PJD_3PARAM ) {
            //if( x[io] == HUGE_VAL )
            //    continue;
            p.x -= this.datum_params[0];
            p.y -= this.datum_params[1];
            p.z -= this.datum_params[2];

        }
        else if (this.datum_type == Proj4js.common.PJD_7PARAM) {
            var Dx_BF =this.datum_params[0];
            var Dy_BF =this.datum_params[1];
            var Dz_BF =this.datum_params[2];
            var Rx_BF =this.datum_params[3];
            var Ry_BF =this.datum_params[4];
            var Rz_BF =this.datum_params[5];
            var M_BF  =this.datum_params[6];
            var x_tmp = (p.x - Dx_BF) / M_BF;
            var y_tmp = (p.y - Dy_BF) / M_BF;
            var z_tmp = (p.z - Dz_BF) / M_BF;
            //if( x[io] == HUGE_VAL )
            //    continue;

            p.x =        x_tmp + Rz_BF*y_tmp - Ry_BF*z_tmp;
            p.y = -Rz_BF*x_tmp +       y_tmp + Rx_BF*z_tmp;
            p.z =  Ry_BF*x_tmp - Rx_BF*y_tmp +       z_tmp;
    } //cs_geocentric_from_wgs84()
  }

}

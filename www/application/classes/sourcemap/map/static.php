<?php
class Sourcemap_Map_Static {

    const MAX_SZ = 524288;//262144;
    const MIN_SZ = 13172;//65536;

    const MAX_W = 9;
    const MIN_W = 6;
    const MAX_H = 3;
    const MIN_H = 2;

    const MAX_STOP_SZ = 256;
    const MIN_STOP_SZ = 24;
    const DEFAULT_ST_COLOR = '#006600';

    const BBOFFLAT = .10; // degrees
    const BBOFFLON = .25; // degrees

    public $zoom;
    public $w;
    public $h;

    public $tiles_img;

    public $raw_sc;
    public $tile_urls;
    public $bbox;

    public function __construct($raw_sc) {
        $this->raw_sc = $raw_sc;
        $this->bbox = Cloudmade_Tiles::get_sc_bbox($raw_sc);
    }

    public function stitch_tiles() {
        list($y0, $x0, $y1, $x1) = $this->bbox;
        if($x0 == $x1 || $x1 - $x0 < self::BBOFFLON) {
            if($x0 > -180+self::BBOFFLON/2) $x0 -= self::BBOFFLON / 2;
            if($x1 < 180-self::BBOFFLON/2) $x1 += self::BBOFFLON / 2;
        }
        if($y0 == $y1 || $y1 - $y0 < self::BBOFFLAT) {
            if($y0 > -88.5+self::BBOFFLAT/2) $y0 += self::BBOFFLAT / 2;
            if($y1 < 88.5-self::BBOFFLAT/2) $y1 -= self::BBOFFLAT / 2;
        }
        $this->zoom = Cloudmade_Tiles::MAX_ZOOM;

        while((list($cols, $rows) = Cloudmade_Tiles::get_tiles_dim($x0, $y0, $x1, $y1, $this->zoom)) 
            && ($rows*$cols) > self::MAX_H*self::MAX_W) {
                $this->zoom--;
            
        }
        $this->bbox = array($y0, $x0, $y1, $x1);
        while($cols < self::MIN_W) {
            $x1 += self::BBOFFLON;
            error_log($cols." : ".$x1);
            list($cols, $rows) = Cloudmade_Tiles::get_tiles_dim($x0, $y0, $x1, $y1, $this->zoom); 
        }
        $this->tile_numbers = Cloudmade_Tiles::get_tile_numbers($x0, $y0, $x1, $y1, $this->zoom);
        $this->tile_urls = Cloudmade_Tiles::get_tile_urls($this->tile_numbers);
        $this->tiles_bounds = Cloudmade_Tiles::get_tileset_bounds($this->tile_numbers);
        $this->tiles_img = Cloudmade_Tiles::stitch_tiles($this->tile_urls);
        $this->w = imagesx($this->tiles_img);
        $this->h = imagesy($this->tiles_img);
    }

    public function render() {
        $this->stitch_tiles();
        list($nwxt, $nwyt, $throwaway) = $this->tile_numbers[0][0];
        #$se = new Sourcemap_Proj_Point($this->tiles_bounds[3], $this->tiles_bounds[2]);
        #list($sext,$seyt) = Cloudmade_Tiles::get_tile_number($se->y, $se->x, $this->zoom);
        $stops = array();
        foreach($this->raw_sc->stops as $stop) {
            $pt = Sourcemap_Proj_Point::fromGeometry($stop->geometry);
            $pt = Sourcemap_Proj::transform('EPSG:900913', 'WGS84', $pt);
            $lon = $pt->x;
            $lat = $pt->y;
            list($xt, $yt) = Cloudmade_Tiles::get_tile_number($lat, $lon, $this->zoom);
            list($xto, $yto) = Cloudmade_Tiles::get_tile_offset($lat, $lon, $this->zoom);
            $x = ($xt - $nwxt)*256 + $xto;
            $y = ($yt - $nwyt)*256 + $yto;
            $stops[$stop->local_stop_id] = (object)array('stop' => $stop, 'x' => $x, 'y' => $y);
        }
        foreach($this->raw_sc->hops as $i => $hop) {
            $from = $stops[$hop->from_stop_id];
            $to = $stops[$hop->to_stop_id];
            $this->draw_hop2($hop, $from, $to);
            //$this->draw_hop($hop, $from, $to);
        }
        foreach($stops as $sid => $st) {
            $this->draw_stop($st->stop, $st->x, $st->y);
        }
        /*ob_start();
        imagepng($this->tiles_img);
        $imgdata = ob_get_contents();
        ob_end_clean();
        return $imgdata;*/
        return $this->tiles_img;
    }

    public function draw_stop($stop, $x, $y) {
        $sz = isset($stop->attributes->size) ? $stop->attributes->size : self::MIN_STOP_SZ;
        $sz = min(self::MAX_STOP_SZ, max(self::MIN_STOP_SZ, $sz));
        if(isset($stop->attributes->color)) {
            $smcolor = new Sourcemap_Color($stop->attributes->color);
        } else {
            $smcolor = new Sourcemap_Color(self::DEFAULT_ST_COLOR);
        }
        list($r, $g, $b) = $smcolor->get_rgb();
        $color = imagecolorallocatealpha($this->tiles_img, $r, $g, $b, (0xff/2)*.5);
        imagefilledellipse($this->tiles_img, $x, $y, $sz, $sz, $color);
        return true;
    }

    public function draw_hop($hop, $from, $to) {
        
        $xo = $this->w / 2;
        $yo = $this->h / 2;
        $xf = $from->x - $xo;
        $yf = $from->y - $yo;
        $xt = $to->x - $xo;
        $yt = $to->y - $yo;

        $mx = $xf+(($xt-$xf)/2);
        $my = $yf+(($yt-$yf)/2);

        $w = abs($xt-$xf);
        $h = abs($yt-$yf);

        $fa = atan2($yo+$yf, $xo+$xf);
        $ta = atan2($yo+$yt, $xo+$xt);

        /*header('Content-Type: text/plain');
        print "f: {$from->x}, {$from->y}\n";
        print "t: {$to->x}, {$to->y}\n";
        print "o: $xo, $yo\n";
        print "f: $xf, $yf\n";
        print "t: $xt, $yt\n";
        print "m: $mx, $my\n";
        die();*/

        $sz = 4;
        $color = imagecolorallocate($this->tiles_img, 0xff, 0x00, 0x00);
        imagefilledellipse($this->tiles_img, $xo+$mx, $yo+$my, $sz, $sz, $color);
        imagearc($this->tiles_img, $xo+$mx, $yo+$my, $w, $h, $fa, $ta, $color);
    }
    
    public function draw_hop2($hop, $from, $to) {
        $color = imagecolorallocate($this->tiles_img, 0x00, 0x00, 0x00);
        imageline($this->tiles_img, $from->x, $from->y, $to->x, $to->y, $color);
    }


    // methods below from http://personal.3d-box.com/php/filledellipseaa.php
    // Parses a color value to an array.
    public static function color2rgb($color) {
        $rgb = array();

        $rgb[] = 0xFF & ($color >> 16);
        $rgb[] = 0xFF & ($color >> 8);
        $rgb[] = 0xFF & ($color >> 0);

        return $rgb;
    }

    // Parses a color value to an array.
    public static function color2rgba($color) {
        $rgb = array();

        $rgb[] = 0xFF & ($color >> 16);
        $rgb[] = 0xFF & ($color >> 8);
        $rgb[] = 0xFF & ($color >> 0);
        $rgb[] = 0xFF & ($color >> 24);

        return $rgb;
    }


    public static function imagefilledellipseaa_Plot4EllipsePoints(&$im, $CX, $CY, $X, $Y, $color, $t) {
        imagesetpixel($im, $CX+$X, $CY+$Y, $color); //{point in quadrant 1}
        imagesetpixel($im, $CX-$X, $CY+$Y, $color); //{point in quadrant 2}
        imagesetpixel($im, $CX-$X, $CY-$Y, $color); //{point in quadrant 3}
        imagesetpixel($im, $CX+$X, $CY-$Y, $color); //{point in quadrant 4}

        $aColor = self::color2rgba($color);
        $mColor = imagecolorallocate($im, $aColor[0], $aColor[1], $aColor[2]);
        if ($t == 1)
        {
            imageline($im, $CX-$X, $CY-$Y+1, $CX+$X, $CY-$Y+1, $mColor);
            imageline($im, $CX-$X, $CY+$Y-1, $CX+$X, $CY+$Y-1, $mColor);
        } else {
            imageline($im, $CX-$X+1, $CY-$Y, $CX+$X-1, $CY-$Y, $mColor);
            imageline($im, $CX-$X+1, $CY+$Y, $CX+$X-1, $CY+$Y, $mColor);
        }
        imagecolordeallocate($im, $mColor);
    }

    // Adapted from http://homepage.smc.edu/kennedy_john/BELIPSE.PDF
    public static function imagefilledellipseaa(&$im, $CX, $CY, $Width, $Height, $color) {

        $XRadius = floor($Width/2);
        $YRadius = floor($Height/2);

        $baseColor = self::color2rgb($color);

        $TwoASquare = 2*$XRadius*$XRadius;
        $TwoBSquare = 2*$YRadius*$YRadius;
        $X = $XRadius;
        $Y = 0;
        $XChange = $YRadius*$YRadius*(1-2*$XRadius);
        $YChange = $XRadius*$XRadius;
        $EllipseError = 0;
        $StoppingX = $TwoBSquare*$XRadius;
        $StoppingY = 0;

        $alpha = 77;    
        $color = imagecolorexactalpha($im, $baseColor[0], $baseColor[1], $baseColor[2], $alpha);
        while ($StoppingX >= $StoppingY)  { // {1st set of points, y' > -1}
            self::imagefilledellipseaa_Plot4EllipsePoints($im, $CX, $CY, $X, $Y, $color, 0);
            $Y++;
            $StoppingY += $TwoASquare;
            $EllipseError += $YChange;
            $YChange += $TwoASquare;
            if ((2*$EllipseError + $XChange) > 0) {
                $X--;
                $StoppingX -= $TwoBSquare;
                $EllipseError += $XChange;
                $XChange += $TwoBSquare;
            }

            // decide how much of pixel is filled.
            $filled = $X - sqrt(($XRadius*$XRadius - (($XRadius*$XRadius)/($YRadius*$YRadius))*$Y*$Y));
            $alpha = abs(90*($filled)+37);
            imagecolordeallocate($im, $color);
            $color = imagecolorexactalpha($im, $baseColor[0], $baseColor[1], $baseColor[2], $alpha);
        }
        // { 1st point set is done; start the 2nd set of points }

        $X = 0;
        $Y = $YRadius;
        $XChange = $YRadius*$YRadius;
        $YChange = $XRadius*$XRadius*(1-2*$YRadius);
        $EllipseError = 0;
        $StoppingX = 0;
        $StoppingY = $TwoASquare*$YRadius;
        $alpha = 77;    
        $color = imagecolorexactalpha($im, $baseColor[0], $baseColor[1], $baseColor[2], $alpha);

        while ($StoppingX <= $StoppingY) { // {2nd set of points, y' < -1}
            self::imagefilledellipseaa_Plot4EllipsePoints($im, $CX, $CY, $X, $Y, $color, 1);
            $X++;
            $StoppingX += $TwoBSquare;
            $EllipseError += $XChange;
            $XChange += $TwoBSquare;
            if ((2*$EllipseError + $YChange) > 0)
            {
                $Y--;
                $StoppingY -= $TwoASquare;
                $EllipseError += $YChange;
                $YChange += $TwoASquare;
            }

            // decide how much of pixel is filled.
            $filled = $Y - sqrt(($YRadius*$YRadius - (($YRadius*$YRadius)/($XRadius*$XRadius))*$X*$X));
            $alpha = abs(90*($filled)+37);
            imagecolordeallocate($im, $color);
            $color = imagecolorexactalpha($im, $baseColor[0], $baseColor[1], $baseColor[2], $alpha);
        }
    }
}

<?php
class Sourcemap_Map_Static {

    const MAX_ZOOM = 17;
    const MIN_ZOOM = 1;

    const MAX_SZ = 128;
    const MIN_SZ = 16;

    const DEFAULT_ST_COLOR = '#006600';

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
        $this->tiles_bounds = Cloudmade_Tiles::get_tileset_bounds(Cloudmade_Tiles::get_tile_numbers($x0, $y0, $x1, $y1));
        $this->zoom = self::MAX_ZOOM;
        $this->tile_urls = Cloudmade_Tiles::get_tile_urls($x0, $y0, $x1, $y1, &$this->zoom);
        $this->tiles_img = Cloudmade_Tiles::stitch_tiles($this->tile_urls);
        $this->w = imagesx($this->tiles_img);
        $this->h = imagesy($this->tiles_img);
    }

    public function render() {
        $this->stitch_tiles();
        $nw = new Sourcemap_Proj_Point($this->tiles_bounds[1], $this->tiles_bounds[0]);
        list($nwxt,$nwyt) = Cloudmade_Tiles::get_tile_number($nw->y, $nw->x, $this->zoom);
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
        }
        foreach($stops as $sid => $st) {
            $this->draw_stop($st->stop, $st->x, $st->y);
        }
        ob_start();
        imagepng($this->tiles_img);
        $imgdata = ob_get_contents();
        ob_end_clean();
        return $imgdata;
    }

    public function draw_stop($stop, $x, $y) {
        $sz = isset($stop->attributes->size) ? $stop->attributes->size : self::MIN_SZ;
        $sz = min(self::MAX_SZ, max(self::MIN_SZ, $sz));
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

    public function draw_hop2($hop, $from, $to) {
        $color = imagecolorallocate($this->tiles_img, 0x00, 0x00, 0x00);
        imageline($this->tiles_img, $from->x, $from->y, $to->x, $to->y, $color);
    }

    public function draw_hop($hop, $from, $to) {

        $color = imagecolorallocate($this->tiles_img, 0x00, 0x00, 0x00);

        $dx = $to->x - $from->x;
        $dy = $to->y - $from->y;

        $theta = (pi()/2) - atan($dy/$dx);
        $maxdisp = sqrt($dx*$dx+$dy*$dy) * 0.03;
        $resolution = 64;

        $x = $from->x;
        $y = $from->y;

        if($dx == 0 && $dy == 0) {
            // pass  -  draw straight line
        } else {
            $absintheta = abs(sin($theta));
            $abcostheta = abs(cos($theta));
            for($p=0; $p<$resolution; $p++) {
                $relamt = sin($p/$resolution*pi()) * $maxdisp;
                if($absintheta < $abcostheta) {
                    $relamt *= abs(sin(pi()*$dx/$dy));
                }
                $ddx = cos($theta+pi()) * $relamt;
                $ddy = sin(-$theta) * $relamt;

                $x1 = $from->x + ($dx*$p/$resolution) + $ddx;
                $y1 = $from->y + ($dy*$p/$resolution) + $ddy;

                imagesetthickness($this->tiles_img, 8);
                imageline($this->tiles_img, $x, $y, $x1, $y1, $color);

                $x = $x1;
                $y = $y1;
            }
        }
        return true;
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

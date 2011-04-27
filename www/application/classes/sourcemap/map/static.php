<?php
class Sourcemap_Map_Static {

    const MAX_ZOOM = 12;
    const MIN_ZOOM = 1;

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
        $this->tile_urls = Cloudmade_Tiles::get_tile_urls($x0, $y0, $x1, $y1);
        $this->tiles_img = Cloudmade_Tiles::stitch_tiles($this->tile_urls);
        $this->w = imagesx($this->tiles_img);
        $this->h = imagesy($this->tiles_img);
    }

    public function render() {
        $this->stitch_tiles();
        foreach($this->raw_sc->stops as $stop) $this->draw_stop($stop);
        return $this->tiles_img;
    }

    public function draw_stop($stop) {
        /*if($pt = Sourcemap_Proj_Point::fromGeometry($stop->geometry)) {
            $pt = Sourcemap_Proj::transform('EPSG:900913', 'WGS84', $pt);
        } else return false;*/
        $pt = Sourcemap_Proj_Point::fromGeometry($stop->geometry);
        $nw = new Sourcemap_Proj_Point($this->tiles_bounds[1], $this->tiles_bounds[0]);
        $nw = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', $nw);
        $se = new Sourcemap_Proj_Point($this->tiles_bounds[3], $this->tiles_bounds[2]);
        $se = Sourcemap_Proj::transform('WGS84', 'EPSG:900913', $se);
        $xsc = $this->w / abs($se->x - $nw->x);
        $ysc = $this->h / abs($nw->y - $se->y);
        $x = ($xsc * abs($pt->x - $nw->x));
        $y = ($ysc * abs($pt->y - $nw->y));
        $color = imagecolorallocate($this->tiles_img, 0xa0, 0x00, 0xa0);
        imagefilledellipse($this->tiles_img, $x, $y, 10, 10, $color);
        return true;
    }
}

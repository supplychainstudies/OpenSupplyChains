<?php

class Sourcemap_Plugin {

    public $name = null;
    public $path = null;

    public function __construct($nm, $path) {
        $this->name = $nm;
        $this->path = $path;
    }

    public function __destruct() {
        // path
    }
}

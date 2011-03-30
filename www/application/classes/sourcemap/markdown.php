<?php
class Sourcemap_Markdown {
    public static function parse($str) {
        static $parser;
        if(!$parser) {
            $parser = new Markdown_Parser();
        }
        return Markdown($str);
    }
}

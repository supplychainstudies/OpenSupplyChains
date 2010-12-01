<?php defined('SYSPATH') or die('No direct script access.');
/**
 * PDOPGSQL database connection.
 *
 * @package    Kohana
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Database_PDOPGSQL extends Kohana_Database_PDOPGSQL {
    public function quote_table($value) {
        return parent::quote_table($value);
    }
    
    public function quote_identifier($value) {
        if(is_string($value)) {
            $value = parent::quote_identifier($value);
            $parts = explode('.', $value);
            foreach($parts as $i => $part) {
                if($part !== '*' && !preg_match('/^"[^"]+"$/', $part)) 
                    $parts[$i] = sprintf('"%s"', trim($part, '"'));
                else $parts[$i] = $part;
            }
            $value = join('.', $parts);
        } elseif(is_object($value) || is_array($value)) {
            $value = parent::quote_identifier($value);
        } 
        return $value;
    }
}

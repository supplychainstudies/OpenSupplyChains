<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
abstract class Sourcemap_Database_Result extends Kohana_Database_Result {

    /**
     * Wraps Kohana database result as_array method for additional functionality.
     * @param string column to use as key in associative array
     * @param mixed column(s) to return as value(s)
     * @return array
     */
    public function as_array($key=null, $values=null) {
        $results = array();
        if(is_array($values)) {
            foreach($this as $row) {
                $result = array();
                foreach($values as $j => $value) {
                    $result[$value] = $row->$value;
                }
                if($key === null) $results[] = (object)$result;
                else $results[$row->$key] = (object)$result;
            }
        } elseif($key !== null && $values === true) {
            foreach($this as $i => $row) {
                $results[$row->$key] = (object)$row->as_array();
            }
        } else {
            $results = parent::as_array($key, $values);
        }
        return $results;
    }

}

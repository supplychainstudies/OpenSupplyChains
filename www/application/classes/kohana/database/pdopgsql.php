<?php defined('SYSPATH') or die('No direct script access.');
/**
 * PDOPGSQL database connection.
 *
 * @package    Kohana
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Kohana_Database_PDOPGSQL extends Kohana_Database_PDO {

    #public function set_charset($charset) {} // TODO: this

    public function get_pdo() {
        return $this->_connection;
    }

    public function list_tables($like = NULL) {
        if (is_string($like)) {
            // Search for table names
            $result = $this->query(
                Database::SELECT, 
                'SELECT table_name FROM information_schema '.
                'WHERE table_schema ='.$this->quote('public').' AND table_name LIKE '.
                $this->quote($like).' ORDER BY table_name', FALSE);
        } else {
            // Find all table names
            $result = $this->query(
                Database::SELECT, 
                'SELECT table_name FROM information_schema.tables '.
                'WHERE table_schema = '.$this->quote('public').' ORDER BY table_name', FALSE);
        }

        $tables = array();
        foreach ($result as $row) {
            // Get the table name from the results
            $tables[] = $row;
        }

        return $tables;
    }

    public function list_columns($table, $like = NULL) {
        // Find all column names
        $result = $this->query(
            Database::SELECT, 
            'SELECT column_name as name FROM information_schema.columns '.
            'WHERE table_name = '.$this->quote($table).' order by column_name', FALSE);
        $columns = array();
        foreach ($result as $row) {
            // Get the column name from the results
            $columns[$row['name']] = $row['name'];
        }
        return $columns;
    }

} // End Database_PDOPGSQL

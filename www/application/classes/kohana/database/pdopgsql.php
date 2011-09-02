<?php defined('SYSPATH') or die('No direct script access.');
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

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

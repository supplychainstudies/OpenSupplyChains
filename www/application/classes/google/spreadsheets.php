<?php
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

class Google_Spreadsheets {

    const URL_BASE = 'https://spreadsheets.google.com/feeds/';

    const URL_LIST = 'spreadsheets/private/full/';

    public static function get_worksheet_cells($oauth_acc_token, $key, $wsid, $scope='private') {
        $scope = $scope ? $scope : 'private';
        $oauth = Google_Oauth::factory(Google_Oauth::SPREADSHEETS);
        $url = self::URL_BASE."cells/$key/$wsid/$scope/full";
        $oauth_header = $oauth->get_token_auth_header($oauth_acc_token, $url);
        $response = Sourcemap_Http_Client::do_get($url, null, array(
            'Authorization' => $oauth_header,
        ));
        if(!$response->status_ok()) 
            throw new Exception('Could not authorize.');
        return self::parse_worksheet_cells($response->body);
    }

    public static function parse_worksheet_cells($atom) {
        $xml = simplexml_load_string($atom);
        $xml->registerXPathNamespace('gs', 'http://schemas.google.com/spreadsheets/2006');
        $rows = $xml->xpath('//gs:rowCount');
        $rows = (int)$rows[0];
        $cols = $xml->xpath('//gs:colCount');
        $cols = (int)$cols[0];
        $cells = array();
        for($i=0; $i<$rows; $i++) {
            $cells[] = array_fill(0, $cols, null);
            //for($j=0; $j<$cols; $j++) $cells[$i][$j] = null;
        }
        foreach($xml->entry as $cell) {
            $gs = $cell->children('gs', true);
            $gs = $gs->attributes();
            $i = $gs['row'] - 1;
            $j = $gs['col'] - 1;
            $cells[$i][$j] = preg_replace('/\s+/', ' ', (string)$cell->content);
        }
        $rows = array();
        foreach($cells as $rowno => $row) {
            $row_not_empty = false;
            foreach($row as $cellno => $cell) {
                $cell = trim($cell);
                $row_not_empty = $row_not_empty || !empty($cell);
            }
            if($row_not_empty) $rows[] = $row;
        }
        return $rows;
    }

    public static function get_sheet_worksheets($oauth_acc_token, $key) {
        $oauth = Google_Oauth::factory(Google_Oauth::SPREADSHEETS);
        $url = self::URL_BASE."worksheets/$key/private/full";
        $oauth_header = $oauth->get_token_auth_header($oauth_acc_token, $url);
        $response = Sourcemap_Http_Client::do_get($url, null, array(
            'Authorization' => $oauth_header,
        ));
        if(!$response->status_ok()) 
            throw new Exception('Could not authorize.');
        return self::parse_worksheets_list($response->body);
    }

    public static function parse_worksheets_list($atom) {
        $xml = simplexml_load_string($atom);
        $wkshts = array();
        foreach($xml->entry as $entry) {
            $wksht = array(
                'title' => (string)$entry->title,
                'content' => (string)$entry->content,
                'updated' => (string)$entry->updated,
            );
            foreach($entry->link as $link) {
                $ids = self::get_key_and_id_from_link($link['href']);
                if($ids) {
                    list($skey, $wsid, $scope) = $ids;
                    $wksht['key'] = $skey;
                    $wksht['id'] = $wsid;
                    $wksht['scope'] = $scope;
                }
            }
            $wkshts[] = $wksht;
        }
        return $wkshts;
    }

    public static function get_list($oauth_acc_token) {
        $oauth = Google_Oauth::factory(Google_Oauth::SPREADSHEETS);
        $url = self::URL_BASE.self::URL_LIST;
        $oauth_header = $oauth->get_token_auth_header($oauth_acc_token, $url);
        $response = Sourcemap_Http_Client::do_get($url, null, array(
            'Authorization' => $oauth_header,
        ));
        if(!$response->status_ok()) 
            throw new Exception('Could not authorize.');
        return self::parse_list($response->body);
    }

    public static function parse_list($atom) {
        $xml = simplexml_load_string($atom);
        $sheets = array();
        foreach($xml->entry as $entry) {
            $sheet = array(
                'title' => (string)$entry->title,
                'content' => (string)$entry->content,
                'updated' => (string)$entry->updated,
                'author_name' => (string)$entry->author->name,
                'author_email' => (string)$entry->author->email
            );
            foreach($entry->link as $link) {
                if($skey = self::get_key_from_link($link['href']))
                    $sheet['key'] = $skey;
            }
            if(isset($sheet['key']))
                $sheets[] = $sheet;
        }
        return $sheets;
    }

    public static function get_key_from_link($link) {
        $pattern = '/https:\/\/spreadsheets\.google\.com\/feeds\/worksheets\/([0-9a-zA-Z_-]+)\/private\/(full|values)/';
        $matches = array();
        $skey = false;
        preg_match($pattern, $link, $matches);
        if($matches) {
            $skey = $matches[1];
        }
        return $skey;
    }

    public static function get_key_and_id_from_link($link) {
        $pattern = '/https:\/\/spreadsheets\.google\.com\/feeds\/list\/([0-9a-zA-Z_-]+)\/([0-9a-zA-Z_-]+)\/private\/(full|values)/';
        $matches = array();
        $skey = false;
        preg_match($pattern, $link, $matches);
        if($matches) {
            $skey = $matches[1];
            $wsid = $matches[2];
            $scope = $matches[3];
            return array($skey, $wsid, $scope);
        } else return false;
    }
}

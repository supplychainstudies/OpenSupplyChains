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

class Pagination extends Kohana_Pagination {
    public function url($page=1) {
        // Clean the page number
    	$page = max(1, (int) $page);

    	// No page number in URLs to first page
    	if ($page === 1 AND ! $this->config['first_page_in_url']) {
    		$page = NULL;
    	}

        $params = isset($this->config['url_params']) 
                && is_array($this->config['url_params']) ? 
                    $this->config['url_params'] : array();
    	switch ($this->config['current_page']['source']) {
    		case 'query_string':
                $params[$this->config['current_page']['key']] = $page;
                return URL::site(Request::current()->uri).URL::query($params);
    		case 'route':
    			return URL::site(Request::current()->uri(array($this->config['current_page']['key'] => $page))).URL::query($params);
    	}

    	return '#';
    }
}

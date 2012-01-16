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

class Controller_Services_Opensearch extends Controller {	
	public function action_index() {		
	$this->request->headers['Content-Type'] = 'application/opensearchdescription+xml';
 
		$open = '<?xml version="1.0" encoding="UTF-8"?>';
		$open .= '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">';
		$open .=  '<ShortName>Sourcemap.com</ShortName>';
		$open .=  '<Description>Search the crowdsourced directory of product supply chains and carbon footprints.</Description>';
		$open .=  '<Tags>sourcemap supply chains carbon footprint</Tags>';
		$open .=  '<Url type="text/html" template="http://sourcemap.com/search?sq={searchTerms}"/>';
		$open .= '</OpenSearchDescription>';

        echo $open;		
	}
}

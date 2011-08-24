<?php
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

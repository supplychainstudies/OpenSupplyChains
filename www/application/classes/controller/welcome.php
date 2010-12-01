<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{
        $this->request->headers['Content-Type'] = 'text/plain; charset=utf-8';
        $this->request->response = 'Hello, world.';
	}

} // End Welcome

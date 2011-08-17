<?php
class Controller_Sitemap extends Sourcemap_Controller_Service {

    public function action_get() {
            $scs = ORM::factory('supplychain')->where(DB::expr('other_perms & '.Sourcemap::READ), '>', 0)
                ->order_by('created', 'desc')
                ->find_all();
            $supplychains = $scs->as_array('id', true);
			// Sitemap instance.
			$sitemap = new Sitemap;

			$urls['index'] = new Sitemap_URL;
			$urls['index'] = new Sitemap_URL;
			$urls['index'] = new Sitemap_URL;
			$urls['index'] = new Sitemap_URL;
			$urls['index'] = new Sitemap_URL;
			
			// New basic sitemap.

			// Set arguments.
			$urls['index']->set_loc('http://google.com')
			    ->set_last_mod(1276800492)
			    ->set_change_frequency('daily')
			    ->set_priority(1);

			// Add it to sitemap.
			$sitemap->add($urls['index']);
			
			$this->response = $sitemap->render();
		    
    }
}

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

class Controller_Blog extends Sourcemap_Controller_Layout {
    
    public $layout = 'base';
    public $template = 'blog';

    protected function _match_alias($alias) {
        $found = ORM::factory('supplychain_alias')
            ->where('site', '=', Kohana::config('sourcemap.site'))
            ->where('alias', '=', $alias)
            ->find_all()->as_array('alias', 'supplychain_id');
        $supplychain_id = $found ? $found[$alias] : -1;
        return $supplychain_id;
    }
    
    public function action_index($supplychain_id) {
        if(!is_numeric($supplychain_id)) {
            $supplychain_id = $this->_match_alias($supplychain_id);
        }
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        $sc = $supplychain->kitchen_sink($supplychain_id);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($supplychain->user_can($current_user_id, Sourcemap::READ)) {
                $this->layout->supplychain_id = $supplychain_id;
               
                // pass supplychain metadeta to template 
                $this->template->supplychain_id = $supplychain_id;
                $this->template->supplychain_date = date('F j, Y', $sc->created );
                $this->template->supplychain_name = isset($sc->attributes->name) ? $sc->attributes->name : "";
                $this->template->supplychain_owner = isset($sc->owner->name) ? $sc->owner->name : "";
                $this->template->supplychain_ownerid = isset($sc->owner->id) ? $sc->owner->id : "";
                $this->template->supplychain_avatar = isset($sc->owner->avatar) ? $sc->owner->avatar : "";
                $this->template->supplychain_desc = isset($sc->attributes->description) ? $sc->attributes->description : "" ;

                $this->layout->scripts = array('blog-view');
                $this->layout->styles = array(
                    'sites/default/assets/styles/reset.css', 
                    'assets/styles/base.less',
                    'assets/styles/general.less'
                );
                // qrcode url
                $qrcode_query = URL::query(array('q' => URL::site('view/'.$supplychain->id, true), 'sz' => 8));
                $this->template->qrcode_url = URL::site('services/qrencode', true).$qrcode_query;
            } else {
                Message::instance()->set('That map is private.');
                $this->request->redirect('browse');
            }
        } else {
            Message::instance()->set('That map could not be found.');
            $this->request->redirect('browse');
        }
    }
}

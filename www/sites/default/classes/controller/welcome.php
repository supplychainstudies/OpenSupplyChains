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

class Controller_Welcome extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'welcome';
    
    public function action_index() {
        $this->layout->scripts = array(
            'sourcemap-core',
            'sourcemap-welcome'
        );
        $this->layout->styles = $this->default_styles;
        $this->layout->styles[] = 'sites/default/assets/styles/slider.less';
        
        $this->layout->page_title = 'Sourcemap: where things come from';
        $recent = Sourcemap_Search::find(array('recent' => 'yes', 'l' => 4));
        $popular = Sourcemap_Search::find(array('comments' => 'yes', 'favorited' => 'yes', 'l' => 4));
        $featured = Sourcemap_Search::find(array('featured' => 'yes', 'l' => 4));
        $morefeatured = Sourcemap_Search::find(array('featured' => 'yes', 'l' => 2, 'o' => 0));

        $this->template->recent = $recent->results;
        $this->template->popular = $popular->results;
        $this->template->featured = $featured->results;
        $this->template->morefeatured = $morefeatured->results;

        $this->template->news = Blognews::fetch(4);
    }
}

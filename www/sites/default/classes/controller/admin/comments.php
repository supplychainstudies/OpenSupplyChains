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

class Controller_Admin_Comments extends Controller_Admin {

    public $template = 'admin/comments/list';

    public function action_index() {

        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Comments', 'admin/comments');

        $default_params = array(
            'p' => 1, 's' => 'date', 'or' => 'desc',
            'l' => 20
        );

        $params = $default_params;
        foreach($default_params as $k => $v)    
            if(isset($_GET[$k]))
                $params[$k] = $_GET[$k];

        $sort_order = strtolower($params['or']);
        switch($sort_order) {
            case 'asc':
            case 'desc':
                break;
            default:
                throw new Exception('Invalid sort order.');
                break;
        }

        $sort = strtolower($params['s']);
        switch($sort) {
            case 'date':
                $sort = 'timestamp';
                break;
            default:
                throw new Exception('Invalid sort.');
                break;
        }

        $limit = max(min($params['l'],100),1);
        $page = max($params['p'],1);
        $offset = $limit * ($page-1);

        $comments = ORM::factory('supplychain_comment');

        $comments->reset(false);
        $count_all = $comments->count_all();

        $comments = $comments->order_by($sort, $sort_order)
            ->limit($limit, $offset)
            ->find_all();

        $comments_arr = array();
        foreach($comments as $i => $comment) {
            $comment_arr = $comment->as_array();
            $comment_arr['body'] = strlen($comment_arr['body']) > 32 ? 
                substr($comment_arr['body'], 0, 32).'...' : $comment_arr['body'];
            $comment_arr['posted'] = date('H:i m/d/Y', $comment_arr['timestamp']);
            $comment_arr['author'] = $comment->user->username;
            $sc_attr = $comment->supplychain->attributes->find_all()
                ->as_array('key', 'value');
            $comment_arr['map_title'] = isset($sc_attr['title']) ? $sc_attr['title'] : 'An Unnamed Sourcemap';
            $comments_arr[] = $comment_arr;
        }

        $pager = Pagination::factory(array(
            'current_page' => array(
                'source' => 'query_string',
                'key' => 'p'
            ),
            'total_items' => $count_all,
            'items_per_page' => $limit,
            'view' => 'pagination/basic',
            'url_params' => $params
        ));

        $this->template->comments = $comments_arr;
        $this->template->pager = $pager;

    }

    public function action_flag($id=null) {
        if(strtolower(Request::$method) == 'post') {
            if($id) {
                $comment = ORM::factory('supplychain_comment', $id);
                if($comment->loaded()) {
                    // pass
                } else {
                    Message::instance()->set('Invalid comment.');
                    $this->request->redirect('admin/comments');
                }
            } else {
                Message::instance()->set('Invalid comment.');
                $this->request->redirect('admin/comments');
            }
            $flag_nm = isset($_POST['flag_nm']) ? $_POST['flag_nm'] : false;
            if($flag_nm) {
                $flag_nm = strtolower($flag_nm);
                switch($flag_nm) {
                    case 'abuse':
                        $flag = Sourcemap::ABUSE;
                        break;
                    case 'hidden':
                        $flag = Sourcemap::HIDDEN;
                        break;
                    default:
                        Message::instance()->set('Invalid flag.');
                        $this->request->redirect('admin/comments');
                        break;
                }
                if(isset($_POST['unflag'])) {
                    $comment->flags = $comment->flags & (~$flag);
                } else {
                    $comment->flags = $comment->flags | $flag;
                }
                $comment->save();
                if($flag_nm == 'hidden') {
                    if(isset($_POST['unflag'])) {
                        Message::instance()->set('Comment unhidden.');
                    } else {
                        Message::instance()->set('Comment hidden.');
                    }
                } else {
                    if(isset($_POST['unflag'])) {
                        Message::instance()->set('Abuse flag removed.');
                    } else {
                        Message::instance()->set('Comment flagged as abusive.');
                    }
                }
            } else {
                Message::instance()->set('No flag specified.');
            }
        }
        $this->request->redirect('admin/comments');
    }
}

<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Albert Parsons 
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Controller_Admin_Announcements extends Controller_Admin {

    public $layout = 'admin';
    public $template = 'admin/announcements/list';

    public function before() {
        parent::before();
        Breadcrumbs::instance()->add('Management', 'admin')
            ->add('Announcements', 'admin/announcements');
    }

    public function action_index() {
        $p = Pagination::factory(array(
            'total_items' => ORM::factory('user_event')
                ->where('scope', '=', Sourcemap_User_Event::EVERYBODY)
                ->count_all()
        ));
        $announcements = ORM::factory('user_event')
                ->where('scope', '=', Sourcemap_User_Event::EVERYBODY)
                ->order_by('timestamp', 'desc')
                ->limit($p->items_per_page)->offset($p->offset)
                ->find_all()->as_array(null, true);
        foreach($announcements as $i => $announcement) {
            $message = '';
            if($data = @json_decode($announcement->data)) {
                if(isset($data->message)) {
                    $message = Sourcemap_Markdown::markdown($data->message);
                }
            }
            $announcement->message = $message;
        }
        $this->template->announcements = $announcements;
        $this->template->page_links = $p->render();
    }

    public function action_announce() {
        if(Request::$method === 'POST') {
            $post = Validate::factory($_POST);
            $post->rule('announcement_message', 'min_length', array(8))
                ->rule('announcement_message', 'max_length', array(256))
                ->rule('announcement_message', 'not_empty')
                ->rule('confirm1', 'not_empty')
                ->rule('confirm2', 'not_empty')
                ->rule('confirm3', 'not_empty');
            if($post->check()) {
                $post = (object)$post->as_array();
                Sourcemap_User_Event::factory(
                    Sourcemap_User_Event::ANNOUNCE, $post->announcement_message
                )->trigger();
                Message::instance()->set('Announced.', Message::SUCCESS);
                $this->request->redirect('admin/announcements');
            } else {
                Message::instance()->set('Try again.');
                $this->request->redirect('admin/announcements');
            }
        } else $this->request->redirect('admin/announcements');
    }

    public function action_delete() {
        if(Request::$method !== 'POST') {
            Message::instance()->set('Bad request.');
            $this->request->redirect('admin/announcements');
        }
        $post = Validate::factory($_POST);
        $post->rule('user_event_id', 'not_empty')
            ->rule('user_event_id', 'is_numeric');
        if($post->check()) {
            $post = (object)$post->as_array();
            $evt = ORM::factory('user_event', $post->user_event_id);
            if($evt && $evt->loaded()) {
                $evt->delete();
                Message::instance()->set('Announcement deleted.', Message::SUCCESS);
                $this->request->redirect('admin/announcements');
            } else {
                Message::instance()->set('Invalid announcement id.');
            }
        } else Message::instance()->set('Bad request.');
        $this->request->redirect('admin/announcements');
    }
}

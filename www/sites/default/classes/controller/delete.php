<?php
class Controller_Delete extends Sourcemap_Controller_Map {
    public $template = 'delete';

    public function action_index($supplychain_id=false) {
        if(!$supplychain_id) $this->request->redirect('home');
        if(!is_numeric($supplychain_id)) {
            $supplychain_id = $this->_match_alias($supplychain_id);
        }
        $supplychain = ORM::factory('supplychain', $supplychain_id);
        if($supplychain->loaded()) {
            $current_user_id = Auth::instance()->logged_in() ? (int)Auth::instance()->get_user()->id : 0;
            $owner_id = (int)$supplychain->user_id;
            if($current_user_id && $supplychain->user_can($current_user_id, Sourcemap::WRITE)) {
                $supplychain = $supplychain->kitchen_sink($supplychain->id);

                // create the form object and add fields
                $form = Sourcemap_Form::factory('delete')
                    ->method('post')->action('delete/'.$supplychain_id)
                    ->add_class('vertical')
                    ->select('confirm_once', 'Are you sure?')
                    ->select('confirm_twice', 'We can\'t undo this. Are you still sure you want to delete this map?')
                    ->select('confirm_thrice', 'Seriously. This is a permanent thing. Are you *sure*?')
                    ->submit('delete', 'Delete');

                $form->field('confirm_once')->option('no', 'No')->option('yes', 'Yes');
                $form->field('confirm_twice')->option('no', 'No')->option('yes', 'Yes');
                $form->field('confirm_thrice')->option('no', 'No')->option('yes', 'Yes');

                if(strtolower(Request::$method) === 'post') {
                    $post = Validate::factory($_POST);
                    $post->rule('confirm_once', 'in_array', array(array('yes')))
                        ->rule('confirm_twice', 'in_array', array(array('yes')))
                        ->rule('confirm_thrice', 'in_array', array(array('yes')));
                    if($post->check()) {
                        try {
                            ORM::factory('supplychain', $supplychain->id)->delete();
                            Message::instance()->set('Map deleted.', Message::SUCCESS);
                            return $this->request->redirect('home');
                        } catch(Exception $e) {
                            $this->request->status = 500;
                            Message::instance()->set('Couldn\'t delete your supplychain. Please contact support.');
                        }
                    } else {
                        Message::instance()->set('You don\'t seem sure.');
                        $form->errors($post->errors('forms/create'));
                    }
                }

                $this->template->supplychain = $supplychain;
                $this->template->form = $form;
            } else {
                Message::instance()->set('You\'re not allowed to edit that map.');
                $this->request->redirect('home');
            }
        } else {
            Message::instance()->set('That map does not exist.');
            $this->request->redirect('home');
        }
    }
}

<?php
class Sourcemap_User_Event_Updatedsc extends Sourcemap_User_Event {

    protected $_tag = 'updatedsc';

    public function __construct($user_id, $scid, $timestamp=null) {
        $this->user_id = $user_id;
        $this->scid = $scid;
        $this->timestamp = $timestamp ? $timestamp : time();
    }

    public function publish_to($scope_id, $scope=self::USER) {
        $recent_evts = ORM::factory('user_event')
            ->where('event', '=', Sourcemap_User_Event::UPDATEDSC)
            ->and_where('scope', '=', $scope)
            ->and_where('scope_id', '=', $scope_id)
            ->and_where('timestamp', '>', time()-3600)
            ->count_all();
        if($recent_evts > 0) return false;
        else return parent::publish_to($scope_id, $scope);
    }

    protected function get_recipients() {
        //todo: friendz...?
        return array($this->user_id);
    }

    protected function get_data() {
        return array(
            'user_id' => $this->user_id,
            'supplychain_id' => $this->scid
        );
    }

    public static function load($data) {
        if($user_id = $data['user_id']) {
            $data['username'] = ORM::factory('user', $data['user_id'])->username;
        }
        if(isset($data['supplychain_id'])) {
            $attr = ORM::factory('supplychain_attribute')
                ->where('supplychain_id', '=', $data['supplychain_id'])
                ->and_where('key', '=', 'title')->find();
            if($attr->loaded()) {
                $data['supplychain_title'] = (string)$attr->value;
            }
        }
        return $data;
    }
}

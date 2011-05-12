<?php
class Model_Category extends ORM {
    public $_table_names_plural = false;

    public function drop_subtree() {
        if(!$this->loaded()) {
            throw new Exception('No category loaded.');
        }
        $catid = $this->id;
        $catr = $this->right;
        $catl = $this->left;
        $this->_db->begin();
        $sql = sprintf(
            'delete from category where "left" >= %d and "right" <= %d',
            $catl, $catr
        );
        if(!$this->_db->query(Database::DELETE, $sql, true)) {
            $this->_db->rollback();
            throw new Exception('Could not drop subtree.');
        }
        $lshift = ($catr - $catl) + 1;
        $sql1 = sprintf('update category set "left" = "left" - %d where "left" > %d', $lshift, $catr);
        $sql2 = sprintf('update category set "right" = "right" - %d where "right" > %d', $lshift, $catr);
        $this->_db->query(Database::UPDATE, $sql1, true);
        $this->_db->query(Database::UPDATE, $sql2, true);
        return $this->_db->commit();
    }

    public function add_child($child, $at_root=false) {
        if(!$at_root && !$this->loaded()) {
            throw new Exception('No category loaded.');
        }
        $v = Validate::factory((array)$child);
        $v->rule('title', 'not_empty')
            ->rule('title', 'max_length', array(32))
            ->rule('name', 'not_empty')
            ->rule('name', 'max_length', array(16))
            ->filter('name', 'strtolower')
            ->rule('description', 'not_empty');
        if($v->check()) {
            $newc = ORM::factory('category')->values($v->as_array());
            if($at_root) {
                $pl = $this->get_max_right();
                $newc->left = $pl + 1;
                $newc->right = $pl + 2;
            } else {
                $pl = $this->left;
                $pr = $this->right;
                $this->_db->begin();
                $sql = sprintf(
                    'update category set "left" = "left" + 2 where "left" > %d',
                    $pl
                );
                $this->_db->query(Database::UPDATE, $sql, true);
                $sql = sprintf(
                    'update category set "right" = "right" + 2 where "right" > %d',
                    $pl
                );
                $this->_db->query(Database::UPDATE, $sql, true);
                $newc->left = $this->left+1;
                $newc->right = $this->left+2;
            }
            $newc->save();
        } else {
            throw new Exception('Missing data.');
        }
        $this->_db->commit();
    }

    public function get_max_right() {
        $rmax = ORM::factory('category')
            ->order_by('right', 'desc')->limit(1)
            ->find()->right;
        return $rmax;
    }
}

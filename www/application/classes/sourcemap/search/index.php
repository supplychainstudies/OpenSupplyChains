<?php
class Sourcemap_Search_Index {

    public static function should_index($scid) {
        $sc = ORM::factory('supplychain', $scid);
        return $sc->loaded() && ($sc->other_perms & Sourcemap::READ);
    }

    public static function update($scid) {
        $sc = ORM::factory('supplychain', $scid);
        if($sc->loaded()) {
            $scidx = ORM::factory('supplychain_search')
                ->where('supplychain_id', '=', $scid)
                ->find();
            if($scidx->loaded()) {
                // pass    
            } else {
                $scidx = ORM::factory('supplychain_search');
                $scidx->supplychain_id = $sc->id;
            }
            $scidx->created = $sc->created;
            $scidx->modified = $sc->modified;
            $scidx->category = $sc->category;
            $scidx->user_id = $sc->user_id;
            $scidx->body = json_encode($sc->kitchen_sink($sc->id));
            $scidx->featured = (boolean)(($sc->flags & Sourcemap::FEATURED) > 0);
            $scidx->favorited = ORM::factory('user_favorite')
                ->where('supplychain_id', '=', $sc->id)->count_all();
            $scidx->comments = ORM::factory('supplychain_comment')
                ->where('supplychain_id', '=', $sc->id)->count_all();
            $scidx->save();
        }
        return true;
    }

    public static function delete($scid) {
        ORM::factory('supplychain_search')->where('supplychain_id', '=', $scid)
            ->delete_all();
        return true;
    }
}

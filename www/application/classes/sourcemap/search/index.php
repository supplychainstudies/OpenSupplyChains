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

class Sourcemap_Search_Index {

    public static function should_index($scid) {
        $sc = ORM::factory('supplychain', $scid);
        return $sc->loaded() && ($sc->other_perms & Sourcemap::READ);
    }

    public static function update($scid) {

        $sc = ORM::factory('supplychain', $scid);
        if($sc->loaded() && $sc->other_perms != 0) {
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
            $scidx->user_featured = $sc->user_featured;
            $rawsc = $sc->kitchen_sink($sc->id);
            $body = "";
            foreach($rawsc->attributes as $k => $v) $body .= "$k $v\n";
            foreach($rawsc->stops as $i => $st) {
                foreach($st->attributes as $k => $v) $body .= "$k $v\n";
            }
            foreach($rawsc->hops as $i => $h) {
                foreach($h->attributes as $k => $v) $body .= "$k $v\n";
            }
            $scidx->body = $body;
            $scidx->featured = (boolean)(($sc->flags & Sourcemap::FEATURED) > 0);
            $scidx->favorited = ORM::factory('user_favorite')
                ->where('supplychain_id', '=', $sc->id)->count_all();
            $scidx->comments = ORM::factory('supplychain_comment')
                ->where('supplychain_id', '=', $sc->id)->count_all();
			$scidx->stops = ORM::factory('stop')
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

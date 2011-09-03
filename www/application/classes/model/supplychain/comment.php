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

class Model_Supplychain_Comment extends ORM {

    // TODO: comment flags, e.g. 'suspended'/'deleted', 'flagged for removal'?

    public $_belongs_to = array(
        'supplychain' => array(
            'model' => 'supplychain',
            'foreign_key' => 'supplychain_id',
            'far_key' => 'id'
        ),
        'user' => array(
            'model' => 'user'
        )
    );
}

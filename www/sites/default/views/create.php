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
?>

<div id="page-title">
    <div class="container">
        <h1>Create a new map</h1>
    </div>
</div>

<div class="container form-page">
    <div class="copy-section">
    	<p>Tell the story behind a product or a service: map the locations of suppliers, add descriptions, photos and videos, calculate the carbon footprint, and embed or link to the map to share it with the world!</p>
		<div style="margin-top: 15px; margin-bottom: 15px">
            <h3 class="blue" style="clear: right; float: left;">Upload a Spreadsheet</h3>
            &nbsp;<a href="/info/instructions/#spreadsheet?w=600" target="_blank" class="modal tooltip"></a>
            <div class="clear"></div>
            <div style="clear: both; float:left;">
                <input type="file" name="file" style="visibility: hidden; width: 0px; height: 0px;" /> <input type="button" name="file_front" value="Choose a File..." class="button alternate" style="clear: none; float: left; margin-right: 10px; width: 150px; height: 30px;" /> 	
                <?php if(isset($user_supplychains) && $user_supplychains): ?>
                    <select name="replace_into" style="margin-top: 10px; width: 330px; height: 30px; padding: 0 30px 0 10px; clear: none; float: left">
                        <option value="0">Create a new map</option>
                        <?php foreach($user_supplychains as $sc): ?>
                        <option value="<?= $sc->id ?>"><?php
                            echo "Replace \"";
                            echo isset($sc->attributes->title) ? HTML::chars($sc->attributes->title) : $sc->id;
                            echo "\" created ";
                            echo date("F j, Y", $sc->created);
                        ?></option>
                    <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
		</div>
    </div>
    <div class="box-section">
    <div class="sourcemap-form vertical"> 
        <fieldset>
            <form action="/create" method="post" accept-charset="utf-8" enctype="multipart/form-data">        
                <label for="title">Title</label>
                <input type="text" name="title" maxlength="10000" class="textbox" placeholder="Admin mode: Maximum 10000 characters." />            

                <label for="description">Description</label>
                <textarea id="form-description" name="description" cols="50" rows="10" maxlength="10000" class="preview" placeholder="Admin mode: Maximum 10000 characters."></textarea>

                <label for="tags">Tags</label>
                <input type="text" name="tags" class="textbox" placeholder="Separated by spaces." />            

                <label for="category">Category</label>
                <select name="category">

                <?php foreach ($categories as $cat){
                    print "<option value=\"" . $cat->id . "\">" . $cat->name . "</option>";
                } ?>
                </select>        

                <label for="publish">Public</label>
                <input type="checkbox" name="publish" class="textbox" checked="checked" />        

                <div class="clear"></div>
                <div class="submit-status hidden"></div>
                <input type="submit" name="create" value="Create" class="button form-button" />

                <label for="_form_id"></label>
                <input type="hidden" name="_form_id" value="create" class=" textbox" />
            </form>
        </fieldset>
    </div>
    </div>
    <div class="clear"></div>
</div>

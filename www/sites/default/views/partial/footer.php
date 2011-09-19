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

<div class="clear"></div>
<div class="container">
    <ul>
    	<li><a href="info">About</a></li>                
    	<li><a href="http://blog.sourcemap.com/help-with-sourcemap/">Help and FAQ</a></li> 
    	<li><a href="info/api">API and Code</a></li> 

    	<li><a href="http://blog.sourcemap.com/contact/">Get In Touch</a></li>                   
    	<li><a href="http://blog.sourcemap.com">Blog</a></li> 
    	<li><a href="http://blog.sourcemap.com/category/press/">Press</a></li> 

    	<li><a href="info/privacy">Privacy Policy</a></li>
    	<li><a href="info/terms">Terms of Service</a></li>
    </ul>   
    <p>&copy; 2011 Sourcemap Inc. 
    <?
        $subdomain = str_replace(".sourcemap.com", "", $_SERVER["HTTP_HOST"]);
        $commit_hash = exec('git log -1 --pretty=format:%h --abbrev=10');
        $commit_date = exec('git log -1 --pretty=format:%cd');
        $codebase_url = "https://sourcemap.codebasehq.com/projects/sourcemap/repositories/sourcemap/commit/";
    ?>
    <?if( $subdomain != "www" ) {?>
    <a href="<?=$codebase_url.$commit_hash?>"> <?=$commit_date?> </a>
    <?}?>
    </p>
</div>
<div class="clear"></div>

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
    <?php
        // version information
        
        // don't print version information to production
        if( getenv('SOURCEMAP_ENV') != "production" ) {
            $codebase_url = "https://sourcemap.codebasehq.com/projects/sourcemap/repositories/sourcemap/commit/";
            $changelog_url = "https://sourcemap.codebasehq.com/changelog/sourcemap/sourcemap";
            $date = "";
            $age = "";
            $commit = "";

            // if we have a www/version.php file, use it
            if (file_exists('version.php')){
                include_once('version.php');
                $commit = explode(" ", $commit);
                $date = str_replace( "Date: ", "", $date );
                $age = Kohana_date::span(strtotime($date), time(), 'days,hours,minutes,seconds');
                echo "| ";
                echo "Commit ";
                echo "<a href=\"". $codebase_url . $commit[1] . "\">" . substr($commit[1], 0, 5) . "</a> ";
                echo "| ";
                echo "Released ";
                echo $age['days'] . " days, " . $age['hours'] . " hours, " . $age['minutes'] . " minutes,";
                echo " and " . $age['seconds'] . " seconds ago. ";
                echo "| ";
                echo "<a href=\"". $changelog_url . "\">Changelog</a>";
            }
            
            // otherwise, do nothing 
            else{
            }
        }
    ?>
    </p>
</div>
<div class="clear"></div>

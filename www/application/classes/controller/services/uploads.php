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

class Controller_Services_Uploads extends Sourcemap_Controller_Service {
	
	public function action_get() {    
		$s3 = new S3(Kohana::config('apis')->awsAccessKey, Kohana::config('apis')->awsSecretKey);
        if(isset($_GET['bucket']) && isset($_GET['filename'])) {
            try{ // error: filename(account) not exist
			    $img = $s3->getObject($_GET['bucket'], baseName($_GET['filename']));
            } catch (Exception $e) {
                if($_GET['bucket']=='accountpics')
                    return $this->request->redirect('assets/images/default-user.png'); 
                else // bucket : banner
                    return $this->_bad_request("No such banner");
            }
			$this->_format = "png";
    	    $this->response = $img->body;

        } else {
            return $this->_bad_request("Bucket and filename required.");
        }
    }

    public function action_post() {
	    $current_user = $this->get_current_user();
        if(!$current_user) {
            return $this->_forbidden('You must be signed in to upload images.');
        }
		$s3 = new S3(Kohana::config('apis')->awsAccessKey, Kohana::config('apis')->awsSecretKey);
		$posted = $this->request->posted_data;
		if(isset($posted->bucket) && isset($posted->filename)) {
            if($current_user->username!=$posted->filename){
                return $this->_bad_request("Wrong filename.");    
            }
			$s3->putObjectFile($posted->file->tmp_name, $posted->bucket, $posted->filename, S3::ACL_PUBLIC_READ);

            // if 
            if($_POST['bucket']=='bannerpics'){
                $current_user->banner_url = "/services/uploads?bucket=bannerpics&filename=".$current_user->username;
            }                
            return $this->request->redirect('home'); 
			/*
              $this->response = (object)array(
	            'uploaded' => $posted->filename
	        );
             */
		}
		else {
			return $this->_bad_request("Bucket and filename required.");
		}
     
	}

}

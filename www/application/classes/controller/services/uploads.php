p
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
            try{ 
			    $img = $s3->getObject($_GET['bucket'], baseName($_GET['filename']));
            } catch (Exception $e) {
                return $this->_bad_request("No such image.");
            }
            $format = substr(strrchr($_GET['filename'],'.'),1);
			if ($format == 'jpeg'){
                $format = 'jpg';
            }

            $this->_format = $format;
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

        // construct file name based on user's acct
        $filename = $current_user->username . "." . substr(strrchr($posted->file->name,'.'),1);
        if(isset($posted->bucket) && isset($filename)) {
            try{
                // Note that we don't create buckets dynamically-- We'll have to log in to AWS and create the bucket manually
                $s3->putObjectFile($posted->file->tmp_name, $posted->bucket, $filename, S3::ACL_PUBLIC_READ);
                
                if ($posted->bucket == Kohana::config('aws')->avatar_bucket){ 
                    $current_user->avatar_url = $filename;
                    $current_user->save();
                } elseif ($posted->bucket == Kohana::config('aws')->banner_bucket){
                    $current_user->banner_url = $filename;
                    $current_user->save();
                } else { // generic
                    //pass
                }
            } catch (Exception $e){
                return $this->_bad_request("There was a problem saving:" . $e);
            }

            return $this->request->redirect('home'); 
		}
		else {
			return $this->_bad_request("Bucket and filename required.");
		}
     
	}

}

<?php
/* Copyright (C) Sourcemap 2011 
 *
 * Takes a filename and returns a URL to our AWS service.
 *
 * */

//TODO : add clever resizing/cropping
class Sourcemap_Image {

    public static $_image_base = 'services/uploads?bucket=%s&filename=%s'; 
    public static $_url_base = '%s.s3-website-us-east-1.amazonaws.com';
    
    public static function avatar($user, $sz=64) {
        // Return default avatar if unset 
        if(!isset($user->avatar_url)) { 
            return URL::base(true, true)."assets/images/default-user.png"; 
        }
        
        // Check (and correct) old-style avatar format
        /*
        if ($user->avatar_url == ""){
            $user->avatar_url = "";
            //$user->save();
        }
        */

        $timestamp=3600;
        $bucket = sprintf(self::$_url_base, Kohana::config('aws')->avatar_bucket); 
        $s3 = new S3(Kohana::config('apis')->awsAccessKey, Kohana::config('apis')->awsSecretKey);
        return $s3->getAuthenticatedURL($bucket, $user->avatar_url, $timestamp, true, false);
    }
    
    public static function banner($filename, $sz=128, $d=null) {
    	if($d === null) { 
            $d = URL::base(true, true)."assets/images/default-user.png"; 
        }
        
        $timestamp=3600;
        $bucket = sprintf(self::$_url_base, Kohana::config('aws')->banner_bucket); 
        $s3 = new S3(Kohana::config('apis')->awsAccessKey, Kohana::config('apis')->awsSecretKey);
        return $s3->getAuthenticatedURL($bucket, $user->avatar_url, $timestamp, true, false);
    }
    
    public static function generic($filename, $sz=128, $d=null) {
    	if($d === null) { 
            $d = URL::base(true, true)."assets/images/default-user.png"; 
        }
        $timestamp=3600;
        $bucket = sprintf(self::$_url_base, Kohana::config('aws')->generic_bucket); 
        $s3 = new S3(Kohana::config('apis')->awsAccessKey, Kohana::config('apis')->awsSecretKey);
        return $s3->getAuthenticatedURL($bucket, $user->avatar_url, $timestamp, true, false);
    }
}

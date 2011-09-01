<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *  FirePHP Helper Class
 *  Version 0.3
 *  Last changed: 2010-01-25
 */ 
// Copyright (c) 2006-2010, Christoph Dorn
// All rights reserved.
// 
// Redistribution and use in source and binary forms, with or without modification,
// are permitted provided that the following conditions are met:
// 
//     * Redistributions of source code must retain the above copyright notice,
//       this list of conditions and the following disclaimer.
// 
//     * Redistributions in binary form must reproduce the above copyright notice,
//       this list of conditions and the following disclaimer in the documentation
//       and/or other materials provided with the distribution.
// 
//     * Neither the name of Christoph Dorn nor the names of its
//       contributors may be used to endorse or promote products derived from this
//       software without specific prior written permission.
// 
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
// ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
// WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
// DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
// ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
// (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
// LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
// ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
// (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
// SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 

class FirePHP_Fire {
    public function set_config($key, $value=NULL)
    {
	return FirePHP_Profiler::instance()->set_config($key, $value);
    }

    public static function set_enabled( $enabled )
    {
	return FirePHP_Profiler::instance()->set_enabled($enabled);
    }

    public static function dump($Key, $Variable)
    {
	return FirePHP_Profiler::instance()->dump($Key, $Variable);
    }

    public static function error($Object, $Label=null)
    {
	return FirePHP_Profiler::instance()->error($Object, $Label);
    }

    public static function group($Name, $Options=null)
    {
	return FirePHP_Profiler::instance()->group($Name, $Options);
    }

    public static function groupEnd()
    {
	return FirePHP_Profiler::instance()->groupEnd();
    }

    public static function info($Object, $Label=null)
    {
	return FirePHP_Profiler::instance()->info($Object, $Label);
    }

    public static function log($Object, $Label=null)
    {
	return FirePHP_Profiler::instance()->log($Object, $Label);
    }

    public static function table($Label, $Table)
    {
	return FirePHP_Profiler::instance()->table($Label, $Table);
    }

    public static function trace($Label)
    {
	return FirePHP_Profiler::instance()->trace($Label);
    }

    public static function warn($Object, $Label=null)
    {
	return FirePHP_Profiler::instance()->warn($Object, $Label);
    }

    public static function variable_table(array $data, $label='', $count=TRUE)
    {
	return FirePHP_Profiler::instance()->variable_table($data, $label, $count);
    }

    public static function multicolumn_table(array $data, $label='', $count=TRUE, $row_num=TRUE)
    {
	return FirePHP_Profiler::instance()->multicolumn_table($data, $label, $count);
    }


}
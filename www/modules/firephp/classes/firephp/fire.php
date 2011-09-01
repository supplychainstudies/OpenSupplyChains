<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *  FirePHP Helper Class
 *  Version 0.3
 *  Last changed: 2010-01-25
 */
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
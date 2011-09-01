<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Template_FirePHP extends Controller_Template {

  public function before() {
    parent::before();
    Fire::info($this->request, 'Before() called');
  }

  public function after() {
    FirePHP_Profiler::instance()
	    ->group('KO3 FirePHP Profiler Results:')
	    ->superglobals() // New Superglobals method to show them all...
	    ->database()
	    ->benchmark()
	    ->groupEnd();
    parent::after();
  }

}


<?php namespace P3in\Firephp\Facades;
 
use Illuminate\Support\Facades\Facade;
 
class FB extends Facade {
 
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor() { return 'fb'; }
 
}
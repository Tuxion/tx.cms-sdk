<?php namespace components\sdk\models; if(!defined('TX')) die('No direct access.');

class Theme extends \dependencies\BaseModel
{
  
  protected static
    
    $table_name = 'cms_themes',
    
    $validate = array(
      'id' => array('required', 'number'=>'integer', 'gt'=>0),
      'title' => array('required', 'string', 'not_empty'),
      'name' => array('required', 'string', 'not_empty', 'component_name')
    );
  
}

<?php namespace components\sdk; if(!defined('TX')) die('No direct access.');

class Json extends \dependencies\BaseComponent
{
  
  protected
    $default_permission = 2,
    $permissions = array(
    );
  
  protected function update_resolve_translation_missing_files($data, $params)
  {
    
    $affected = 0;
    
    tx('Sql')
      ->table('sdk', 'TranslationMissingFiles')
      ->where('component', "'{$params->{0}}'")
      ->where('language_code', "'{$params->{1}}'")
      ->execute()
      ->each(function($phrase)use(&$affected){
        $phrase->delete();
        $affected++;
      });
    
    return array(
      'success' => true,
      'affected' => $affected
    );
    
  }
  
  protected function update_resolve_translation_missing_phrases($data, $params)
  {
    
    $affected = 0;
    
    tx('Sql')
      ->table('sdk', 'TranslationMissingPhrases')
      ->where('component', "'{$params->{0}}'")
      ->where('language_code', "'{$params->{1}}'")
      ->execute()
      ->each(function($phrase)use(&$affected){
        $phrase->delete();
        $affected++;
      });
    
    return array(
      'success' => true,
      'affected' => $affected
    );
    
  }
  
  protected function create_component($data, $params)
  {
    
    return tx('Sql')
      ->model('sdk', 'Component')
      ->set($data)
      
      ->validate_model(array(
        'force_create' => true,
      ))
      
      ->is('set', function($com)use($data){
        
        $com->merge(array(
          'path' => tx('Component')->helpers('sdk')->call('create_component', $data->having('name', 'title', 'forced'))
        ));
        
      });
    
  }
  
  protected function create_theme($data, $params)
  {
    
    return tx('Sql')
      ->model('sdk', 'Theme')
      ->set($data)
      
      ->validate_model(array(
        'force_create' => true,
      ))
      
      ->is('set', function($com){
        
        $com->merge(array(
          'path' => tx('Component')->helpers('sdk')->call('create_theme', $com->having('name', 'title'))
        ));
        
      });
    
  }
  
  protected function create_template($data, $params)
  {
    
    return tx('Sql')
      ->model('sdk', 'Template')
      ->set($data)
      
      ->validate_model(array(
        'force_create' => true,
      ))
      
      ->is('set', function($com){
        
        $com->merge(array(
          'path' => tx('Component')->helpers('sdk')->call('create_template', $com->having('name', 'title'))
        ));
        
      });
    
  }
  
  protected function update_string_replace($data)
  {

    //TEXT ITEM: description, text
    tx('Sql')
    ->table('text', 'ItemInfo')

    ->where(tx('Sql')->conditions()
      ->add('1', array('description', '|', "'%{$data->original}%'"))
      ->add('2', array('text', '|', "'%{$data->original}%'"))
      ->combine('3', array('1', '2'), 'OR')
      ->utilize('3')
    )

    ->execute()
    ->each(function($row)use($data){

      $row->merge(array(
        'description' => str_replace($data->original->get(), $data->replace_by->get(), $row->description->get()),
        'text' => str_replace($data->original->get(), $data->replace_by->get(), $row->text->get())
      ))->save();

    });

    //LOGO WIZARD: description
    tx('Sql')
    ->table('wizard', 'Answers')
    ->where('description', '|', "'%{$data->original}%'")
    ->execute()
    ->each(function($row)use($data){

      $row->merge(array(
        'description' => str_replace($data->original->get(), $data->replace_by->get(), $row->description->get()),
      ))->save();

    });

  }

}

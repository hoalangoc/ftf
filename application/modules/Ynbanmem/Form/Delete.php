<?php

/**
 * @category   
 * @package    
 * @copyright  
 * @license    
 */
class Ynbanmem_Form_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Member')
      ->setDescription('Are you sure you want to delete this user?');


    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Remove User',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
   // $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    //$button_group = $this->getDisplayGroup('buttons');

    // Set default action
    //$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}
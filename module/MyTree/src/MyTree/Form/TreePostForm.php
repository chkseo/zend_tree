<?php
namespace MyTree\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;



class TreePostForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('treepost');
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new \MyTree\Form\TreePostInputFilter());
 
        $this->add(array(
            'name' => 'id',
            'type'  => 'Hidden',
  

            
              
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'min' => 3,
                'max' => 25,
                'label' => 'Имя ветки',
            ),
        ));
        $this->add(array(
            'name' => 'parent',
            'type' => 'Text',
            'options' => array(
                'min' => 3,
                'max' => 25,
                'label' => 'parent',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'id' => 'submitbutton',
                
            ),
        ));

		$this->setInputFilter(new \MyTree\Form\TreePostInputFilter());
    }

}
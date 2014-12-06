<?php
namespace Latihan\AnseraBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 */
class FormBuah extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder,array $options) {
        $builder->add('namabuah', 'text', 
            array('label' => 'Nama Buah'));
        $builder->add('FirstButton','submit',
            array('label'=>'First Button'));
        $builder->add('SecondButton','submit', array('label'=>'Second Button'));   
        $builder->getForm();
    }

    public function getName() {
        return 'formBuah';
    }

    public function getDefaultOptions(array $options) {
        return $options;
    }
}
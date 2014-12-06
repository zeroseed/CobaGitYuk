<?php
namespace Latihan\AnseraBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 */
class FormKategori extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder,array $options) {
        $builder->add('namaKategori', 'text', 
            array('label' => 'Nama Kategori'));
        $builder->add('noRak', 'text', 
            array('label'=>'No Rak'));
        $builder->add('FirstButton','submit',
            array('label'=>'First Button'));
        $builder->add('SecondButton','submit', array('label'=>'Second Button'));   
        $builder->getForm();
    }

    public function getName() {
        return 'formkategori';
    }

    public function getDefaultOptions(array $options) {
        return $options;
    }
}
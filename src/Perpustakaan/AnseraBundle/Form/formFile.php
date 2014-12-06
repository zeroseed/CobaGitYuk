<?php
namespace Perpustakaan\AnseraBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 */
class FormFile extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('file', 'file', array('label' => 'Upload File'));
        $builder->add('FirstButton', 'submit', array('label'=>'Upload'));
        
        $builder->getForm();
    }

    public function getName() {
        return 'formFile';
    }

    public function getDefaultOptions(array $options) {
        return $options;
    }
}
<?php
namespace Latihan\AnseraBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 */
class FormArtist extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('nama', 'text', array('label' => 'Nama'));        
        $builder->add('pekerjaan', 'text', array('label' => 'Pekerjaan'));
        $builder->add('Save', 'submit');

        $builder->getForm();
    }

    public function getName() {
        return 'formArtist';
    }

    public function getDefaultOptions(array $options) {
        return $options;
    }
}
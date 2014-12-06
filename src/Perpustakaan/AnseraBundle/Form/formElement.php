<?php
namespace Perpustakaan\AnseraBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Perpustakaan\AnseraBundle\Entity\Kategori;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 */
class FormElement extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('contohInteger', 'email', array('label' => 'Contoh Integer'));
     
        /*
        $builder->add('contohText', 'text', array('label' => 'Contoh text'));        
        $builder->add('contohTextarea', 'textarea', array('label' => 'Contoh textarea'));
        $builder->add('contohEmail', 'email', array('label' => 'Contoh Email'));
        $builder->add('contohInteger', 'integer', array('label' => 'Contoh Integer'));
        $builder->add('contohPassword', 'password', array('label' => 'Contoh Password'));
        $builder->add('contohURL', 'url', array('label' => 'Contoh URL'));
        $builder->add('contohHidden', 'hidden', array('label' => 'Hidden'));
        $builder->add('contohCheckboxJakarta', 'checkbox', array('label' => 'Contoh Checkbox Jakarta','value'=>'Jakarta'));
        $builder->add('contohCheckboxCirebon', 'checkbox', array('label' => 'Contoh Checkbox Cirebon','value'=>'Cirebon'));
        $builder->add('contohRadio', 'radio', array('label' => 'Radio ','value'=>'PHP'));
        $builder->add('contohRadio2', 'radio', array('label' => 'Radio2','value'=>'MySQL'));
        $builder->add('contohEntity', 'entity', array('label' => 'contoh entity','class'=>'PerpustakaanAnseraBundle:Kategori'));
        $builder->add('contohCountry', 'country', array('label' => 'Country'));
        $builder->add('contohLanguage', 'language', array('label' => 'Language'));
        $builder->add('contohTimezone', 'timezone', array('label' => 'Timezone'));
        $builder->add('contohCurrency', 'currency', array('label' => 'currency'));
        $builder->add('date', 'date', array('label' => 'date'));
 $builder->add('time', 'time', array('label' => 'time'));
*//*
 $builder->add('BUTTON', 'button', array(
    'attr' => array('class' => 'save'),
));
 $builder->add('SUBMIT', 'submit', array(
    'attr' => array('class' => 'save'),
));
 $builder->add('RESET', 'reset', array(
    'attr' => array('class' => 'save'),
));*/

// $builder->add('File', 'file', array('label' => 'File'));


        $builder->add('Save', 'submit');

        $builder->getForm();
    }

    public function getName() {
        return 'formelement';
    }

    public function getDefaultOptions(array $options) {
        return $options;
    }
}
<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpMyAdminBundle\Form;

use Doctrine\DBAL\Schema\Column;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to create and manipulate blog comments. Although in this
 * case the form is trivial and we could build it inside the controller, a good
 * practice is to always define your forms as classes.
 * See http://symfony.com/doc/current/book/forms.html#creating-form-classes
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class TableRawType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('columns');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var Column[] $columns
         */
        $columns = isset($options['columns']) && is_array($options['columns'])
            ? $options['columns']
            : array();

        foreach ($columns as $column) {
            if (!$column->getAutoincrement()) {
                $builder->add($column->getName(), null, array('required' => $column->getNotnull()));
            }
        }

        $builder->add('submit', SubmitType::class, array(
            'label' => 'submit',
            'attr'  => array('class' => 'btn btn-default')
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        // Best Practice: use 'app_' as the prefix of your custom form types names
        // see http://symfony.com/doc/current/best_practices/forms.html#custom-form-field-types
        return 'insert_raw';
    }
}

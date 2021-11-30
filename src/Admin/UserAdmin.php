<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserAdmin extends AbstractAdmin
{
    private $translator;

    public function __construct(string $code, string $class, string $baseControllerName, TranslatorInterface $translator)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->translator = $translator;
    }

    private $fieldsArray = ['id', 'uuid', 'email', 'username', 'roles', 'height', 'weight', 'birthDate', 'createdAt'];

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->translator->trans('user.content', [], 'translations'))
            ->add('email', EmailType::class)
            ->add('createdAt', DateTimeType::class, [
                'input' => 'datetime_immutable'
            ])->end();

        $form->with($this->translator->trans('user.meta', [], 'translations'))
//            ->add('rates', CollectionType::class,
//                array('by_reference' => false),
//                array('edit' => 'inline',
//                    'inline' => 'table'
//                ))
            ->add('rates', CollectionType::class, [
                'type_options' => [
                    // Prevents the "Delete" option from being displayed
                    'delete' => false,
                    'delete_options' => [
                        // You may otherwise choose to put the field but hide it
                        'type' => HiddenType::class,
                        // In that case, you need to fill in the options as well
                        'type_options' => [
                            'mapped' => false,
                            'required' => false,
                        ]
                    ]
                ]
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        foreach ($this->fieldsArray as $field) {
            $datagrid->add($field);
        }
//        $datagrid->add('category', null, [
//            'field_type' => EntityType::class,
//            'field_options' => [
//                'class' => Category::class,
//                'choice_label' => 'name',
//            ],
//        ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        foreach ($this->fieldsArray as $field) {
            $list->addIdentifier($field);
        }

        $list->add('rates',CollectionType::class,[
            'associated_property'=>'value'
        ]);


    }

    protected function configureShowFields(ShowMapper $show): void
    {
        foreach ($this->fieldsArray as $field) {
            $show->add($field);
        }
    }

    /**
     * @param object $object
     * @return string
     */
    public function toString(object $object): string
    {
        return $object instanceof User
            ? $object->getEmail()
            : 'User';
    }
}
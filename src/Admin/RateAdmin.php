<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Rate;
use App\Entity\SubCategory;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

class RateAdmin extends AbstractAdmin
{
    private $translator;

    public function __construct(string $code, string $class, string $baseControllerName, TranslatorInterface $translator)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->translator = $translator;
    }

    private $fieldsArray = ['id', 'uuid', 'value', 'createdAt'];

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->translator->trans('user.content', [], 'translations'), ['class' => 'col-md-9',])
            ->add('value', NumberType::class)
            ->add('product', ModelType::class, [
                'class' => Product::class,
                'property' => 'name',
            ])
            ->add('createdAt', DateTimeType::class, [
                'input' => 'datetime_immutable'
            ])
            ->end();

//        $form->with($this->translator->trans('user.meta', [], 'translations'), ['class' => 'col-md-3'])
//            ->add('user', ModelType::class, [
//                'class' => User::class,
//                'property' => 'email',
//            ])
//            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        foreach ($this->fieldsArray as $field) {
            $datagrid->add($field);
        }
        $datagrid->add('category', null, [
            'field_type' => EntityType::class,
            'field_options' => [
                'class' => Category::class,
                'choice_label' => 'name',
            ],
        ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        foreach ($this->fieldsArray as $field) {
            $list->addIdentifier($field);
        }
            $list->add('user.email');

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
        return $object instanceof Rate
            ? 'Rate (' . $object->getId() . ')'
            : 'Rate';
    }
}
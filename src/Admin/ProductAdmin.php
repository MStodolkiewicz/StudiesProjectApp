<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductAdmin extends AbstractAdmin
{
    private $translator;

    public function __construct(string $code, string $class, string $baseControllerName, TranslatorInterface $translator)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->translator = $translator;
    }

    private $fieldsArray = ['id', 'uuid', 'name', 'barCodeNumbers', 'brand', 'isVerified','isDeleted', 'proteins', 'carbohydrates', 'fat', 'kcal', 'createdAt'];

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->translator->trans('user.content', [], 'translations'), ['class' => 'col-md-9'])
            ->add('name', TextType::class)
            ->add('barCodeNumbers', TextType::class)
            ->add('brand', TextType::class)
            ->add('isVerified', BooleanType::class)
            ->add('deletedAt', DateTimeType::class)
            ->add('proteins', NumberType::class)
            ->add('carbohydrates', NumberType::class)
            ->add('fat', NumberType::class)
            ->add('kcal', NumberType::class)
            ->add('createdAt', DateTimeType::class, [
                'input' => 'datetime_immutable'
            ])
            ->end();

        $form->with($this->translator->trans('user.meta', [], 'translations'), ['class' => 'col-md-3'])
            ->add('user', ModelType::class, [
                'class' => User::class,
                'property' => 'email',
            ])
            ->add('category', ModelType::class, [
                'class' => Category::class,
                'property' => 'name',
            ])
            ->add('subcategory', ModelType::class, [
                'class' => SubCategory::class,
                'property' => 'name',
            ])
            ->end();


    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name')
            ->add('barCodeNumbers')
            ->add('brand')
            ->add('isVerified')
            ->add('isDeleted')
            ->add('proteins')
            ->add('carbohydrates')
            ->add('fat')
            ->add('kcal');

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
        $list->addIdentifier('name', TextType::class)
            ->addIdentifier('barCodeNumbers', TextType::class)
            ->addIdentifier('brand', TextType::class)
            ->add('user.email')
            ->add('category.name')
            ->add('subcategory.name')
            ->addIdentifier('isVerified', BooleanType::class)
            ->addIdentifier('isDeleted', BooleanType::class)
            ->addIdentifier('proteins', NumberType::class)
            ->addIdentifier('carbohydrates', NumberType::class)
            ->addIdentifier('fat', NumberType::class)
            ->addIdentifier('kcal', NumberType::class);

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
        return $object instanceof Product
            ? $object->getName()
            : 'Product';
    }
}
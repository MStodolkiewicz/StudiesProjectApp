<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Intake;
use App\Entity\Product;
use App\Entity\SubCategory;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Filter\NumberType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

class IntakeAdmin extends AbstractAdmin
{
    private $translator;

    public function __construct(string $code, string $class, string $baseControllerName, TranslatorInterface $translator)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->translator = $translator;
    }

    private $fieldsArray = ['id', 'uuid','product.name','amountInGrams','mealType','user.username', 'createdAt'];

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with($this->translator->trans('user.content', [], 'translations'))
            ->add('product', ModelType::class, [
                'class' => Product::class,
                'property' => 'name',
            ])
            ->add('amountInGrams',TextType::class)
            ->add('mealType',TextType::class)
            ->add('user', ModelType::class, [
                'class' => User::class,
                'property' => 'email',
            ])
            ->add('createdAt', DateTimeType::class, [
                'input' => 'datetime_immutable'
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        foreach ($this->fieldsArray as $field) {
            $datagrid->add($field);
        }

    }

    protected function configureListFields(ListMapper $list): void
    {
        foreach ($this->fieldsArray as $field) {
            $list->addIdentifier($field);
        }

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
        return $object instanceof Intake
            ? 'Intake (' . $object->getId() .')'
            : 'Intake';
    }
}
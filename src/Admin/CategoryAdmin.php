<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\SubCategory;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class CategoryAdmin extends AbstractAdmin
{
    private $fieldsArray = ['id', 'uuid', 'name', 'createdAt'];

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('name', TextType::class);
        $form->add('createdAt', DateTimeType::class, [
            'input' => 'datetime_immutable'
        ]);
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
        return $object instanceof Category
            ? $object->getName()
            : 'Category';
    }
}
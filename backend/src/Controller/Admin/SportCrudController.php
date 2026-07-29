<?php

namespace App\Controller\Admin;

use App\Entity\Sport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class SportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sport::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Sport')
            ->setEntityLabelInPlural('Sports')
            ->setDefaultSort([
                'sortOrder' => 'ASC',
                'name' => 'ASC',
            ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield TextField::new('slug');
        yield TextField::new('icon');
        yield BooleanField::new('isActive', 'Active');
        yield IntegerField::new('sortOrder', 'Sort order');
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Bookmaker;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class BookmakerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bookmaker::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Bookmaker')
            ->setEntityLabelInPlural('Bookmakers')
            ->setDefaultSort([
                'sortOrder' => 'ASC',
                'name' => 'ASC',
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield TextField::new('slug');
        yield TextField::new('emoji');
        yield IntegerField::new('sortOrder', 'Sort order');
        yield BooleanField::new('isActive', 'Active');
        yield BooleanField::new('isLegal', 'Legal');
        yield NumberField::new('defaultTaxPercent', 'Default tax %')
            ->setNumDecimals(2)
            ->setStoredAsString();
        yield TextField::new('website')->setRequired(false);
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
    }
}

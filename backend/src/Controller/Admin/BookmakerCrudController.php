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
use function Symfony\Component\Translation\t;

final class BookmakerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bookmaker::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('admin.bookmaker.singular'))
            ->setEntityLabelInPlural(t('admin.bookmaker.plural'))
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
        yield IntegerField::new('sortOrder', t('admin.field.sort_order'));
        yield BooleanField::new('isActive', t('admin.field.is_active'));
        yield BooleanField::new('isLegal', t('admin.field.is_legal'));
        yield NumberField::new('defaultTaxPercent', t('admin.field.default_tax_percent'))
            ->setNumDecimals(2)
            ->setStoredAsString();
        yield TextField::new('website', t('admin.field.website'))->setRequired(false);
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
    }
}

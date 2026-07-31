<?php

namespace App\Controller\Admin;

use App\Entity\BetSlip;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use function Symfony\Component\Translation\t;

final class BetSlipCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BetSlip::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('admin.bet_slip.singular'))
            ->setEntityLabelInPlural(t('admin.bet_slip.plural'))
            ->setDefaultSort([
                'placedAt' => 'DESC',
                'id' => 'DESC',
            ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('owner');
        yield AssociationField::new('bookmaker');
        yield TextField::new('name')->setRequired(false);
        yield MoneyField::new('stake')->setCurrency('PLN')->setStoredAsCents(false);
        yield NumberField::new('totalOdds')->setNumDecimals(3)->setStoredAsString();
        yield TextField::new('status');
        yield DateTimeField::new('placedAt');
        yield DateTimeField::new('settledAt')->setRequired(false);
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
    }
}

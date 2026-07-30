<?php

namespace App\Controller\Admin;

use App\Entity\Competition;
use App\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class CompetitionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Competition::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Competition')
            ->setEntityLabelInPlural('Competitions')
            ->setDefaultSort([
                'name' => 'ASC',
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield AssociationField::new('sport');
        yield AssociationField::new('region')
            ->setRequired(false)
            ->setFormTypeOption('placeholder', 'No region')
            ->setFormTypeOption('choice_label', 'name')
            ->setQueryBuilder(static fn ($queryBuilder) => $queryBuilder
                ->orderBy('entity.sortOrder', 'ASC')
                ->addOrderBy('entity.name', 'ASC'))
            ->formatValue(static function ($value): string {
                if (!$value instanceof Region) {
                    return 'No region';
                }

                $emoji = $value->getEmoji();

                return null === $emoji ? $value->getName() : sprintf('%s %s', $emoji, $value->getName());
            });
        yield BooleanField::new('isActive', 'Active');
        yield AssociationField::new('owner')->hideOnForm();
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Competition) {
            $entityInstance->setOwner(null);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}

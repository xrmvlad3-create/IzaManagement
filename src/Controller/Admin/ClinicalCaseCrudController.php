<?php

namespace App\Controller\Admin;

use App\Entity\ClinicalCase;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClinicalCaseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ClinicalCase::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setRequired(true),
            TextEditorField::new('description'),
            AssociationField::new('speciality', 'Specialty'),
        ];
    }
}

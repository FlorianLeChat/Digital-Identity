<?php

namespace App\Controller\Admin;

use App\Entity\Absence;
use App\Entity\user;
use App\Entity\cours;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class AbsenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Absence::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
           // IdField::new('id'),
            ArrayField::new('user'),
            ArrayField::new('cours'),
            BooleanField::new('justification_statut'),
        ];
    }
    
}

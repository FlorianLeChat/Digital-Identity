<?php

namespace App\Controller\Admin;

use App\Entity\Matiere;
use App\Entity\Formation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class MatiereCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Matiere::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            //IdField::new('id'),
            TextField::new('nome_matiere'),
            AssociationField::new('user'),
            AssociationField::new('formation'),
            //TextEditorField::new('description'),
        ];
    }
    
}

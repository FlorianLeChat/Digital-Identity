<?php

namespace App\Controller\Admin;

use App\Entity\Presence;
use App\Entity\Cours;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;


class PresenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Presence::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
           //IdField::new('id'),
            TextField::new('token'),
            ArrayField::new('user'),
            ArrayField::new('cours'),
        ];
    }
    
}

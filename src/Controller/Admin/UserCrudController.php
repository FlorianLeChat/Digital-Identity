<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Formation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            //IdField::new('id'),
            EmailField::new('email'),
            ArrayField::new('roles'),
            TextField::new('password'),
            TextField::new('firsname'),
            TextField::new('lastname'),
            IntegerField::new('code_badge'),
            IntegerField::new('td'),
            IntegerField::new('tp'),
            IntegerField::new('year'),
            AssociationField::new('formation'),
        ];
    }
    
}

<?php

namespace App\Admin\Controller;

use App\Entity\GooglePlace;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GooglePlaceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GooglePlace::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(),
            TextField::new('placeId'),
            TextField::new('url')->hideOnIndex(),
            FormField::addPanel('Json fields'),
            CodeEditorField::new('textsearchApiResultAdmin')->hideOnIndex()->setDisabled()->setLabel('TextsearchApiResult'),
            CodeEditorField::new('placeDetailsApiResultAdmin')->hideOnIndex()->setDisabled()->setLabel('PlaceDetailsApiResultAdmin'),
        ];
    }
}

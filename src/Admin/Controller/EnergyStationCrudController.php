<?php

namespace App\Admin\Controller;

use App\Admin\Filter\EnergyStationStatusFilter;
use App\Entity\EnergyStation;
use App\Lists\EnergyStationStatusReference;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EnergyStationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EnergyStation::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('admin');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('energyStationId')
            ->add('pop')
            ->add('name')
            ->add('googlePlace')
            ->add(EnergyStationStatusFilter::new('status'))
            ->add(TextFilter::new('address'))
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'))
            ->add(DateTimeFilter::new('closedAt'));
    }

    public function configureFields(string $pageName): iterable
    {
        $energyStationStatus = [
            EnergyStationStatusReference::UPDATED_TO_ADDRESS_FORMATED => EnergyStationStatusReference::UPDATED_TO_ADDRESS_FORMATED,
            EnergyStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH => EnergyStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH,
            EnergyStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS => EnergyStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS,

            EnergyStationStatusReference::WAITING_VALIDATION => EnergyStationStatusReference::WAITING_VALIDATION,

            EnergyStationStatusReference::VALIDATION_REJECTED => EnergyStationStatusReference::VALIDATION_REJECTED,

            EnergyStationStatusReference::CLOSED => EnergyStationStatusReference::CLOSED,
            EnergyStationStatusReference::OPEN => EnergyStationStatusReference::OPEN,
        ];

        return [
            FormField::addPanel('Energy Station Details'),
            TextField::new('energyStationId')
                ->setDisabled()
                ->setColumns('col-sm-12 col-lg-12 col-xxl-12'),
            TextField::new('hash')
                ->hideOnIndex()
                ->setDisabled()
                ->setColumns('col-sm-12 col-lg-12 col-xxl-12'),
            TextField::new('type')
                ->setDisabled()
                ->setColumns('col-sm-12 col-lg-12 col-xxl-12'),
            TextField::new('name')
                ->setColumns('col-sm-12 col-lg-12 col-xxl-12'),
            TextField::new('pop')
                ->hideOnIndex()
                ->setColumns('col-sm-12 col-lg-12 col-xxl-12'),
            TextField::new('uuid')
                ->hideOnIndex()
                ->setColumns('col-sm-12 col-lg-12 col-xxl-12')
                ->setDisabled(),

            FormField::addPanel('Status'),
            TextField::new('status')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            ChoiceField::new('statusAdmin')
                ->setLabel('Change status')
                ->hideOnIndex()
                ->hideOnDetail()
                ->autocomplete()
                ->renderAsNativeWidget()
                ->setChoices($energyStationStatus)
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            ArrayField::new('statusesAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Status History')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Prices'),
            CodeEditorField::new('lastEnergyPricesAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('LastEnergyPrices'),
            CodeEditorField::new('previousEnergyPricesAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('PreviousEnergyPrices'),

            FormField::addPanel('EvInformation'),
            IdField::new('evInformation.id')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Id')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            IdField::new('evInformation.uuid')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Uuid')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('evInformation.numberRechargePoint')
                ->hideOnIndex()
                ->setLabel('Nb Pdc')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('evInformation.maximumPower')
                ->hideOnIndex()
                ->setLabel('Maximum Power')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            DateField::new('evInformation.dateCreated')
                ->hideOnIndex()
                ->setLabel('Date Created')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('GooglePlace'),
            IdField::new('googlePlace.id')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Id')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            IdField::new('googlePlace.uuid')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Uuid')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            FormField::addRow(),
            TextField::new('googlePlace.placeId')
                ->setLabel('PlaceId')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.googleId')
                ->hideOnIndex()
                ->setLabel('GoogleId')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.website')
                ->hideOnIndex()
                ->setLabel('Website')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.phoneNumber')
                ->hideOnIndex()
                ->setLabel('PhoneNumber')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.compoundCode')
                ->hideOnIndex()
                ->setLabel('CompoundCode')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.globalCode')
                ->hideOnIndex()
                ->setLabel('GlobalCode')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.googleRating')
                ->hideOnIndex()
                ->setLabel('GoogleRating')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.rating')
                ->hideOnIndex()
                ->setLabel('Rating')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.userRatingsTotal')
                ->hideOnIndex()
                ->setLabel('UserRatingsTotal')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.icon')
                ->hideOnIndex()
                ->setLabel('Icon')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.reference')
                ->hideOnIndex()
                ->setLabel('Reference')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.wheelchairAccessibleEntrance')
                ->hideOnIndex()
                ->setLabel('WheelchairAccessibleEntrance')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.businessStatus')
                ->hideOnIndex()
                ->setLabel('BusinessStatus')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            ArrayField::new('googlePlace.openingHours')
                ->hideOnIndex()
                ->setLabel('OpeningHours')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Address'),
            IdField::new('address.id')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Id')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            IdField::new('address.uuid')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Uuid')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            FormField::addRow(),
            TextField::new('address.vicinity')
                ->hideOnIndex()
                ->setLabel('Vicinity')
                ->setColumns('col-sm-12'),
            TextField::new('address.street')
                ->hideOnIndex()
                ->setLabel('Street')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.number')
                ->hideOnIndex()
                ->setLabel('Street Number')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.city')
                ->hideOnIndex()
                ->setLabel('City')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.region')
                ->hideOnIndex()
                ->setLabel('Region')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.postalCode')
                ->hideOnIndex()
                ->setLabel('PostalCode')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.country')
                ->hideOnIndex()
                ->setLabel('Country')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.longitude')
                ->hideOnIndex()
                ->setLabel('Longitude')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.latitude')
                ->hideOnIndex()
                ->setLabel('Latitude')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            DateTimeField::new('address.createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('CreatedAt')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            DateTimeField::new('address.updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('UpdatedAt')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Energy Station Brand'),
            IdField::new('energyStationBrand.id')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Id')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('energyStationBrand.uuid')
                ->setDisabled()
                ->hideOnIndex()
                ->setLabel('Uuid')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            FormField::addRow(),
            TextField::new('energyStationBrand.name')
                ->hideOnIndex()
                ->setLabel('Name')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('energyStationBrand.reference')
                ->hideOnIndex()
                ->setLabel('Reference')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addRow(),
            TextField::new('energyStationBrand.image.name', 'Name')
                ->setDisabled()
                ->setLabel('Image Name')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('energyStationBrand.image.originalName', 'originalName')
                ->setDisabled()
                ->setLabel('Image OriginalName')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            NumberField::new('energyStationBrand.image.size', 'Size')
                ->setDisabled()
                ->setLabel('Image Size')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('energyStationBrand.image.mimeType', 'mimeType')
                ->setDisabled()
                ->setLabel('Image MimeType')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            ArrayField::new('energyStationBrand.image.dimensions', 'Dimensions')
                ->setDisabled()
                ->setLabel('Image Dimensions')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),

            FormField::addRow(),
            TextField::new('energyStationBrand.imageLow.name', 'Name')
                ->setDisabled()
                ->setLabel('Image Low Name')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('energyStationBrand.imageLow.originalName', 'originalName')
                ->setDisabled()
                ->setLabel('Image Low OriginalName')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            NumberField::new('energyStationBrand.imageLow.size', 'Size')
                ->setDisabled()
                ->setLabel('Image Low Size')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('energyStationBrand.imageLow.mimeType', 'mimeType')
                ->setDisabled()
                ->setLabel('Image Low MimeType')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            ArrayField::new('energyStationBrand.imageLow.dimensions', 'Dimensions')
                ->setDisabled()
                ->setLabel('Image Low Dimensions')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),

            FormField::addPanel('Energy Station Services'),
            CodeEditorField::new('servicesAdmin')
                ->setDisabled()
                ->hideOnIndex(),

            FormField::addPanel('Energy Station Metadata'),
            DateTimeField::new('createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            DateTimeField::new('updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->setDisabled(),
            DateTimeField::new('closedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Image'),
            TextField::new('imageFile', 'Upload')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            ImageField::new('image.name', 'Image')
                ->setRequired(true)
                ->setBasePath('/images/energy_stations/')
                ->hideOnForm(),
            TextField::new('image.name', 'Name')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('image.originalName', 'originalName')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            NumberField::new('image.size', 'Size')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('image.mimeType', 'mimeType')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            ArrayField::new('image.dimensions', 'Dimensions')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),

            FormField::addPanel('Max Retry'),
            Field::new('maxRetryPositionStack')
                ->setDisabled()
                ->hideOnIndex(),
            Field::new('maxRetryTextSearch')
                ->setDisabled()
                ->hideOnIndex(),
            Field::new('maxRetryPlaceDetails')
                ->setDisabled()
                ->hideOnIndex(),

            FormField::addPanel('Json fields'),
            CodeEditorField::new('elementAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Element'),
            CodeEditorField::new('address.positionStackApiResultAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('positionStackApiResult'),
            CodeEditorField::new('googlePlace.textsearchApiResultAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('TextsearchApiResult'),
            CodeEditorField::new('googlePlace.placeDetailsApiResultAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('PlaceDetailsApiResult'),
        ];
    }
}

<?php

namespace App\Admin\Controller;

use App\Entity\Address;
use App\Entity\Currency;
use App\Entity\EnergyPrice;
use App\Entity\EnergyService;
use App\Entity\EnergyStation;
use App\Entity\EnergyStationBrand;
use App\Entity\EnergyType;
use App\Entity\EvInformation;
use App\Entity\GooglePlace;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureCrud(): Crud
    {
        $crud = Crud::new();

        return $crud
            ->addFormTheme('bundles/EasyAdminBundle/crud/form.html.twig')
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }

    /** @param User $user */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)->setName($user->getEmail());
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToUrl('Api Docs', 'fas fa-map-marker-alt', '/api/docs');
        yield MenuItem::linkToCrud('EnergyStation', 'fas fa-list', EnergyStation::class);
        yield MenuItem::linkToCrud('EnergyStationBrand', 'fas fa-list', EnergyStationBrand::class);
        yield MenuItem::linkToCrud('EnergyPrice', 'fas fa-list', EnergyPrice::class);
        yield MenuItem::linkToCrud('EnergyType', 'fas fa-list', EnergyType::class);
        yield MenuItem::linkToCrud('Currency', 'fas fa-list', Currency::class);
        yield MenuItem::linkToCrud('EvInformation', 'fas fa-list', EvInformation::class);
        yield MenuItem::linkToCrud('GooglePlace', 'fas fa-list', GooglePlace::class);
        yield MenuItem::linkToCrud('Address', 'fas fa-list', Address::class);
        yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
        yield MenuItem::linktoRoute('Validation', 'fa fa-list', 'app_admin_validation');
    }
}

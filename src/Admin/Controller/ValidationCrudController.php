<?php

namespace App\Admin\Controller;

use App\Entity\EnergyStation;
use App\Entity\EntityId\EnergyStationId;
use App\Lists\EnergyStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Repository\EnergyStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ValidationCrudController extends AbstractController
{
    #[Route('/admin/validation', name: 'app_admin_validation')]
    public function index(AdminUrlGenerator $adminUrlGenerator, EnergyStationRepository $energyStationRepository, Request $request): Response
    {
        $energyStationId = $request->query->get('energyStationId') ?? null;

        if (null !== $energyStationId) {
            $energyStation = $energyStationRepository->findOneBy(['energyStationId' => $energyStationId]);
        }

        if (null === $energyStationId) {
            $energyStation = $energyStationRepository->findRandomEnergyStation(EnergyStationStatusReference::WAITING_VALIDATION);
        }

        if (null === $energyStation) {
            $energyStation = $energyStationRepository->findRandomEnergyStation(EnergyStationStatusReference::PLACE_ID_ANOMALY);
        }

        if (null === $energyStation) {
            $energyStation = $energyStationRepository->findRandomEnergyStation(EnergyStationStatusReference::ADDRESS_ERROR_FORMATED);
        }

        if (null === $energyStation) {
            $energyStation = $energyStationRepository->findRandomEnergyStation(EnergyStationStatusReference::VALIDATION_REJECTED);
        }

        $energyStations = $energyStationRepository->getEnergyStationGooglePlaceByPlaceId($energyStation);

        $url = $adminUrlGenerator
            ->setController(EnergyStationCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($energyStation?->getId())
            ->generateUrl();

        return $this->render('Admin/validation.html.twig', [
            'energyStation' => $energyStation,
            'energyStations' => $energyStations,
            'energyStationsCount' => count($energyStations),
            'energyStationUrlEdit' => $url,
        ]);
    }

    #[Route('/admin/validation/validate/{energyStationId}', name: 'app_admin_validation_validate')]
    public function validate(EntityManagerInterface $em, EnergyStation $energyStation, EnergyStationRepository $energyStationRepository): Response
    {
        // if (EnergyStationStatusReference::WAITING_VALIDATION !== $energyStation->getStatus()) {
        //     return $this->redirect('/admin?routeName=app_admin_validation');
        // }

        $energyStation->setStatus(EnergyStationStatusReference::VALIDATED);
        $energyStation->setStatus(EnergyStationStatusReference::OPEN);

        $energyStations = $energyStationRepository->getEnergyStationGooglePlaceByPlaceId($energyStation);
        foreach ($energyStations as $entity) {
            $entity->setStatus(EnergyStationStatusReference::VALIDATION_REJECTED);
            $entity->getGooglePlace()->setPlaceId(NULL);
            $em->persist($entity);
        }

        $em->persist($energyStation);
        $em->flush();

        return $this->redirect('/admin?routeName=app_admin_validation');
    }

    #[Route('/admin/validation/rejected/textsearch/{energyStationId}', name: 'app_admin_validation_rejected_textsearch')]
    public function rejectedToTextSearch(EntityManagerInterface $em, MessageBusInterface $messageBus, EnergyStation $energyStation): Response
    {
        // if (EnergyStationStatusReference::WAITING_VALIDATION !== $energyStation->getStatus()) {
        //     return $this->redirect('/admin?routeName=app_admin_validation');
        // }

        $energyStation->setStatus(EnergyStationStatusReference::VALIDATION_REJECTED);
        $energyStation->getGooglePlace()->setPlaceId(NULL);

        $em->persist($energyStation);
        $em->flush();

        $messageBus->dispatch(
            new CreateGooglePlaceTextsearchMessage(new EnergyStationId($energyStation->getEnergyStationId()))
        );

        return $this->redirect('/admin?routeName=app_admin_validation');
    }

    #[Route('/admin/validation/rejected/placedetails/{energyStationId}', name: 'app_admin_validation_rejected_placedetails')]
    public function rejectedToPlaceDetails(EntityManagerInterface $em, MessageBusInterface $messageBus, EnergyStation $energyStation): Response
    {
        // if (EnergyStationStatusReference::WAITING_VALIDATION !== $energyStation->getStatus()) {
        //     return $this->redirect('/admin?routeName=app_admin_validation');
        // }

        $energyStation->setStatus(EnergyStationStatusReference::VALIDATION_REJECTED);
        $energyStation->getGooglePlace()->setPlaceId(NULL);

        $em->persist($energyStation);
        $em->flush();

        $messageBus->dispatch(
            new CreateGooglePlaceDetailsMessage(new EnergyStationId($energyStation->getEnergyStationId()))
        );

        return $this->redirect('/admin?routeName=app_admin_validation');
    }

    #[Route('/admin/validation/rejected/{energyStationId}', name: 'app_admin_validation_rejected')]
    public function rejected(EntityManagerInterface $em, MessageBusInterface $messageBus, EnergyStation $energyStation): Response
    {
        // if (EnergyStationStatusReference::WAITING_VALIDATION !== $energyStation->getStatus()) {
        //     return $this->redirect('/admin?routeName=app_admin_validation');
        // }

        $energyStation->setStatus(EnergyStationStatusReference::VALIDATION_REJECTED);
        $energyStation->getGooglePlace()->setPlaceId(NULL);

        $em->persist($energyStation);
        $em->flush();

        return $this->redirect('/admin?routeName=app_admin_validation');
    }
}

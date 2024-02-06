<?php

namespace App\ApiResource\Controller;

use App\Repository\AddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetAddressCities extends AbstractController
{
    public static string $operationName = 'get_address_cities';

    public function __construct(
        private AddressRepository $addressRepository
    )
    {
    }

    public function __invoke(Request $request)
    {
        return $this->addressRepository->getCities();
    }
}

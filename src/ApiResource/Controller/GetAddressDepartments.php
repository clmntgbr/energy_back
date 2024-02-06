<?php

namespace App\ApiResource\Controller;

use App\Service\AddressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetAddressDepartments extends AbstractController
{
    public static string $operationName = 'get_addresses_departments';

    public function __construct(
        private AddressService $addressService
    )
    {
    }

    public function __invoke(Request $request)
    {
        return $this->addressService->getAddressDepartments();
    }
}

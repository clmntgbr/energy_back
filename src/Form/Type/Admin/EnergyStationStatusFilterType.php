<?php

namespace App\Form\Type\Admin;

use App\Lists\EnergyStationStatusReference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnergyStationStatusFilterType extends AbstractType
{
    public function __construct()
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => EnergyStationStatusReference::getConstantsList(),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $data = ['EURUSD', 'EURCHF'];

        foreach ($data as $value) {
            $currency = new Currency();
            $currency->setName($value);
            $manager->persist($currency);
        }

        $manager->flush();
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/03/2018
 * Time: 16:00
 */

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Phone;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $client->setName('bilemo');
        $user = new User();
        $user->setUsername('boss');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'boss'));
        $user->setRoles('ROLE_SUPER_ADMIN');
        $user->setClient($client);
        $user->setEmail('boss@gmail.com');
        $client->setLeader($user);

        $manager->persist($client);
        $manager->persist($user);

        for ($i = 1; $i < 61; $i++) {
            $phone = new Phone();
            $phone->setMark('Sungsong');
            $phone->setReference('SG-'.$i);
            $phone->setDescription('The Sungsong sg-'. $i .' is one of the best smartphone of the universe !');
            $phone->setPrice($i * 4 + rand(100, 500));
            $manager->persist($phone);
        }

        for ($e = 1; $e < 61; $e++) {
            $phone = new Phone();
            $phone->setMark('Noukio');
            $phone->setReference('NK-'.$e);
            $phone->setDescription('The Noukio HH-' . $e . ' has all you could never imagine in a smartphone !');
            $phone->setPrice($e * 4 + rand(100, 500));
            $manager->persist($phone);
        }

        $manager->flush();
    }
}

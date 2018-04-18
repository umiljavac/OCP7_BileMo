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

        for ($i = 1; $i < 11; $i++) {
            $phone = new Phone();
            $phone->setMark('Sungsong');
            $phone->setReference('SG-'.$i);
            $phone->setDescription(
                'The Sungsong sg-'. $i .' is one of the best smartphone of the universe ! '
                . 'Vita est illis semper in fuga uxoresque mercenariae conductae ad tempus '
                . 'ex pacto atque, ut sit species matrimonii, dotis nomine futura coniunx '
                . 'hastam et tabernaculum offert marito, post statum diem si id elegerit '
                . 'discessura, et incredibile est quo ardore apud eos in venerem uterque solvitur '
                . 'sexus.Eius populus ab incunabulis primis ad usque pueritiae tempus extremum, '
                . 'quod annis circumcluditur fere trecentis, circummurana pertulit bella, deinde '
                . 'aetatem ingressus adultam post multiplices bellorum aerumnas Alpes transcendit '
                . 'et fretum, in iuvenem erectus et virum ex omni plaga quam orbis ambit inmensus, '
                . 'reportavit laureas et triumphos, iamque vergens in senium et nomine solo '
                . 'aliquotiens vincens ad tranquilliora vitae discessit.'
            );
            $phone->setPrice($i * 4 + rand(100, 500));
            $manager->persist($phone);
        }

        for ($e = 1; $e < 11; $e++) {
            $phone = new Phone();
            $phone->setMark('Noukio');
            $phone->setReference('NK-'.$e);
            $phone->setDescription(
                'The Noukio HH-' . $e . ' has all you could never imagine in a smartphone ! '
                . 'Vita est illis semper in fuga uxoresque mercenariae conductae ad tempus '
                . 'ex pacto atque, ut sit species matrimonii, dotis nomine futura coniunx '
                . 'hastam et tabernaculum offert marito, post statum diem si id elegerit '
                . 'discessura, et incredibile est quo ardore apud eos in venerem uterque solvitur '
                . 'sexus.Eius populus ab incunabulis primis ad usque pueritiae tempus extremum, '
                . 'quod annis circumcluditur fere trecentis, circummurana pertulit bella, deinde '
                . 'aetatem ingressus adultam post multiplices bellorum aerumnas Alpes transcendit '
                . 'et fretum, in iuvenem erectus et virum ex omni plaga quam orbis ambit inmensus, '
                . 'reportavit laureas et triumphos, iamque vergens in senium et nomine solo '
                . 'aliquotiens vincens ad tranquilliora vitae discessit.'
            );
            $phone->setPrice($e * 4 + rand(100, 500));
            $manager->persist($phone);
        }

        for ($a = 1; $a < 11; $a++) {
            $phone = new Phone();
            $phone->setMark('Ifon');
            $phone->setReference('IF-'.$a);
            $phone->setDescription(
                'The Ifon IF-' . $a . ' will never disappoint you ! '
                . 'Vita est illis semper in fuga uxoresque mercenariae conductae ad tempus '
                . 'ex pacto atque, ut sit species matrimonii, dotis nomine futura coniunx '
                . 'hastam et tabernaculum offert marito, post statum diem si id elegerit '
                . 'discessura, et incredibile est quo ardore apud eos in venerem uterque solvitur '
                . 'sexus.Eius populus ab incunabulis primis ad usque pueritiae tempus extremum, '
                . 'quod annis circumcluditur fere trecentis, circummurana pertulit bella, deinde '
                . 'aetatem ingressus adultam post multiplices bellorum aerumnas Alpes transcendit '
                . 'et fretum, in iuvenem erectus et virum ex omni plaga quam orbis ambit inmensus, '
                . 'reportavit laureas et triumphos, iamque vergens in senium et nomine solo '
                . 'aliquotiens vincens ad tranquilliora vitae discessit.'
            );
            $phone->setPrice($a * 4 + rand(100, 500));
            $manager->persist($phone);
        }

        for ($b = 1; $b < 11; $b++) {
            $phone = new Phone();
            $phone->setMark('Wiwo');
            $phone->setReference('WI-'.$b);
            $phone->setDescription(
                'The Wiwo IF-' . $b . ' is one of the best smartphone of the galaxy ! '
                . 'Vita est illis semper in fuga uxoresque mercenariae conductae ad tempus '
                . 'ex pacto atque, ut sit species matrimonii, dotis nomine futura coniunx '
                . 'hastam et tabernaculum offert marito, post statum diem si id elegerit '
                . 'discessura, et incredibile est quo ardore apud eos in venerem uterque solvitur '
                . 'sexus.Eius populus ab incunabulis primis ad usque pueritiae tempus extremum, '
                . 'quod annis circumcluditur fere trecentis, circummurana pertulit bella, deinde '
                . 'aetatem ingressus adultam post multiplices bellorum aerumnas Alpes transcendit '
                . 'et fretum, in iuvenem erectus et virum ex omni plaga quam orbis ambit inmensus, '
                . 'reportavit laureas et triumphos, iamque vergens in senium et nomine solo '
                . 'aliquotiens vincens ad tranquilliora vitae discessit.'
            );
            $phone->setPrice($b * 4 + rand(100, 500));
            $manager->persist($phone);
        }

        $client = new Client();
        $client->setName('alpha');
        $user = new User();
        $user->setUsername('alphaleader');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'alphaleader'));
        $user->setRoles('ROLE_ADMIN');
        $user->setClient($client);
        $user->setEmail('alphaleader@gmail.com');
        $client->setLeader($user);

        $manager->persist($user);

        for ($z = 1; $z < 10; $z++) {
            $user = new User();
            $user->setUsername('alphauser'. $z);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'alphauser'. $z));
            $user->setRoles('ROLE_USER');
            $user->setClient($client);
            $user->setEmail('alphauser' . $z .'@gmail.com');
            $manager->persist($user);
        }

        $manager->persist($client);

        $client = new Client();
        $client->setName('beta');
        $user = new User();
        $user->setUsername('betaleader');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'betaleader'));
        $user->setRoles('ROLE_ADMIN');
        $user->setClient($client);
        $user->setEmail('betaleader@gmail.com');
        $client->setLeader($user);

        $manager->persist($user);

        for ($x = 1; $x < 10; $x++) {
            $user = new User();
            $user->setUsername('betauser'. $x);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'betauser'. $x));
            $user->setRoles('ROLE_USER');
            $user->setClient($client);
            $user->setEmail('betauser' . $x .'@gmail.com');
            $manager->persist($user);
        }

        $manager->persist($client);

        $manager->flush();
    }
}

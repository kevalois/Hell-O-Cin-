<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\ORM\Doctrine\Populator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use App\DataFixtures\Faker\MovieAndGenreProvider;

use App\Entity\Job;
use App\Entity\Team;
use App\Entity\Movie;
use App\Entity\Genre;
use App\Entity\Person;
use App\Entity\Casting;
use App\Entity\Department;
use App\Entity\User;
use App\Utils\Slugger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    /**
     * On demande à Symfony de nous transmettre le "service" UserPasswordEncoder
     * à l'instanciation de l'objet
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // On crée nos users
        $user = new User();
        $user->setEmail('admin@admin.com');
        // On utilise l'encodeur de mot de passe
        $encodedPassword = $this->passwordEncoder->encodePassword($user, 'admin');
        $user->setPassword($encodedPassword);
        $user->setRoles(['ROLE_ADMIN']);
        // On persiste notre user
        $manager->persist($user);

        $user = new User();
        $user->setEmail('user@user.com');
        // On utilise l'encodeur de mot de passe
        $encodedPassword = $this->passwordEncoder->encodePassword($user, 'user');
        $user->setPassword($encodedPassword);
        $user->setRoles(['ROLE_USER']);
        // On persiste notre user
        $manager->persist($user);

        $generator = Factory::create('fr_FR');
        // On donne le point de départ du random
        $generator->seed('monsuperpointdedepart');

        //ajout provider custom MovieAndGenreProvider 
        //Note : $generator est attendu dans le constructeur de la classe Base de faker
        $generator->addProvider(new MovieAndGenreProvider($generator));

        $populator = new Populator($generator, $manager);
        
        /*
         Faker n'apelle pas le constructeur d'origine donc genres n'est pas setté
         -> effet de bord sur adders qui utilise la methode contains sur du null
        */
        $populator->addEntity(Movie::class, 10, array(
                'title' => function() use ($generator) { return $generator->unique()->movieTitle(); },
                'score' => function() use ($generator) { return $generator->numberBetween(0, 5); },
                'summary' => function() use ($generator) { return $generator->paragraph(); },
                'poster' => null,
            )
        );
            
        $populator->addEntity(Genre::class, 20, array(
            'name' => function() use ($generator) { return $generator->unique()->movieGenre(); },
        ));

        $populator->addEntity(Person::class, 20, array(
            'name' => function() use ($generator) { return $generator->name(); },
        ));
        
        $populator->addEntity(Casting::class, 50, array(
            'orderCredit' => function() use ($generator) { return $generator->numberBetween(1, 10); },
            'role' => function() use ($generator) { return $generator->firstName(); },
        ));
        
        $populator->addEntity(Department::class, 50, array(
            'name' => function() use ($generator) { return $generator->company(); },
        ));

        $populator->addEntity(Job::class, 50, array(
            'name' => function() use ($generator) { return $generator->jobTitle(); },
        ));

        $populator->addEntity(Team::class, 150);

        $inserted = $populator->execute();

        //generated lists
        $movies = $inserted[Movie::class];
        $genres = $inserted[Genre::class];

        foreach ($movies as $movie) {

            shuffle($genres);

            // tableau rand en amont => recuperation des 3 premiers donne une valeur unique par rapport a mt rand
            $movie->addGenre($genres[0]);
            $movie->addGenre($genres[1]);
            $movie->addGenre($genres[2]);

            $manager->persist($movie);
        }
        $manager->flush();
    }
}

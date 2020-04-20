<?php

namespace App\DataFixtures;
use  App\Entity\Topic;
use  App\Entity\Category;
use  App\Entity\User;
use  App\Entity\Participation;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TopicFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // create 3 categories
        for ($i = 0; $i < 3; $i++) {
            $categery = new Category();
            $categery->setName($faker->sentence())
                    ->setCreatedAt($faker->dateTimeBetween('-8 months'))
                    ->setDescription($faker->paragraph());
                    
            $manager->persist($categery);
            // for each category create 1 to 4 topic(s)
            for ($j = 0; $j < mt_rand(1,4); $j++) {
                $topic = new Topic();
                $content = '<p>'.join($faker->paragraphs(5), '</p><p>') . '</p>';
                $topic->setTitle($faker->sentence())
                    ->setFilename($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setDescription($content)
                    ->setLikecount($faker->numberBetween(0,7))
                    ->setDislikecount($faker->numberBetween(0,7))
                    ->setCategory($categery);
                    $author = new User();
                    $author->setAddress($faker->address)
                           ->setUsername($faker->name)
                           ->setEmail($faker->email)
                           ->setPassword($faker->password)
                           ->setExpertise($faker->jobTitle)
                           ->addTopic($topic);
                    $manager->persist($author);
                $manager->persist($topic);

                // for each topic create 0 to 5 participation(s)
                for ($k = 0; $k < mt_rand(0,5); $k++) {
                    $content = '<p>'.join($faker->paragraphs(2), '</p><p>') . '</p>';
                    $participation =new Participation();
                    $now = new \DateTime();
                    $interval = $now->diff($topic->getCreatedAt());
                    $days = $interval->days;
                    $minimum = '-'. $days .' days';
                    $participation->setContent($content)
                                ->setLikecount($faker->numberBetween(0,7))
                                ->setDislikecount($faker->numberBetween(0,7))
                                ->setTopic($topic)
                                 ->setUpdatedAt($faker->dateTimeBetween($minimum));
                    
                    $author = new User();
                    $author->setAddress($faker->address)
                           ->setUsername($faker->name)
                           ->setEmail($faker->email)
                           ->setPassword($faker->password)
                           ->setExpertise($faker->jobTitle)
                           ->addParticipation($participation);
                   
                    $manager->persist($author);
                    $manager->persist($participation);




                }
            }
        }

        $manager->flush();
    }
}

<?php
namespace App\Controller;

use App\Entity\LikeSheet;
use App\Entity\Participation;
use App\Entity\Topic;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikingController extends AbstractController
{
    /**
     * @Route("/liking", name="liking_managing")
     * @Method("POST")
     */
    public function manage(Request $request): Response
    {

        if (isset($_POST['id']) && isset($_POST['type']) && isset($_POST['liking'])) {

            $id = $_POST["id"];
            $liking = ($_POST["liking"] == 0) ? false : true;

            $type = $_POST["type"];

            $em = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository(LikeSheet::class);
            $user = $this->getUser();

           
            
           
            if ($type == 1) {
                $entity = $this->getDoctrine()->getRepository(Topic::class)->findOneBy(['id' => $id]);
                $likesheets = $repository->findBy(
                    array('author' => $user, 'liking' => $liking,'topic' => $entity)
                );
            }
            if ($type == 2) {
                $entity =  $this->getDoctrine()->getRepository(Participation::class)->findOneBy(['id' => $id]);
                $likesheets = $repository->findBy(
                    array('author' => $user, 'liking' => $liking, 'participation' => $entity)
                );
               
            }
           
            if (sizeof($likesheets) == 0) {
                $like = new LikeSheet();

                if ($type == 1) {
                    $like->setAuthor($user)
                        ->setLiking($liking)
                        ->setTargetEntityType($type)
                        ->setTopic($entity);
                }

                if ($type == 2) {
                    $like->setAuthor($user)
                        ->setLiking($liking)
                        ->setTargetEntityType($type)
                        ->setParticipation($entity);
                }

                $entity->addLikeSheet($like);
                $user->addLikesheet($like);
                $em->persist($like);
                $em->persist($user);
                $em->persist($entity);
               
                

            } else {
                if ($likesheets[0]->getLiking() == $liking) {
                    $entity->removeLikeSheet($likesheets[0]);
                } else {
                    $likesheets[0]->setLiking(!$likesheets[0]->getLiking());
                }

            }

            $em->flush();

            return $liking ? new Response($entity->getLike()) : new Response($entity->getDisLike());

        }

        return new Response("No Response");
    }
}

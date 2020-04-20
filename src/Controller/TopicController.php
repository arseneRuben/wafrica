<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Participation;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\ParticipationType;
use App\Repository\TopicRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @Route("/profile/topic")
 */
class TopicController extends AbstractController
{
    /**
     * @Route("/", name="topic_index", methods={"GET"})
     */
    public function index(TopicRepository $topicRepository): Response
    {
        return $this->render('topic/index.html.twig', [
            'topics' => $topicRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="topic_new", methods={"GET","POST"})
     * @Route("/{id}/edit", name="topic_update", methods={"GET","POST"})
     * @isGranted("ROLE_USER")
     */
    public function form(Topic $topic = null, Request $request): Response
    {
        if (!$topic) {
            $topic = new Topic();
        }

        $form = $this->createFormBuilder($topic)
            ->add('title')
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('imageFile', VichImageType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$topic->getId()) {
                $topic->setCreatedAt(new \DateTime());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($topic);
            $entityManager->flush();

            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }

        return $this->render('topic/new.html.twig', [
            'topic' => $topic,
            'editMode' => $topic->getId() !== null,
            'formTopic' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="topic_show", methods={"GET", "POST"})
     */
    public function show(Topic $topic, Request $request): Response
    {
        $comment = new Participation();

        $form = $this->createForm(ParticipationType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$comment->getId()) {
                $comment->setUpdatedAt(new \DateTime());
                $comment->setAuthor($this->getUser());
                $comment->setTopic($topic);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($topic);
                $entityManager->flush();
                $topic->addParticipation($comment);
            }

        }

        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
            'formComment' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="topic_delete", methods={"DELETE"})
     * @isGranted("ROLE_USER")
     */
    public function delete(Request $request, Topic $topic): Response
    {
        if ($this->isCsrfTokenValid('delete' . $topic->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($topic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('topic_index');
    }
}

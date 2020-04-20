<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\RegistrationFormType;
use App\Repository\TopicRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends AbstractController
{

    /**
     * @Route("/home", name="homepage")
     */
   
    
    public function indexAction(Request $request, TopicRepository $topicRepository, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, \Swift_Mailer $mailer): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Formulaire de contact
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

        if($contactForm->isSubmitted() && $contactForm->isValid()){
            $contact = $contactForm->getData();
            
           if ($this->getUser()) {
            $message = (new \Swift_Message("Nouveau message") )
            ->setFrom($this->getUser()->getEmail())
            ->setTo('fopoar@gmail.com')
            ->setBody(
                $this->renderView('emails/contact.html.twig', compact('contact')
            ),
            'text/html'
        );
        $mailer->send($message);
        $this->addFlash('message', 'Le message a ete bien envoye');
       //  return $this->redirectToRoute
           }
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $user = new User();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);
        $registrationForm->handleRequest($request);
        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $registrationForm->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            return $this->redirectToRoute('homepage');
        } else {
            if ($this->getUser()) {
                return $this->render('default/index.html.twig', [
                    'topics' => $topicRepository->findAll(),
                    'registrationForm' => $registrationForm->createView(),
                    'contactForm' => $contactForm->createView(),
                    'last_username' => $lastUsername,
                    'error' => $error,
                ]);
            } else {
                return $this->render('default/index.html.twig', [
                    'topics' => $topicRepository->findAll(),
                    'registrationForm' => $registrationForm->createView(),
                    'contactForm' => $contactForm->createView(),
                    'last_username' => $lastUsername,
                    'error' => $error,
                ]);
            }
        }

    }

}

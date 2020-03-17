<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\TipsRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Security\UserConnexionAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserConnexionAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'picture'=>'inscription',
            'name'=>'Inscription'
        ]);
    }

    /**
     * @Route("/mon_compte", name="myAccount")
     */
    public function viewAccount(UserRepository $userRepository, CategoryRepository $categoryRepository, TipsRepository $tipsRepository): Response
    {
        $user=$this->getuser();
        $tips=$user->getTips();
        $tabTips=[];
        $noActiveTips=[];
        foreach ($tips->toArray() as $tip){
            array_push($tabTips,$tip);
            if($tip->getStatus()=='en attente' OR $tip->getStatus()=='refusé'){
                array_push($noActiveTips,$tip);
            }            
        } 
        return $this->render('registration/myAccount.html.twig', [
            'picture'=>'monCompte',
            'name'=>'Mon compte',
            'categories'=>$categoryRepository->findAll(),
            'tabTips'=>$tabTips,
            'noActiveTips'=>$noActiveTips,
            'user'=>$user
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Form\ModifyPasswordType;
use App\Form\RegistrationFormType;
use App\Repository\TipsRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
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
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserConnexionAuthenticator $authenticator,\Swift_Mailer $mailer): Response
    {
        $userConnected = $this->getuser();
        if($userConnected){
            return $this->redirectToRoute('myAccount');
        }
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

            // we create and save a token
            $user->setActivationToken(md5(uniqid()));
            // send an email
            $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom('moncontact.tips@gmail.com')
                ->setTo($form->get('email')->getData())
                ->setBody(
                    $this->renderView(
                        'emails/activation.html.twig', ['token' => $user->getActivationToken()] 
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $this->addFlash('warning', 'Un email de confirmation vous a été envoyé pour activer votre compte');

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
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $users,\Swift_Mailer $mailer)
    {
        // We search user with this token
        $user = $users->findOneBy(['activationToken' => $token]);

        // if any user has this token
        if(!$user){
            // Error 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // we delete the token
        $user->setActivationToken(null);
        
        // we send a mail
        $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom('moncontact.tips@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/welcome.html.twig', 
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte est bien activé');

        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/modification", name="modifyPassword",methods={"POST"})
     */
    public function modifyPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserConnexionAuthenticator $authenticator,\Swift_Mailer $mailer): Response
    {
        $user = $this->getuser();
        $form = $this->createForm(ModifyPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // send an email to confirm the modification
            $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom('mon@adresse.fr')
                ->setTo($user->getemail())
                ->setBody(
                    $this->renderView(
                        'emails/confirmModificationPassword.html.twig',
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
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
            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié '
            );

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
            return $this->redirectToRoute('main');
        } 
        if($form->isSubmitted()){
            $this->addFlash(
                'warning',
                ' Erreur vous n\'avez pas saisie le même mot de passe '
            );
            
            return $this->redirectToRoute('myAccount');
        }

        return $this->render('registration/_modifyPassword.html.twig', [
            'registrationForm' => $form->createView(),
            'picture'=>'inscription',
            'name'=>'Modification du mot de passe'
        ]);
    }

    /**
     * @Route("/mon_compte", name="myAccount")
     */
    public function viewAccount(CategoryRepository $categoryRepository, TipsRepository $tipsRepository): Response
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
            'categories' => $categoryRepository->findBy(
                [],
                ['nameCategory' => 'ASC']
            ),
            'tabTips'=>$tabTips,
            'noActiveTips'=>$noActiveTips,
            'user'=>$user
        ]);
    }

    /**
     * @Route("/mon_compte/{id}", name="myTips")
     */
    public function viewMyTips(Category $category,Request $request, PaginatorInterface $paginator, CategoryRepository $categoryRepository, TipsRepository $tipsRepository): Response
    {
        $user=$this->getuser();
        $tips=$user->getTips();
        $tabTips=[];
        foreach ($tips->toArray() as $tip){
            if($tip->getCategory()->getId() == $category->getId()){
                if ($tip->getStatus() == 'actif'){
                    array_push($tabTips,$tip);
                }
            }            
        } 
        $pagination = $paginator->paginate(
            $tabTips, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('registration/myTips.html.twig', [
            'category' => $category,
            'categories' => $categoryRepository->findBy(
                [],
                ['nameCategory' => 'ASC']
            ),
            'pagination'=>$pagination,
            'user'=>$user
        ]);
    }
}

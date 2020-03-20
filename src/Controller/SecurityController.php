<?php

namespace App\Controller;

use DateInterval;
use App\Entity\Tips;
use App\Repository\TipsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SecurityController extends AbstractController
{
    /**
     * @Route("/connexion", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,
        'picture'=>'connexion',
        'name'=>'Connexion'
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/moderation", name="moderation")
     * @IsGranted("ROLE_ADMIN")
     * 
     */
    public function moderate(TipsRepository $tipsRepository, Request $request,\Swift_Mailer $mailer): Response
    {
        
        // ====== refuse a tips ===========================
        $getReasonRefuse = htmlspecialchars($request->request->get('reasonRefuse'));
        if (empty($getReasonRefuse)==false){
            $tip=$tipsRepository->findby([
                'id' => $request->request->get('tipsId')
            ]);
            $tip[0]->setRefusalReason($getReasonRefuse);
            $tip[0]->setStatus('refusé');
            $entityManager = $this->getDoctrine()->getManager();
            
            $test=$tip[0]->getUsers($this);
            foreach ($test->toArray() as $user){   
            // send an email to warn of refusal
            $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom('mon@adresse.fr')
                ->setTo($user->getemail())
                ->setBody(
                    $this->renderView(
                        'emails/refusedTips.html.twig'
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
           }
        }
        
        // get all tips where status aren't "actif"
        $unvalidTips = $tipsRepository->findby([
            'status' => ['en attente', 'refusé']
        ]);
        // delete a refused tips if it has existed for more than a month
        $entityManager = $this->getDoctrine()->getManager();
        for ($i=0;$i<count($unvalidTips);$i++){
            $date1M=$unvalidTips[$i]->getCreatedAt();
            $date1M->add(new DateInterval('P1M'));
            if (($unvalidTips[$i]->getStatus() == 'refusé') AND $date1M<new \DateTime()) {
                $entityManager->remove($unvalidTips[$i]);
            }
        }

        $entityManager->flush();
        

        return $this->render('security/moderation.html.twig', [
        'picture'=>'moderation',
        'name'=>'Modération',
        'unvalidTips'=>$unvalidTips
        ]);
    }
    /**
     * @Route("/{id}/validTIPS", name="validTips",methods={"VALIDTIPS"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function validTIPS(Request $request, Tips $tip): Response
    {
         if ($this->isCsrfTokenValid('validtips'.$tip->getId(), $request->request->get('_token')) && $this->getUser()) {
            $entityManager = $this->getDoctrine()->getManager();
            $tip->setStatus('actif');
            $entityManager->flush();
        } 
        return $this->redirectToRoute('moderation');
    }
}

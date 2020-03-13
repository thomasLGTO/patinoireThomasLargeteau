<?php

namespace App\Controller;

use App\Entity\Tips;
use App\Form\TipsType;
use App\Repository\TipsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tips")
 */
class TipsController extends AbstractController
{
    /**
     * @Route("/", name="tips_index", methods={"GET"})
     */
    public function index(TipsRepository $tipsRepository): Response
    {
        return $this->render('tips/index.html.twig', [
            'tips' => $tipsRepository->findAll(),
            'name' => 'Accueil',
            'picture'=>'pictureHome'
        ]);
    }

    /**
     * @Route("/new", name="tips_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tip = new Tips();
        $form = $this->createForm(TipsType::class, $tip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tip->addUser($this->getUser());
            $tip->setNumberUsers(1);
            $tip->setCreatedAt(new \DateTime());
            $tip->setStatus('en attente');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tip);
            $entityManager->flush();

            return $this->redirectToRoute('tips_index');
        }

        return $this->render('tips/new.html.twig', [
            'tip' => $tip,
            'form' => $form->createView(),
            'picture'=>'newTipsPage',
            'name'=>'Déposer un tips'
        ]);
    }

    /**
     * @Route("/{id}", name="tips_show", methods={"GET"})
     */
    public function show(Tips $tip): Response
    {
        return $this->render('tips/show.html.twig', [
            'tip' => $tip,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tips_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tips $tip): Response
    {
        dump($tip->getCategory()->getId());
        $form = $this->createForm(TipsType::class, $tip);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('tips_index');
        }
        return $this->render('tips/edit.html.twig', [
            'tip' => $tip,
            'form' => $form->createView(),
            'name' => 'Modifier le tips',
            'picture'=>'pictureHome'
        ]);
    }

    /**
     * @Route("/{id}/newUser", name="tips_newUser",methods={"USETIPS"})
     */
    public function newUSer(Request $request, Tips $tip): Response
    {
        $users=$tip->getUsers();
        $j=0; 
        // add user when the button " j'utilise ce tips " is on click
            foreach ($users->toArray() as $user){
                if ($user == $this->getUser()){
                    $entityManager = $this->getDoctrine()->getManager();
                    $tip->removeUser($this->getUser());
                    $tip->setNumberUsers($tip->getNumberUsers()-1);
                    $entityManager->flush();
                    $j=1;
                    break;
                } 
           }
           if ($j == 1){}
           else if ($this->isCsrfTokenValid('usetips'.$tip->getId(), $request->request->get('_token')) && $this->getUser()) {
            $entityManager = $this->getDoctrine()->getManager();
            $tip->addUser($this->getUser());
            $tip->setNumberUsers($tip->getNumberUsers()+1);
            $entityManager->flush();
        } 
        return $this->redirectToRoute('category_show',array('id'=>$tip->getCategory()->getId()));
    }

    /**
     * @Route("/{id}", name="tips_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tips $tip): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tip->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tips_index');
    }
}

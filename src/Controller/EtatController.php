<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Form\CreateEtatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtatController extends AbstractController
{
    /**
     * @Route("/etat/create", name="etat_create")
     */
    public function create(Request $request,
                            EntityManagerInterface $entityManager
            ): Response
    {
       $etat = new Etat();
       $createEtatForm = $this->createForm(CreateEtatType::class,$etat);
       $createEtatForm->handleRequest($request );

       if ($createEtatForm->isSubmitted()&&$createEtatForm->isValid()){
           $entityManager->persist($etat);
           $entityManager->flush();
           $this->addFlash('success','Wish Added ! Good job.');
       }
        return $this->render('etat/create.html.twig',[
            'createEtatForm' => $createEtatForm->createView()
    ]);
    }
}

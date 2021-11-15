<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Ville;
use App\Form\CreateEtatType;
use App\Form\CreateVilleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/ville/create", name="ville_create")
     */
    public function create(Request $request,
                           EntityManagerInterface $entityManager
    ): Response
    {
        $ville = new Ville();
        $createVilleForm = $this->createForm(CreateVilleFormType::class,$ville);
        $createVilleForm->handleRequest($request );

        if ($createVilleForm->isSubmitted()&&$createVilleForm->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success','Ville Added ! Good job.');
        }
        return $this->render('ville/create.html.twig',[
            'createVilleForm' => $createVilleForm->createView()
        ]);
    }
}

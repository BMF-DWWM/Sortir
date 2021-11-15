<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Form\CreateEtatType;
use App\Form\CreateLieuformType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieu/create", name="lieu_create")
     */
    public function create(Request $request,
                           EntityManagerInterface $entityManager
    ): Response
    {
        $lieu = new Lieu();
        $createLieuForm = $this->createForm(CreateLieuformType::class,$lieu);
        $createLieuForm->handleRequest($request );

        if ($createLieuForm->isSubmitted()&&$createLieuForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success','Lieu Added ! Good job.');
        }
        return $this->render('lieu/create.html.twig',[
            'createLieuForm' => $createLieuForm->createView()
        ]);
    }
}

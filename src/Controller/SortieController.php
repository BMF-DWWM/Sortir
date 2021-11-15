<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CreateEtatType;
use App\Form\CreateSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/create", name="sortie_create")
     */
    public function create(Request $request,
                           EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = new Sortie();
        $createSortieForm = $this->createForm(CreateSortieType::class,$sortie);
        $createSortieForm->handleRequest($request );

        if ($createSortieForm->isSubmitted()&&$createSortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Sortie Added ! Good job.');
        }
        return $this->render('sortie/create.html.twig',[
            'createSortieForm' => $createSortieForm->createView()
        ]);
    }
}

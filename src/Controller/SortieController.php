<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CreateEtatType;
use App\Form\CreateSortieType;
use App\Form\SearchSortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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
        $dateDebutSortie = ($createSortieForm->get('dateHeureDebut')->getData());

        $sortie->setOrganisateur($this->getUser());

        if ($createSortieForm->isSubmitted()&&$createSortieForm->isValid()){
            $sortie->setDateLimiteInscription($dateDebutSortie);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Sortie Added ! Good job.');
        }
        return $this->render('sortie/create.html.twig',[
            'createSortieForm' => $createSortieForm->createView()
        ]);
    }

    /**
     * @Route("/sortie/list", name="sortie_list")
     */
    public function list(SortieRepository $sortieRepository,
                        Request $request
    ): Response
    {
       $formSearch = $this->createForm(SearchSortieType::class);
       $search = $formSearch->handleRequest($request);
       $sorties = $sortieRepository ->findAll();

       if ($formSearch->isSubmitted()&&$formSearch->isValid()){
           $sorties = $sortieRepository->search(
               $search->get('mots')->getData(),
               $search->get('campus')->getData(),
               $search->get('date1')->getData(),
               $search->get('date2')->getData()

           );
       }

       return $this->render('sortie/list.html.twig',[
            'sorties'=> $sorties,
            'formSearch' => $formSearch->createView()
       ]);
    }


}

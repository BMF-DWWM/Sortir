<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\CreateEtatType;
use App\Form\CreateLieuformType;
use App\Form\CreateSortieType;
use App\Form\SearchSortieType;
use App\Repository\EtatRepository;
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
                           EntityManagerInterface $entityManager,
                            EtatRepository $etatRepository
    ): Response
    {
        $sortie = new Sortie();
        $createSortieForm = $this->createForm(CreateSortieType::class,$sortie);
        $createSortieForm->handleRequest($request );
        $dateDebutSortie = ($createSortieForm->get('dateHeureDebut')->getData());
        $sortie->setOrganisateur($this->getUser());

        $lieu = new Lieu();
        $createLieuForm= $this->createForm(CreateLieuformType::class,$lieu);
        $createLieuForm->handleRequest($request);



        if ($createLieuForm->isSubmitted()&&$createLieuForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();
            $sortie->setLieu($lieu);
            $this->addFlash('success','Lieu Added ! Good job.');

        }


        if ($createSortieForm->isSubmitted()&&$createSortieForm->isValid()){
            $sortie->setEtat($etatRepository->find('4'));
            $sortie->setDateLimiteInscription($dateDebutSortie);
            $sortie->addMembreInscrit($this->getUser());
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Sortie Added ! Good job.');
        }
        return $this->render('sortie/create.html.twig',[
            'createLieuForm'=> $createLieuForm->createView(),
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
       $sorties = $sortieRepository ->findAll();


       $formSearch = $this->createForm(SearchSortieType::class);
       $search = $formSearch->handleRequest($request);


       if ($formSearch->isSubmitted()&&$formSearch->isValid()){
           $sorties = $sortieRepository->search(
               $search->get('mots')->getData(),
               $search->get('campus')->getData(),
               $search->get('date1')->getData(),
               $search->get('date2')->getData(),
               $search->get('jeSuisOrganisateur')->getData(),
               $this->getUser()

           );
       }

       return $this->render('sortie/list.html.twig',[
            'sorties'=> $sorties,
            'formSearch' => $formSearch->createView()
       ]);
    }
    /**
     * @Route("/sortie/modifier", name="sortie_modifier")
     */
    public function modifier(SortieRepository $sortieRepository,
                         EntityManagerInterface $entityManager,
                         Request $request
    ): Response
    {

            $sortie= $sortieRepository->find($_GET["id"]);
            $formModifSortie = $this->createForm(CreateSortieType::class, $sortie);
            $formModifSortie->handleRequest($request);

            $sorties = $sortieRepository ->findAll();
            $formSearch = $this->createForm(SearchSortieType::class);


            if ($formModifSortie->isSubmitted()&&$formModifSortie->isValid()){
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success','Modif Added ! Good job.');
                return $this->render('sortie/list.html.twig',[
                    'sorties'=> $sorties,
                    'formSearch' => $formSearch->createView()
                ]);
            }





        return $this->render('sortie/modifier.html.twig',[
            'sortie'=>$sortie,
            'formModifSortie' => $formModifSortie->createView()
        ]);
    }

    /**
     * @Route("/sortie/desiste", name="sortie_desiste")
     */
    public function desiste(SortieRepository $sortieRepository,
                             Request $request,
                            EntityManagerInterface $entityManager
    ): Response
    {

        if ($_POST["desiste"] =! null){
            $sortie= $sortieRepository->find($_GET["id"]);
            $sortie->removeMembreInscrit($this->getUser());
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','You leave ! it\'s not a Good job.');

        }

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
    /**
     * @Route("/sortie/annuler", name="sortie_annuler")
     */
    public function annuler(SortieRepository $sortieRepository,
                            Request $request,
                            EntityManagerInterface $entityManager
    ): Response
    {

        if ($_POST["annuler"] =! null){
            $sortie= $sortieRepository->find($_GET["id"]);
            $entityManager->remove($sortie);
            $entityManager->flush();
            $this->addFlash('success','Sortie annuler ! it\'s not a Good job.');

        }

        $formSearch = $this->createForm(SearchSortieType::class);
        $sorties = $sortieRepository ->findAll();



        return $this->render('sortie/list.html.twig',[
            'sorties'=> $sorties,
            'formSearch' => $formSearch->createView()
        ]);
    }

    /**
     * @Route("/sortie/inscription", name="sortie_inscription")
     */
    public function inscription(SortieRepository $sortieRepository,
                         Request $request,
                        EntityManagerInterface $entityManager

    ): Response
    {


        if ($_POST["inscription"] =! null){
            $sortie= $sortieRepository->find($_GET["id"]);
            $sortie->addMembreInscrit($this->getUser());
            $this->addFlash('success','Inscritption Added ! Good job.');

        }



        $entityManager->persist($sortie);
        $entityManager->flush();
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

    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public function detail(Sortie $sortie
    ): Response
    {
        return $this->render('sortie/detail.html.twig',[
            'sortie' => $sortie
        ]);
    }

}

<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\CreateLieuformType;
use App\Form\CreateSortieType;
use App\Form\SearchParticipantType;
use App\Form\SearchSortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_view")
     */
    public function view(ParticipantRepository $participantRepository, Request $request): Response
    {
        $participants = $participantRepository ->findAll();


        $formSearch = $this->createForm(SearchParticipantType::class);
        $search = $formSearch->handleRequest($request);


        if ($formSearch->isSubmitted()&&$formSearch->isValid()){
            $participants = $participantRepository->search(
                $search->get('mots')->getData(),
                $search->get('campus')->getData(),
                $this->getUser()

            );
        }


        return $this->render('admin/index.html.twig',[
            'participants'=> $participants,
            'controller_name' => 'AdminController',
            'formSearch' => $formSearch->createView()
        ]);

    }

    /**
     * @Route("/admin/lieu/create", name="admin_lieu_view")
     */
    public function viewLieu(): Response
    {
        return $this->redirectToRoute('lieu_create');
    }

    /**
     * @Route("/admin/campus/create", name="admin_campus_view")
     */
    public function viewCampus(): Response
    {
        return $this->redirectToRoute('campus_create');
    }

    /**
     * @Route("/admin/registration/register", name="admin_registration_view")
     */
    public function viewRegistration(): Response
    {
        return $this->redirectToRoute('app_register');
    }

    /**
     * @Route("/admin/participant/list", name="participant_list")
     */
    public function list(ParticipantController $participantRepository,
                         Request $request
    ): Response
    {
        $participants = $participantRepository->findAll();


        $formSearch = $this->createForm(SearchParticipantType::class);
        $search = $formSearch->handleRequest($request);


        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $participants = $participantRepository->search(
                $search->get('mots')->getData(),
                $search->get('campus')->getData(),
                $this->getUser()

            );
        }


        return $this->render('admin/index.html.twig', [
            'participants' => $participants,
            'formSearch' => $formSearch->createView()
        ]);
    }

    /**
     * @Route("/admin/participant/inactif", name="participant_inactif")
     *
     */
        public function inactif(ParticipantRepository $participantRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $participant= $participantRepository->find($_GET["id"]);
        $participant->setActif(false);
        $entityManager->persist($participant);
        $entityManager->flush();

        return $this->redirectToRoute('admin_view');
    }
    /**
     * @Route("/admin/participant/actif", name="participant_actif")
     *
     */
        public function actif(ParticipantRepository $participantRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $participant= $participantRepository->find($_GET["id"]);
        $participant->setActif(true);
        $entityManager->persist($participant);
        $entityManager->flush();


        return $this->redirectToRoute('admin_view');
    }

    /**
     * @Route("/admin/participant/suppression", name="participant_suppression")
     *
     */
        public function suppression(ParticipantRepository $participantRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $participant= $participantRepository->find($_GET["id"]);

        $entityManager->remove($participant);
        $entityManager->flush();


        return $this->redirectToRoute('admin_view');
    }

}

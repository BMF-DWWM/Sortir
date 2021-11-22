<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\CreateLieuformType;
use App\Form\SearchSortieType;
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
    public function view(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
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
     * @Route("/participant/list", name="sortie_list")
     */
    public function list(ParticipantController $participantRepository,
                         Request $request
    ): Response
    {
        $participants = $participantRepository ->findAll();


        $formSearch = $this->createForm(SearchParticipantType::class);
        $search = $formSearch->handleRequest($request);


        if ($formSearch->isSubmitted()&&$formSearch->isValid()){
            $participants = $participantRepository->search(
                $search->get('mots')->getData(),
                $search->get('campus')->getData(),
                $search->get('date1')->getData(),
                $search->get('date2')->getData(),
                $search->get('jeSuisOrganisateur')->getData(),
                $this->getUser()

            );
        }

        return $this->render('admin/index.html.twig',[
            'participants'=> $participants,
            'formSearch' => $formSearch->createView()
        ]);
    }
}

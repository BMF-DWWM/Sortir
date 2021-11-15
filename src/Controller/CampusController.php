<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Form\CampusCreateFormType;
use App\Form\CreateEtatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus/create", name="campus_create")
     */
    public function create(Request $request,
                           EntityManagerInterface $entityManager
    ): Response
    {
        $campus = new Campus();
        $createCaampusForm = $this->createForm(CampusCreateFormType::class,$campus);
        $createCaampusForm->handleRequest($request );

        if ($createCaampusForm->isSubmitted()&&$createCaampusForm->isValid()){
            $entityManager->persist($campus);
            $entityManager->flush();
            $this->addFlash('success','Campus Added ! Good job.');
        }
        return $this->render('campus/create.html.twig',[
            'createCaampusForm' => $createCaampusForm->createView()
        ]);
    }
}

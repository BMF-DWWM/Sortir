<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\UpdatePhotoProfilType;
use App\Form\UpdateProfilType;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant", name="participant_view")
     */
    public function view(ParticipantRepository $participantRepository,Request $request): Response
    {
        $user = $participantRepository->find($_GET["id"]);
        return $this->render('participant/view.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/participant/MonProfil", name="participant_modifier")
     */
    public function modifierProfil(Request $request,
                             UserAuthenticatorInterface $authenticator,
                             AppAuthenticator $appAuthenticator
    ): Response
    {
        $user = $this ->getUser();
        $form = $this->createForm(UpdateProfilType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            return $this->redirectToRoute("participant_view", [
                "id" => $user->getId(),
            ]);
        }
        return $this->render('participant/profil.html.twig', [
            'user' => $user,
            'UpdateProfil' => $form->createView(),

        ]);
    }
    /**
     * @Route("/participant/MonProfil/NouveauMotDePasse", name="participant_NouveauMotDePasse")
     */
    public function NouveauMotDePasse(Request $request,
                                      UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        dump($user);
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            if ($oldPassword = $passwordHasher ->isPasswordValid($user,$form->get('oldPassword')->getData())) {

                $password = $form->get('password')->getData();
                $newEncodedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($newEncodedPassword);
                $em->persist($user);
                $em->flush();
                $this->addFlash('notice', 'Votre mot de passe à bien été changé !');
                return $this->redirectToRoute('participant_modifier');
            }
              else {
                    $form->addError(new FormError('Ancien mot de passe incorrect'));
                }
        }
        return $this->render('participant/mdp.html.twig', array(

            'UpdatePass' => $form->createView(),
            'User' => $user
        ));
    }

    /**
     * @Route("/participant/MonProfil/ModifierPhotoProfil", name="participant_modifier_PhotoProfil")
     * @return Response
     */
    public function modifierPhotoProfil(Request $request, SluggerInterface $slugger, FileUploader $fileUploader,EntityManager $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UpdatePhotoProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $user->setBrochureFilename($brochureFileName);
            }

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setBrochureFilename($newFilename);
            }

            $em->persist($user);
            $em->flush();
            $this->addFlash('notice', 'Votre photo de profil à bien été changé !');

            return $this->redirectToRoute('participant_modifier');

        }

        return $this->renderForm('product/new.html.twig', [
            'UpdatePhotoProfil' => $form,
        ]);
    }
}

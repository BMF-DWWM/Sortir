<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ChangePasswordType;
use App\Form\RegistrationFormType;
use App\Form\UpdateProfilType;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

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

}

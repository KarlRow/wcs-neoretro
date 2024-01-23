<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Form\RegistrationGameFormType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Game;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('dashboard/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'pageTitle' => 'NeoRetro',
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): never
    {
        throw new Exception();
    }

    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie. Vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('dashboard');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'pageTitle' => 'NeoRetro',
        ]);
    }

    #[Route('/newgame', name: 'new_game')]
    public function game(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $game = new Game();
        $form = $this->createForm(RegistrationGameFormType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($game->getTitle()) {
                $slug = $slugger->slug($game->getTitle());
                $game->setSlug($slug);
            }

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard', [], Response::HTTP_SEE_OTHER);
        }
            $games = $entityManager->getRepository(Game::class)->findAll();
            return $this->render('admin/new_game.html.twig', [
                'registrationGameForm' => $form->createView(),
                'pageTitle' => 'Admin.Add Game',
                'games' => $games,
            ]);
    }
}

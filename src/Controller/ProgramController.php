<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\User;
use App\Form\ProgramType;
use App\Form\CommentType;
use App\Service\ProgramDuration;
use Symfony\Component\Mime\Email;
use App\Repository\SeasonRepository;
use App\Repository\CommentRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController

{
    #[Route('/', name: 'index')]

    public function index(ProgramRepository $programRepository, RequestStack $requestStack): Response
    {
        $programs = $programRepository->findAll();
        $session = $requestStack->getSession();
        // if (!$session->has('total')) {
        //     $session->set('total', 0) : ; // if total doesn’t exist in session, it is initialized.
        // }
    
        $total = $session->get('total'); // get actual value in session with ‘total' key.
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ProgramRepository $programRepository, SluggerInterface $slugger, MailerInterface $mailer): Response
    {
        $program = new Program();
    

        // Create the form, linked with $category
        $form = $this->createForm(ProgramType::class, $program);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // créer le slug en db de program à partir du form de création
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());
            $programRepository->save($program, true); 
            // message de confirmation de succès de la soumission du form
            $this->addFlash('success', 'The new program has been created');
            // mail de confirmation de succès de la soumission du form
            $email = (new Email())
            ->from($this->getParameter('mailer_from'))
            ->to('your_email@example.com')
            ->subject('Une nouvelle série vient d\'être publiée !')
            ->html($this->renderView('Program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);
            // renvoie vers la page d'acceuil
            return $this->redirectToRoute('program_index');
        } else {
            // si le form n'a pas fonctionné
            $this->addFlash('danger', 'The new program hasn\'t been created');
        }
        // Render the form (best practice)
        return $this->renderForm('program/new.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    
    }

    #[Route('/{slug}/', name: 'show')]
    public function show(string $slug, Program $program, ProgramDuration $programDuration): Response
    {
        if (!$program) {
        throw $this->createNotFoundException(
            'No program with name : '.$slug.' found in program\'s table.'
        );
    }
    return $this->render('program/show.html.twig', [
        'program' => $program,
        'programDuration' => $programDuration->calculate($program)
    ]);
}

#[Route('/{program_slug}/seasons/{season_id}', name: 'season_show')]
#[Entity('program', options: ['mapping' => ['program_slug' => 'slug']])]
#[Entity('seasons', options: ['mapping' => ['season_id' => 'id']])]
    public function showSeason(Program $program, Season $season): Response
    {
        return $this->render('program/season.html.twig', [
            'season' => $season,
            'program' => $program,
        ]);
    }

#[Route('/{program_slug}/seasons/{season_id}/episode/{episode_slug}', name: 'episode_show')]
#[Entity('program', options: ['mapping' => ['program_slug' => 'slug']])]
#[Entity('seasons', options: ['mapping' => ['season_id' => 'id']])]
#[Entity('episode', options: ['mapping' => ['episode_slug' => 'slug']])]
public function showEpisode (Request $request, Program $program, Season $season,  Episode $episode, CommentRepository $commentRepository): Response
{
    $comment = new Comment;
    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);
    $user = $this->getUser();
    $allComments = $commentRepository->findBy(
        ['episode' => $episode],
        ['id' => 'ASC']
    );

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($user);
            $comment->setEpisode($episode);
            $commentRepository->save($comment, true);
            $this->addFlash('success', 'Your comment has been sent');
        } else {
            $this->addFlash('danger', 'Your comment hasn\'t been sent');
        }

    return $this->render('program/episode.html.twig', [
        'episode' => $episode,
        'season' => $season,
        'program' => $program,
        'form' => $form->createView(),
        'comments' => $comment,
        'allcomments' => $allComments
    ]);
} 

#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Program $program, ProgramRepository $programRepository, SluggerInterface $slugger): Response
{
    $form = $this->createForm(ProgramType::class, $program);
    $form->handleRequest($request);
    if ($this->getUser() !== $program->getOwner()) {
        // If not the owner, throws a 403 Access Denied exception
        throw $this->createAccessDeniedException('Only the owner can edit the program!');
    }
    if ($form->isSubmitted() && $form->isValid()) {
        $slug = $slugger->slug($program->getTitle());
        $program->setSlug($slug);
        $programRepository->save($program, true);


        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('program/edit.html.twig', [
        'program' => $program,
        'form' => $form,
    ]);
}

#[Route('/{id}', name: 'delete', methods: ['POST'])]
public function delete(Request $request, Program $program, ProgramRepository $programRepository): Response
{
    if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
        $programRepository->remove($program, true);
    }

    return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
}

}


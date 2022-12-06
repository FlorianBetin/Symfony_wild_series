<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ProgramType;
use Symfony\Component\HttpFoundation\Request;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController

{
    #[Route('/', name: 'index')]

    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ProgramRepository $programRepository): Response
    {
        $program = new Program();
    
        // Create the form, linked with $category
        $form = $this->createForm(ProgramType::class, $program);
    
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            // Deal with the submitted data
            // For example : persiste & flush the entity
            // And redirect to a route that display the result
            $programRepository->save($program, true); 
            return $this->redirectToRoute('program_index');
        }
    
    
        // Render the form (best practice)
        return $this->renderForm('program/new.html.twig', [
            'form' => $form,
        ]);
    
    }

    #[Route('/{id}/', name: 'show')]
    public function show(int $id, Program $program): Response
    {
        if (!$program) {
        throw $this->createNotFoundException(
            'No program with id : '.$id.' found in program\'s table.'
        );
    }
    return $this->render('program/show.html.twig', [
        'program' => $program,
    ]);
}

#[Route('/{program_id}/seasons/{season_id}', name: 'season_show')]
#[Entity('program', options: ['mapping' => ['program_id' => 'id']])]
#[Entity('seasons', options: ['mapping' => ['season_id' => 'id']])]
    public function showSeason(Program $program, Season $season): Response
    {
        return $this->render('program/season.html.twig', [
            'season' => $season,
            'program' => $program,
        ]);
    }

#[Route('/{program_id}/seasons/{season_id}/episode/{episode_id}', name: 'episode_show')]
#[Entity('program', options: ['mapping' => ['program_id' => 'id']])]
#[Entity('seasons', options: ['mapping' => ['season_id' => 'id']])]
#[Entity('episode', options: ['mapping' => ['episode_id' => 'id']])]
public function showEpisode (Program $program, Season $season, Episode $episode): Response
{
    return $this->render('program/episode.html.twig', [
        'episode' => $episode,
        'season' => $season,
        'program' => $program,
    ]);
} 


}


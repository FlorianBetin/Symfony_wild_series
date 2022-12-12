<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'new')]
public function new(Request $request, CategoryRepository $categoryRepository): Response
{
    $category = new Category();

    // Create the form, linked with $category
    $form = $this->createForm(CategoryType::class, $category);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Deal with the submitted data
        // For example : persiste & flush the entity
        // And redirect to a route that display the result
        $categoryRepository->save($category, true); 
        $this->addFlash('success', 'The new category has been created');
        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    } else {
        $this->addFlash('danger', 'The new category hasn\'t been created');
    }

    // Render the form (best practice)
    return $this->renderForm('category/new.html.twig', [
        'form' => $form,
    ]);

}

    #[Route('/{categoryName}/', methods: ['GET'], name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        if (!$category) {
        throw $this->createNotFoundException(
            'No category with name : '.$categoryName.' found in program\'s table.'
        );
    }
        $programs = $programRepository->findBy(array('category'=> $category), array('id' => 'DESC'), 3, 0 );
    
    return $this->render('category/show.html.twig', [
        'category' => $category,
        'programs' => $programs,
    ]);
}


}
<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request,EntityManagerInterface $em): Response
    {
        $recipes = $em->getRepository(Recipe::class)->findRecipeWithDurationLowerThan(10);
        // $recipes[0]->setTitle('Pâte à la bolognaise simple') ; Mise à jour
        //* Insert :
        // $recipe = new Recipe() ;
        // $recipe->setTitle('Barbe à papa') 
        //        ->setSlug('barbe-a-papa')
        //        ->setContent('Mettez du sucre')
        //        ->setCreatedAt(new DateTimeImmutable())
        //        ->setUpdateAt(new DateTimeImmutable())
        //        ->setDuration(2)
        // ;
        // $em->persist($recipe);
        // $em->flush() ;

        //* Supprimer
        // $em->remove($recipes[0]);
        // $em->flush() ;

        // dd($recipes);

        //* Durée Total
        // $recipe = $em->getRepository(Recipe::class)->findTotalDuration() ;
        // dd($recipe) ;
        
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]) ;
    }

    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id) ;
        if($recipe->getSlug() !== $slug)  {
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId(), 'slug' => $recipe->getSlug()]) ;
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe 
        ]) ;
    }

    #[Route('/recettes/{id}/edit', name:'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $em) : Response
    {
        $form = $this->createForm(RecipeType::class, $recipe) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdateAt(new DateTimeImmutable()) ;
            $em->flush() ;
            $this->addFlash('success', 'Le recette a été bien modifier!') ;
            return $this->redirectToRoute('recipe.index') ;
        }
        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe ,
            'form' => $form
        ]) ;
    }
    #[Route('/recettes/add', name: 'recipe.create')]
    public function create(Request $request, EntityManagerInterface $em) : Response
    {
        $recipe = new Recipe() ;
        $form = $this->createForm(RecipeType::class, $recipe) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new DateTimeImmutable()) ;
            $recipe->setUpdateAt(new DateTimeImmutable()) ;
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée');
            return $this->redirectToRoute('recipe.index') ;
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form ,
            'recipe' => $recipe
        ]) ;
    }
    #[Route('/recettes/{id}/delete', name: 'recipe.delete', methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('recipe.index') ;
    }
}

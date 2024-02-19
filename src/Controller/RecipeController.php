<?php

namespace App\Controller;

use App\Entity\Recipe;
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
}

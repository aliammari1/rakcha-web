<?php

namespace App\Controller;

use App\Entity\CategorieProduit;
use App\Form\CategorieProduitType;
use App\Repository\CategorieProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie/produit')]
class CategorieProduitController extends AbstractController
{
    #[Route('/', name: 'app_categorie_produit_index', methods: ['GET'])]
    public function index(CategorieProduitRepository $categorieproduitRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieProduitType::class, new CategorieProduit());
        $updateForms = array();
        for ($i = 0; $i < count($categorieproduitRepository->findAll()); $i++) {
            $updateForms[$i] = $this->createForm(CategorieProduitType::class, $categorieproduitRepository->findAll()[$i])->createView();
        }
        return $this->render('categorie_produit/tables.html.twig', [
            'categorieproduit' => $categorieproduitRepository->findAll(),
            'form' => $form->createView(),
            'updateForms' => $updateForms,
        ]);
    }

    #[Route('/new', name: 'app_categorie_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CategorieProduitRepository $categorieproduitRepository): Response
    {
        $form = $this->createForm(CategorieProduitType::class, new CategorieProduit());
        $updateForms = array();
        for ($i = 0; $i < count($categorieproduitRepository->findAll()); $i++) {
            $updateForms[$i] = $this->createForm(CategorieProduitType::class, $categorieproduitRepository->findAll()[$i])->createView();
        }
        $categorieProduit = new CategorieProduit();
        $form = $this->createForm(CategorieProduitType::class, $categorieProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieProduit);
            $entityManager->flush();
          

            return $this->redirectToRoute('app_categorie_produit_index', [], Response::HTTP_SEE_OTHER);
        }
        $hasErrorsCreate = true;

        return $this->render('categorie_produit/tables.html.twig', [
            'categorieproduit' => $categorieproduitRepository->findAll(),
            'form' => $form->createView(),
            'updateForms' => $updateForms,
            'hasErrorsCreate' => $hasErrorsCreate,
        ]);
    }

    #[Route('/{idCategorie}', name: 'app_categorie_produit_show', methods: ['GET'])]
    public function show(CategorieProduit $categorieProduit): Response
    {
        return $this->render('categorie_produit/show.html.twig', [
            'categorie_produit' => $categorieProduit,
        ]);
    }

    #[Route('/{idCategorie}/edit/{formUpdateNumber}/', name: 'app_categorie_produit_edit', methods: ['GET', 'POST'])]
    public function edit($formUpdateNumber, Request $request, CategorieProduit $categorieProduit, EntityManagerInterface $entityManager, CategorieProduitRepository $categorieproduitRepository): Response
    {
        $form = $this->createForm(CategorieProduitType::class, new CategorieProduit());
        $updateForms = array();
        for ($i = 0; $i < count($categorieproduitRepository->findAll()); $i++) {
            $updateForms[$i] = $this->createForm(CategorieProduitType::class, $categorieproduitRepository->findAll()[$i])->createView();
        }
        $form = $this->createForm(CategorieProduitType::class, $categorieProduit);
        $updateform = $this->createForm(CategorieProduitType::class, $categorieProduit);
        $updateform->handleRequest($request);

        if ($updateform->isSubmitted() && $updateform->isValid()) {
            $entityManager->flush();
       

            return $this->redirectToRoute('app_categorie_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_produit/tables.html.twig', [
            'categorieproduit' => $categorieproduitRepository->findAll(),
            'form' => $form->createView(),
            'updateForms' => $updateForms,
            'updateform' => $updateform->createView(),
            "formUpdateNumber" => $formUpdateNumber,
        ]);
    }

    #[Route('/{idCategorie}', name: 'app_categorie_produit_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieProduit $categorieProduit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorieProduit->getIdCategorie(), $request->request->get('_token'))) {
            $entityManager->remove($categorieProduit);
            $entityManager->flush();
        
        }

        return $this->redirectToRoute('app_categorie_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}

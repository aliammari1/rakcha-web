<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/produit')]
class ProduitController extends AbstractController
{


    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, new Produit());
        $updateForms = array();
        for ($i = 0; $i < count($produitRepository->findAll()); $i++) {
            $updateForms[$i] = $this->createForm(ProduitType::class, $produitRepository->findAll()[$i])->createView();
        }
        return $this->render('produit/tables.html.twig', [
            'produit' =>$produitRepository->findAll(),
            'form' => $form->createView(),
            'updateForms' => $updateForms,
        ]);
    }

    #[Route('/listeproduit', name: 'app_produit_liste', methods: ['GET'])]
    public function liste(ProduitRepository $produitRepository): Response
    {
       return $this->render('produit/about.html.twig', [
          'produit' => $produitRepository->findAll(),
       ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Ceci est nécessaire pour générer un nom de fichier unique basé sur le nom de fichier original.
                $safeFilename = preg_replace('/\s/', '_', $imageFile->getClientOriginalName());
                $safeFilename = strtolower(preg_replace('/[^\w\d.-]/', '', $safeFilename));
                
                $newFilename = 'img/produit/' . $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                
                try {
                    $imageFile->move(
                        $this->getParameter('APP_IMAGE_DIRECTORY'),
                        $newFilename
                    );
                }catch (FileException $e) { }
            $produit->setImage($newFilename);
        }
          

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{idProduit}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{idProduit}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Ceci est nécessaire pour générer un nom de fichier unique basé sur le nom de fichier original.
                $safeFilename = preg_replace('/\s/', '_', $imageFile->getClientOriginalName());
                $safeFilename = strtolower(preg_replace('/[^\w\d.-]/', '', $safeFilename));
                
                $newFilename = 'img/produit/' . $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                
                try {
                    $imageFile->move(
                        $this->getParameter('APP_IMAGE_DIRECTORY'),
                        $newFilename
                    );
                }catch (FileException $e) { }
            $produit->setImage($newFilename);
        }

            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{idProduit}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getIdProduit(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }




    
}

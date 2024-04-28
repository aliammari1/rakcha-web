<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\CommentaireProduit;
use App\Repository\UsersRepository;
use App\Form\CommentaireProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentaireProduitRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commentaire')]
class CommentaireProduitController extends AbstractController
{
    #[Route('/', name: 'app_commentaire_produit_index', methods: ['GET'])]
    public function index(CommentaireProduitRepository $commentaireProduitRepository): Response
    {
        return $this->render('produit/blog.html.twig', [
            'commentaire_produits' => $commentaireProduitRepository->findAll(),
        ]);
    }

    #[Route('/produit/{id}/commentaire/new', name: 'app_commentaire_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UsersRepository $usersRepository, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, $id): Response
    {
        $produit = $produitRepository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
    
        $commentaireProduit = new CommentaireProduit();
        $commentaireProduit->setIdproduit($produit); 
        
        $userId = 1; 
        $user = $usersRepository->findOneBy(['id' => $userId]);
       
        $commentaireProduit->setIdClient($user);
    
        $form = $this->createForm(CommentaireProduitType::class, $commentaireProduit);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            $commentText = $commentaireProduit->getCommentaire();
            
            
            $correctedText = $this->autoCorrect($commentText);
            
            
            $commentaireProduit->setCommentaire($correctedText);
    
            $entityManager->persist($commentaireProduit);
            $entityManager->flush();
    
           
    
           
            return $this->redirectToRoute('app_commentaire_produit_show', ['id' => $id]);
        }
    
       
        return $this->render('produit/blog.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(), 
        ]);
    }

    public function autoCorrect($text): string
    {
        // Initialize cURL
        $curl = curl_init();
        
        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://typewise-ai.p.rapidapi.com/correction/whole_sentence",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'text' => $text,
                'keyboard' => 'QWERTY',
                'languages' => [
                    'en'
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: typewise-ai.p.rapidapi.com",
                "X-RapidAPI-Key: aef1032e49msh9d46f007189dde9p15f3f9jsn879fab779700",
                "content-type: application/json"
            ],
        ]);
        
        // Execute cURL request
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        // Close cURL session
        curl_close($curl);
        
        // Check for cURL errors
        if ($err) {
            // Handle cURL error
            return "cURL Error #:" . $err;
        } else {
            // Decode JSON response into an associative array
            $responseData = json_decode($response, true);
            
            // Check if decoding was successful
            if ($responseData === null) {
                // Handle JSON decoding error
                return "Failed to decode JSON response";
            } else {
                // Check if the response contains 'corrected_text' key
                if (isset($responseData['corrected_text'])) {
                    // Access the corrected text from the decoded response
                    return $responseData['corrected_text'];
                } else {
                    // Handle case where 'corrected_text' key is not present
                    return "No corrected text found in the response";
                }
            }
        }
    }
    
    

    #[Route('/produit/{id}/commentaire', name: 'app_commentaire_produit_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager,Produit $produit, CommentaireProduitRepository $commentaireRepository, Request $request): Response
    {
       
      // Récupérer les commentaires associés au produit
    $commentaires = $commentaireRepository->findBy(['idproduit' => $produit]);

    // Créer un formulaire pour ajouter un nouveau commentaire
    $commentaireProduit = new CommentaireProduit();
    $form = $this->createForm(CommentaireProduitType::class, $commentaireProduit);
    $form->handleRequest($request);

    // Vérifier si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        
        $entityManager->persist($commentaireProduit);
        $entityManager->flush();

        // Rafraîchir la page pour afficher le nouveau commentaire
        return $this->redirectToRoute('app_commentaire_produit_show', ['id' => $produit->getIdproduit()]);
    }

    // Rendez la vue avec les détails du produit et les commentaires
    return $this->render('produit/blog.html.twig', [
        'produit' => $produit,
        'commentaires' => $commentaires,
        'form' => $form->createView(),
    ]);
    }

    

    

    #[Route('/commentaireproduit/{id}/edit', name: 'app_edit_commentaire', methods: ['POST'])]
    public function edit(Request $request, $id, EntityManagerInterface $entityManager, CommentaireProduitRepository $commentaireRepository): Response
    {
                // Récupérer le commentaire à partir de l'identifiant
            $commentaireProduit = $commentaireRepository->find($id);

            // Vérifier si le commentaire existe
            if (!$commentaireProduit) {
                throw $this->createNotFoundException('Commentaire non trouvé');
            }

            // Récupérer les données JSON envoyées depuis la requête
            $data = json_decode($request->getContent(), true);

            if (!isset($data['contenu'])) {
                return $this->json(['error' => 'Le champ "contenu" est manquant dans les données JSON.'], 200);
            }

            // Mettre à jour le contenu du commentaire
            $commentaireProduit->setCommentaire($data['contenu']);

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            
            return $this->json(['message' => 'Commentaire mis à jour avec succès', 'commentaire' => $commentaireProduit]);

    
}
    
#[Route('/commentaireproduit/{id}', name: 'app_delete_commentaire', methods: ['POST'])]
public function deleteCommentaire(Request $request, CommentaireProduit $commentaire,EntityManagerInterface $entityManager): Response
{
    // Vérifier si la requête est une requête AJAX
    if ($request->isXmlHttpRequest()) {
        // Récupérer l'entité CommentaireProduit à partir de l'ID et le supprimer
       
        $entityManager->remove($commentaire);
        $entityManager->flush();

        // Retourner une réponse JSON pour indiquer que la suppression a réussi
        return $this->json(['success' => true]);
    }

    // Si la requête n'est pas AJAX, retourner simplement une réponse vide
    return new Response();
}


}

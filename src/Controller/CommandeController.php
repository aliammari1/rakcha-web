<?php

namespace App\Controller;

use Omnipay\Omnipay;
use App\Entity\Panier;
use App\Entity\Produit;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Entity\Commandeitem;
use App\Repository\UsersRepository;
use App\Repository\CommandeRepository;
use Payum\Core\Request\GetHumanStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }



   
    #[Route('/new', name: 'app_commande_new', methods: ['POST'])]
 
    public function new(SessionInterface $session, Request $request, EntityManagerInterface $entityManager, UsersRepository $usersRepository): Response
    {
        // Vérifier si la requête est une requête POST et si le bouton "Buy Products" a été soumis
        if ($request->isMethod('POST') && $request->request->has('buy_products')) {
            // Créer une nouvelle instance de Commande
            $commande = new Commande();
            
            // Récupérer l'utilisateur client à associer à la commande
            $userId = 1; // ID de l'utilisateur que vous souhaitez attribuer
            $user = $usersRepository->findOneBy(['id' => $userId]);
            
            // Définir les valeurs pour la commande
            $commande->setIdclient($user); 
            $commande->setStatu('en cours'); 
            $commande->setDatecommande(new \DateTime()); 
            
            // Récupérer les données du formulaire
            $formData = $request->request->get('form', []);
            if (!empty($formData)) {
                if (!empty($formData['numTelephone'])) {
                    $commande->setNumTelephone($formData['numTelephone']);
                } else {
                    $this->addFlash('error', 'Le numéro de téléphone est requis.');
                    return $this->redirectToRoute('app_error_page');
                }
                
                if (!empty($formData['adresse'])) {
                    $commande->setAdresse($formData['adresse']); 
                } else {
                    $this->addFlash('error', 'L\'adresse est requise.');
                    return $this->redirectToRoute('app_error_page');
                }
            }
            
            // Récupérer les données du panier
            $panierItems = $entityManager->getRepository(Panier::class)->findBy(['idclient' => $user]);
            
            // Récupérer les produits sélectionnés de la session
            $produitsSelectionnes = $session->get('produits_selectionnes', []);
            dump($panierItems);
         dump($produitsSelectionnes);
            
            // Ajouter chaque produit du panier sélectionné à la commande
            foreach ($panierItems as $panierItem) {
                if (in_array($panierItem->getIdPanier(), $produitsSelectionnes)) {

                    dump($panierItem);
                    // Créer une nouvelle instance de CommandeItem
                    $commandeItem = new CommandeItem();
                    $commandeItem->setIdcommande($commande);
                    $commandeItem->setIdProduit($panierItem->getIdproduit());
                    $commandeItem->setQuantity($panierItem->getQuantite());
    
                    // Appeler persist() sur l'entité CommandeItem
                    $entityManager->persist($commandeItem);
                }
            }
            
            // Persistez la commande après avoir ajouté tous les articles de commande
            $entityManager->persist($commande);
            
            // Enregistrer les modifications dans la base de données
            $entityManager->flush();
    
            // Rediriger l'utilisateur vers une page de confirmation ou toute autre page pertinente
            return $this->redirectToRoute('app_commande_form', ['idcommande' => $commande->getIdcommande()]);
        }
    
        // Si la requête n'est pas une requête POST ou si le bouton "Buy Products" n'a pas été soumis,
        // rediriger l'utilisateur vers une page d'erreur ou toute autre page pertinente
        return $this->redirectToRoute('app_error_page');
    }
    
    
     
    

        #[Route('/order/form', name: 'app_commande_form', methods: ['GET'])]
        public function form(Request $request): Response
        {
            $commande = new Commande();
            $form = $this->createForm(CommandeType::class, $commande);

            return $this->render('commande/addCommande.html.twig', [
                'form' => $form->createView(),
            ]);
        }



    #[Route('/{idcommande}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{idcommande}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{idcommande}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getIdcommande(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }


    
    private $passerelle;
    private $manager;
    
    public function __construct(EntityManagerInterface $manager)
    {
       $this->passerelle = Omnipay::create('PayPal_Rest');
       $this->passerelle->initialize([
           'clientId' => $_ENV['PAYPAL_CLIENT_ID'],
           'secret' => $_ENV['PAYPAL_SECRET_KEY'],
           'testMode' => true,
       ]);
       $this->manager = $manager;
    }

    //Page d'accueil
#[Route('/payment', name: 'app_payment',methods: ['POST'])]
public function payment(Request $request): Response
{
    $token = $request->request->get('token');

    if (!$this->isCsrfTokenValid('myform', $token)) {
        return new Response('Operation non autorisée', Response::HTTP_BAD_REQUEST,
            ['content-type' => 'text/plain']);
    }

    $response = $this->passerelle->purchase([
        'amount' => $request->request->get('amount'),
        'currency' => $_ENV['PAYPAL_CURRENCY'],
        'returnUrl' => 'https://127.0.0.1:8000/success',
        'cancelUrl' => 'https://127.0.0.1:8000/error'
    ])->send();
    
    var_dump($response->getData());

    if ($response->isRedirect()) {
        
        $responseData = $response->getData();
    
        // Vérifier si la clé 'links' existe dans les données de la réponse
        if (isset($responseData['links'])) {
            foreach ($responseData['links'] as $link) {
                // Vérifier si le lien est l'URL de redirection
                if ($link['rel'] === 'approval_url') {
                    // Obtenir l'URL de redirection
                    $redirectUrl = $link['href'];
                    
                    // Rediriger l'utilisateur vers l'URL de redirection
                    return $this->redirect($redirectUrl);
                }
            }
        }
        
        // Si la clé 'approval_url' n'est pas trouvée, gérer l'erreur ici
        return new Response('L\'URL de redirection est introuvable dans les données de réponse.', Response::HTTP_INTERNAL_SERVER_ERROR);
        
    } else {
        // Gérer les erreurs de paiement ici
        return $this->render('commande/error.html.twig', [
            'message' => 'Une erreur est survenue lors du traitement du paiement.'
        ]);
    }
}

 
#[Route('/success', name: 'app_success')]
public function success(Request $request): Response
{
    if ($request->query->get('paymentId') && $request->query->get('PayerID')) {
        $operation = $this->passerelle->completePurchase([
            'payer_id' => $request->query->get('PayerID'),
            'transactionReference' => $request->query->get('paymentId'),
        ]);

        $response = $operation->send();

        if ($response->isSuccessful()) {
            $data = $response->getData();

            // Rediriger l'utilisateur vers la page de succès après un paiement réussi
            return $this->redirectToRoute('app_payment_success', ['transactionId' => $data['id']]);
        } else {
            // Gérer les erreurs de paiement ici
            return $this->render('commande/error.html.twig', [
                'message' => 'Une erreur est survenue lors du traitement du paiement.'
            ]);
        }
    } else {
        // Gérer les paramètres manquants ici
        return $this->render('commande/error.html.twig', [
            'message' => 'Les paramètres de paiement sont manquants.'
        ]);
    }

    
}
#[Route('/payment/success', name: 'app_payment_success')]
public function paymentSuccess(Request $request): Response
{
    // Récupérer les détails de la transaction depuis la session
    $transactionDetails = $request->getSession()->get('transaction_details');

    // Vérifier si les détails de la transaction existent
    if (!$transactionDetails) {
        // Gérer le cas où les détails de la transaction ne sont pas trouvés
        return $this->redirectToRoute('app_error_page');
    }

    // Afficher la page de succès avec les détails de la transaction
    return $this->render('commande/payment_success.html.twig', [
        'transactionDetails' => $transactionDetails,
    ]);
}

 
 
     //Page d'error de la transaction
     #[Route('/error', name: 'app_error')]
     public function error(): Response
     {
         return $this->render('commande/error.html.twig',
               [
                 'message'=>'le paiement a échoué'
               ]
               );
     }

}

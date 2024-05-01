<?php

namespace App\Controller;

use App\Entity\Episodes;
use App\Form\EpisodesType;
use App\Entity\Series;
use App\Entity\Feedback;
use App\Entity\Users; 
use App\Form\FeedbackType;
use Symfony\Component\Security\Core\Security;
use App\Repository\EpisodesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Twilio\Rest\Client;
use Sentiment\Analyzer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;





#[Route('/episodes')]
class EpisodesController extends AbstractController
{
    #[Route('/', name: 'app_episodes_index', methods: ['GET'])]
    public function index(EpisodesRepository $episodesRepository): Response
    {
        flash()->addSuccess("successfully added category");
        $form = $this->createForm(EpisodesType::class, new Episodes());
        $updateForms = array();
        for ($i = 0; $i < count($episodesRepository->findAll()); $i++) {
            $updateForms[$i] = $this->createForm(EpisodesType::class, $episodesRepository->findAll()[$i])->createView();
        }
        return $this->render('episodes/episodesTables.html.twig', [
            'episodes' => $episodesRepository->findAll(),
            'form' => $form->createView(),
            'updateForms' => $updateForms,
        ]);
    }
    #[Route('/listeEpisodes', name: 'app_episodes_liste', methods: ['GET'])]
    public function listeEpisodes(EpisodesRepository $episodesRepository): Response
    {
        flash()->addSuccess("successfully added category");
        $form = $this->createForm(EpisodesType::class, new Episodes());
        $updateForms = array();
        for ($i = 0; $i < count($episodesRepository->findAll()); $i++) {
            $updateForms[$i] = $this->createForm(EpisodesType::class, $episodesRepository->findAll()[$i])->createView();
        }
        return $this->render('episodes/listEpisodes.html.twig', [
            'episodes' => $episodesRepository->findAll(),
            'form' => $form->createView(),
            'updateForms' => $updateForms,
        ]);
    }

    #[Route('/new', name: 'app_episodes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $episode = new Episodes();
        $form = $this->createForm(EpisodesType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                 // Traitement de l'image
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = preg_replace('/\s/', '_', $originalFilename);
            $safeFilename = strtolower(preg_replace('/[^\w\d.-]/', '', $safeFilename));
            $newFilename = 'img/series/' . $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('APP_IMAGE_DIRECTORY'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Gestion de l'exception
            }

            // Enregistrement du chemin de l'image dans l'entité Series
            $episode->setImage($newFilename);
        }
                      //Traitement du video 
          $videoFile = $form->get('video')->getData();
        // Vérifier si un fichier vidéo a été téléchargé
       if ($videoFile) {
    // Générer un nom de fichier unique
       $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
       $safeFilename = preg_replace('/\s/', '_', $originalFilename);
       $safeFilename = strtolower(preg_replace('/[^\w\d.-]/', '', $safeFilename));
       $newFilename = 'img/series/' . $safeFilename . '-' . uniqid() . '.' . $videoFile->guessExtension();

       try {
        // Déplacer le fichier vidéo vers le répertoire de destination
        $videoFile->move(
            $this->getParameter('APP_VIDEO_DIRECTORY'),
            $newFilename
        );
       } catch (FileException $e) {
        // Gérer l'exception si le déplacement du fichier échoue
      }

    // Enregistrer le chemin du fichier vidéo dans l'entité Episodes
    $episode->setVideo($newFilename);
}
              
            $entityManager->persist($episode);
            $entityManager->flush();

            return $this->redirectToRoute('app_episodes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episodes/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{idepisode}', name: 'app_episodes_show', methods: ['GET'])]
    public function show(Episodes $episode): Response
    {
        return $this->render('episodes/show.html.twig', [
            'episode' => $episode,
        ]);
    }


    #[Route('/{idepisode}/edit', name: 'app_episodes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Episodes $episode, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EpisodesType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement de l'image
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = preg_replace('/\s/', '_', $originalFilename);
            $safeFilename = strtolower(preg_replace('/[^\w\d.-]/', '', $safeFilename));
            $newFilename = 'img/series/' . $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('APP_IMAGE_DIRECTORY'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Gestion de l'exception
            }

            // Enregistrement du chemin de l'image dans l'entité Series
            $episode->setImage($newFilename);
        }
              //Traitement du video 
              $videoFile = $form->get('video')->getData();
              // Vérifier si un fichier vidéo a été téléchargé
             if ($videoFile) {
          // Générer un nom de fichier unique
             $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
             $safeFilename = preg_replace('/\s/', '_', $originalFilename);
             $safeFilename = strtolower(preg_replace('/[^\w\d.-]/', '', $safeFilename));
             $newFilename = 'img/series/' . $safeFilename . '-' . uniqid() . '.' . $videoFile->guessExtension();
      
             try {
              // Déplacer le fichier vidéo vers le répertoire de destination
              $videoFile->move(
                  $this->getParameter('APP_VIDEO_DIRECTORY'),
                  $newFilename
              );
             } catch (FileException $e) {
              // Gérer l'exception si le déplacement du fichier échoue
            }
      
          // Enregistrer le chemin du fichier vidéo dans l'entité Episodes
          $episode->setVideo($newFilename);
      }
        
            $entityManager->flush();
            return $this->redirectToRoute('app_episodes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episodes/edit.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{idepisode}', name: 'app_episodes_delete', methods: ['POST'])]
    public function delete(Request $request, Episodes $episode, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getIdepisode(), $request->request->get('_token'))) {
            $entityManager->remove($episode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_episodes_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/series/{idserie}/episodes', name: 'app_episodes_showw')]
    public function showBySerie(int $idserie, EpisodesRepository $episodesRepository): Response
    {
        // Récupérer les épisodes de la série spécifiée
        $episodes = $episodesRepository->findBy(['idserie' => $idserie]);
    
        // Passer les données à la vue Twig pour l'affichage
        return $this->render('episodes/listEpisodes.html.twig', [
            'episodes' => $episodes,
        ]);
    }
      


    #[Route('/episodes/{idEpisode}/watch', name: 'app_episode_watch')]
    public function watch(Request $request, int $idEpisode, Security $security,SessionInterface $session): Response
    {
        // Récupérer les données de l'épisode depuis la base de données
        $episode = $this->getDoctrine()->getRepository(Episodes::class)->find($idEpisode);
        // Vérifier si l'épisode existe
         if (!$episode) {
        throw $this->createNotFoundException('Episode not found');
         }

         // Récupérer un utilisateur spécifique de la base de données
         $idUser = 1; // Remplacez 1 par l'ID de l'utilisateur que vous souhaitez récupérer
        $user = $this->getDoctrine()->getRepository(Users::class)->find($idUser);
        $photoDeProfil = $user->getPhotoDeProfil();

         // Récupérer les feedbacks associés à l'épisode depuis la base de données
         $feedbacks = $this->getDoctrine()->getRepository(Feedback::class)->findBy(['idEpisode' => $idEpisode]);
    
        // Récupérer l'utilisateur actuellement connecté
       /*
        $user = $security->getUser();
    */
        // Créer un nouveau formulaire de feedback
        $feedback = new Feedback();
        $feedbackForm = $this->createForm(FeedbackType::class, $feedback);
        // Gérer la soumission du formulaire
        $feedbackForm->handleRequest($request);
        if ($feedbackForm->isSubmitted() && $feedbackForm->isValid()) {
            // Récupérer la description saisie par l'utilisateur à partir du formulaire
            $description = $feedbackForm->getData()->getDescription();
            // Associer l'épisode au feedback
            $feedback->setIdEpisode($idEpisode); 
    
            // Associer l'utilisateur au feedback
            $feedback->setIdUser($user->getId()); 
            // Définir la description saisie par l'utilisateur dans l'objet Feedback
             $feedback->setDescription($description);  
                   // Analyse de sentiment avec php-sentiment-analyzer
            $commentText =  $feedback->getDescription(); // Supposons que vous avez une méthode getComment() dans votre entité Commentairecinema
            $sentiment = $this->analyseSentiment($commentText);
    
            // Le sentiment est retourné sous forme de chaîne ('positive', 'negative', 'neutral')
            // Vous pouvez utiliser directement cette valeur comme sentiment du commentaire
            $feedback->setSentiment($sentiment);
    
            // Enregistrer le feedback en base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($feedback);
                
             /*     
            // Envoi du SMS après l'ajout de la série
          $twilioSid = "ACd3d2094ef7f546619e892605940f1631";
          $twilioToken = "8d56f8a04d84ff2393de4ea888f677a1";
          $twilioPhoneNumber = "+17573640849";
          $phoneNumber = '+21653775010'; // Remplacez par le numéro de téléphone réel de votre base de données

        try {
            $client = new Client($twilioSid, $twilioToken);
            $client->messages->create(
                $phoneNumber,
                [
                    'from' => $twilioPhoneNumber,
                    'body' => 'Thank You for Your Feedback'
                ]
            );
        } catch (\Exception $e) {
            // Gérer l'exception ici
            $errorMessage = $e->getMessage();
            $this->addFlash('error', 'Erreur lors de l\'envoi du SMS : ' . $errorMessage);
        }
        
        */
            $entityManager->flush();

            // Vérifier si le sentiment est négatif
        if ($sentiment == 'neu') {
            // Le sentiment est négatif, envoyer un SMS à l'utilisateur pour demander pourquoi il n'a pas aimé l'épisode
            $twilioSid = "ACb62dae18a1cdf503d09534ba7f13db8d";
            $twilioToken = "3763cdf1b024cff8330fab6501d95d75";
            $twilioPhoneNumber = "+13347218426";
            $phoneNumber = '+21653775010'; // Remplacez par le numéro de téléphone réel de votre base de données
            try {
                $client = new Client($twilioSid, $twilioToken);
                $client->messages->create(
                    $phoneNumber,
                    [
                        'from' => $twilioPhoneNumber,
                        'body' => 'Bonjour, nous avons remarqué que vous avez donné un feedback négatif pour l\'épisode. Pourriez-vous nous dire pourquoi ?'
                    ]
                );
            } catch (\Exception $e) {
                // Gérer l'exception ici
                $errorMessage = $e->getMessage();
                $this->addFlash('error', 'Erreur lors de l\'envoi du SMS : ' . $errorMessage);
            }
        }
             // Message flash pour informer l'utilisateur que le sentiment a été traité
            $session->getFlashBag()->add('success', 'Le sentiment du feedback a été traité avec succès.');
            // Rediriger l'utilisateur vers la même page pour éviter la soumission multiple du formulaire
            return $this->redirectToRoute('app_episode_watch', ['idEpisode' => $idEpisode]);
        }

           
        // Passer les feedbacks et le formulaire de feedback au template Twig
        return $this->render('front/watchepisode.html.twig', [
            'episode' => $episode,
            'feedbacks' => $feedbacks,
            'user' => $user, // Ajoutez cette ligne pour passer l'utilisateur au modèle Twig
            'photoDeProfil' => $photoDeProfil, // Ajoutez cette ligne pour passer l'URL de la photo de profil au modèle Twig
            'feedbackForm' => $feedbackForm->createView(), 
        ]);
    }
    

    private function analyseSentiment(string $comment): string
{
    $analyzer = new Analyzer();
    $sentiment = $analyzer->getSentiment($comment);

    // Récupérer la catégorie de sentiment avec le score le plus élevé
    $maxSentiment = array_keys($sentiment, max($sentiment))[0];

    // Retourner la catégorie de sentiment
    return $maxSentiment;
}














    
}



  

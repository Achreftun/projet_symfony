<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\LigneCommande;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommandeController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session): Response
    {
        $lignes = $session->get('lignes');
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
            'lignes' => $lignes
        ]);
    }
    #[Route('/panier/{id}', name: 'app_panier_add')]
    public function show(Article $article, SessionInterface $session): Response
    {
        $lignes = $session->get('lignes');
        if ($lignes) {
            $lignes = array();
        }
        $ligne = new LigneCommande();
        $ligne->setArticle($article);
        $ligne->setQuantiteCommandee(1);
        $lignes[] = $ligne;
        $session->set('lignes', $lignes);
        return $this->redirectToRoute('app_panier');
    }
}

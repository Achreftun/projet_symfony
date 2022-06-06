<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        $found = false;
        if (!$lignes) {
            $ligne = new LigneCommande();
            $ligne->setArticle($article);
            $ligne->setQuantiteCommandee(1);
            $lignes[] = $ligne;
        } else {
            foreach ($lignes as $l) {
                if ($l->getArticle()->getId() == $article->getId()) {
                    $l->setQuantiteCommandee($l->getQuantiteCommandee() + 1);
                    $found = true;
                }
            }
            if (!$found) {
                $ligne = new LigneCommande();
                $ligne->setArticle($article);
                $ligne->setQuantiteCommandee(1);
                $lignes[] = $ligne;
            }
        }
        $session->set('lignes', $lignes);
        return $this->redirectToRoute('app_panier');
    }
    #[Route('/panier/{id}/supprimer', name: 'app_panier_delete')]
    public function delete(Article $article, SessionInterface $session): Response
    {
        $lignes = $session->get('lignes');
        foreach ($lignes as $key => $l) {
            if ($l->getArticle()->getId() == $article->getId()) {
                unset($lignes[$key]);
            }
        }
        $session->set('lignes', $lignes);
        return $this->redirectToRoute('app_panier');
    }
    #[Route('/commande', name: 'app_commande_validate')]
    public function validate(EntityManagerInterface $em, SessionInterface $session): Response
    {
        $lignes = $session->get('lignes');
        $commande = new Commande();
        $dateTimeZone = new \DateTimeZone('Europe/Paris');
        $commande->setDateCommande(new \DateTime('now', $dateTimeZone));
        foreach ($lignes as $l) {
            $article = $em->getRepository(Article::class)->find($l->getArticle()->getId());
            $article->setQuantite($article->getQuantite() - $l->getQuantiteCommandee());
            $l->setArticle($article);
            $commande->addLigneCommande($l);
        }
        $em->persist($commande);
        $em->flush();
        $session->clear();
        return $this->redirectToRoute('app_commande_show');
    }
    #[Route('/commande/show', name: 'app_commande_show')]
    public function showCommande(EntityManagerInterface $em, SessionInterface $session): Response
    {
        $commandes = $em->getRepository(Commande::class)->findAll();
        return $this->render('commande/show.html.twig', [
            'controller_name' => 'CommandeController',
            'commandes' => $commandes
        ]);
    }
}

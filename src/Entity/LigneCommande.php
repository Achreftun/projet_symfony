<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $quantiteCommandee;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'ligneCommandes', cascade: ['persist'])]
    private $article;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'ligneCommandes', cascade: ['persist'])]
    private $commande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiteCommandee(): ?int
    {
        return $this->quantiteCommandee;
    }

    public function setQuantiteCommandee(?int $quantiteCommandee): self
    {
        $this->quantiteCommandee = $quantiteCommandee;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
}

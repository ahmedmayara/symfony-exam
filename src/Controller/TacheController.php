<?php

namespace App\Controller;

use App\Entity\PropertySearch;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tache;
use App\Form\PropertySearchType;
use App\Form\TacheType;

class TacheController extends AbstractController
{
    #[Route('/taches', name: 'app_tache')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $propertySearch = new PropertySearch();

        $form = $this->createForm(PropertySearchType::class, $propertySearch);

        $form->handleRequest($request);

        $taches = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $titre = $propertySearch->getTitre();
            if ($titre != "") {
                $taches = $doctrine->getRepository(Tache::class)->findBy(['titre' => $titre]);
            } else {
                $taches = $doctrine->getRepository(Tache::class)->findAll();
            }
        } else {
            $taches = $doctrine->getRepository(Tache::class)->findAll();
        }

        return $this->render('tache/index.html.twig', [
            'taches' => $taches,
            'form' => $form->createView()
        ]);
    }

    #[Route('/taches/ajouter', name: 'app_tache_ajouter')]
    public function ajouter(ManagerRegistry $doctrine, Request $request): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->persist($tache);
            $doctrine->getManager()->flush();
            return $this->redirectToRoute('app_tache');
        }
        return $this->render('tache/ajouter.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/taches/modifier/{id}', name: 'app_tache_modifier')]
    public function modifier(ManagerRegistry $doctrine, Request $request, Tache $tache): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();
            return $this->redirectToRoute('app_tache');
        }
        return $this->render('tache/modifier.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/taches/supprimer/{id}', name: 'app_tache_supprimer')]
    public function supprimer(ManagerRegistry $doctrine, Tache $tache): Response
    {
        $doctrine->getManager()->remove($tache);
        $doctrine->getManager()->flush();
        return $this->redirectToRoute('app_tache');
    }

    #[Route('/taches/{id}', name: 'app_tache_afficher')]
    public function afficher(ManagerRegistry $doctrine, Tache $tache): Response
    {
        return $this->render('tache/afficher.html.twig', [
            'tache' => $tache
        ]);
    }
}

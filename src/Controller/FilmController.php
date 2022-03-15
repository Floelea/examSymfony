<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\FilmType;
use App\Form\ImpressionType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    #[Route('/film', name: 'film')]
    public function index(FilmRepository $repo): Response
    {
        $films = $repo->findAll();
        return $this->render('film/index.html.twig', [
            'films' => $films
        ]);
    }

    /**
     * @Route("/film/{id}",name="show")
     */
    public function show(Film $film){

        $impression = new Impression();
        $form = $this->createForm(ImpressionType::class,$impression);
        return $this->renderForm('film/show.html.twig',[
            'film'=>$film,
            'formImpression'=>$form
        ]);
    }

    /**
     * @Route("/film/delete/{id}",name="delete_film")
     */
    public function delete(Film $film, EntityManagerInterface $manager){
        if ($film && $film->getUser() == $this->getUser()){
            $manager->remove($film);
            $manager->flush();
        }
        return $this->redirectToRoute('film');
    }

    /**
     * @Route("/film/new",name="new_film",priority="2")
     */
    public function new(Request $request,EntityManagerInterface $manager){

        $film = new Film();
        $form = $this->createForm(FilmType::class,$film);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $film->setCreatedAt(new \DateTime());
            $film->setUser($this->getUser());
            $manager->persist($film);
            $manager->flush();
            return $this->redirectToRoute('film');
        }
        return $this->renderForm('film/new.html.twig',[
            'form'=>$form
        ]);
    }

    /**
     * @Route("/film/edit/{id}",name="edit_film")
     */
    public function edit(Request $request,Film $film,EntityManagerInterface $manager){

        if ($film->getUser() == $this->getUser()) {
            $form = $this->createForm(FilmType::class, $film);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $manager->persist($film);
                $manager->flush();
                return $this->redirectToRoute('film');
            }
            return $this->renderForm('film/edit.html.twig', [
                'formulaireEdit' => $form
            ]);
        }
        return $this->redirectToRoute('film');
    }
}

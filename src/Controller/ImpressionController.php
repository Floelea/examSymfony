<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\ImpressionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImpressionController extends AbstractController
{
    #[Route('/impression', name: 'app_impression')]
    public function index(): Response
    {
        return $this->render('impression/index.html.twig', [
            'controller_name' => 'ImpressionController',
        ]);
    }

    /**
     * @Route("/impression/delete/{id}",name="delete_impression")
     */
    public function delete(Impression $impression, EntityManagerInterface $manager){

        if ($impression && $impression->getUser() == $this->getUser()){
            $manager->remove($impression);
            $manager->flush();
        }
        return $this->redirectToRoute("show",['id'=>$impression->getFilm()->getId()]);
    }

    /**
     * @Route("/impression/new/{id}",name="new_impression")
     */
    public function new(Request $request, Film $film, EntityManagerInterface $manager)
    {
        $impression = new Impression();
        $form = $this->createForm(ImpressionType::class, $impression);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $impression->setFilm($film);
            $impression->setCreatedAt(new \DateTime());
            $impression->setUser($this->getUser());
            $manager->persist($impression);
            $manager->flush();
            return $this->redirectToRoute('show', ['id'=>$film->getId()]);
        }
    }

    /**
     * @Route("/impression/edit/{id}",name="edit_impression")
     */
    public function edit(Impression $impression, Request $request,EntityManagerInterface $manager){

        if ($impression->getUser() == $this->getUser()) {
            $form = $this->createForm(ImpressionType::class, $impression);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($impression);
                $manager->flush();
                return $this->redirectToRoute('show', ['id' => $impression->getFilm()->getId()]);
            }
            return $this->renderForm('impression/edit.html.twig', [
                'formulaireEdit' => $form
            ]);
        }
        return $this->redirectToRoute('film',['id'=>$impression->getFilm()->getId()]);
    }



}

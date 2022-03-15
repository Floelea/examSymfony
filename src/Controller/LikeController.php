<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Entity\Like;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/film/like/{id}', name: 'like')]
    public function index(Film $film,EntityManagerInterface $manager,LikeRepository $repo): Response
    {
        $like = $repo->findOneBy([
            'user'=>$this->getUser(),
            'film'=>$film
        ]);

        if(!$like){
            $like = new Like();
            $like->setUser($this->getUser());
            $like->setFilm($film);
            $manager->persist($like);
            $liked = true;
        }else{
            $manager->remove($like);
            $liked = false;
        }

        $manager->flush();
        $count = $repo->count(['film' => $film]);
        $reponse = [
            'liked'=>$liked,
            'count'=>$count
        ];

        return $this->json($reponse, 200);

        return $this->redirectToRoute('film',['id'=>$film->getId()]);
    }

    #[Route('/impression/like/{id}', name: 'imp_like')]
    public function likeImp(Impression $impression,EntityManagerInterface $manager,LikeRepository $repo): Response
    {
        $like = $repo->findOneBy([
            'user'=>$this->getUser(),
            'impression'=>$impression
        ]);

        if(!$like){
            $like = new Like();
            $like->setUser($this->getUser());
            $like->setImpression($impression);
            $manager->persist($like);
            $liked = true;
        }else{
            $manager->remove($like);
            $liked = false;
        }

        $manager->flush();
        $count = $repo->count(['impression' => $impression]);
        $reponse = [
            'liked'=>$liked,
            'count'=>$count
        ];

        return $this->json($reponse, 200);

        return $this->redirectToRoute('film',['id'=>$film->getId()]);
    }


}

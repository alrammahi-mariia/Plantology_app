<?php

namespace App\Controller;

use App\Entity\Favourite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Plants;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'api_main')]
class AppController extends AbstractController
{
    #[Route('/plants', name: 'plant_list', methods:['GET'])]
    public function index(EntityManagerInterface $em): Response
    {   
        $plants = $em->getRepository(Plants::class)->findAll();
        $data = [];
        $counter = 0;
        foreach ($plants as $plant) {
            $data[$counter++] = [
                'id' => $plant->getId(),
                'name' => $plant->getName(),
                'name_2' => $plant->getName2(),
                'img' => $plant->getImg(),
                'water' => $plant->getWater(),
                'conditions' => $plant->getConditions(),
                'difficulty' => $plant->getDifficulty()
            ];
        }
        return $this->json($data);
    }
    #[Route('/plants/{id}', name: 'plant_show', methods: ['GET'])]
    public function show(int $id, ManagerRegistry $doctrine): Response
    {
        $plant = $doctrine->getRepository(Plants::class)->find($id);

        if (!$plant) {
            return $this->json('No plant found for id' . $id, 404);
        }

        $data = [
            'id' => $plant->getId(),
            'name' => $plant->getName(),
            'name_2' => $plant->getName2(),
            'img' => $plant->getImg(),
            'water' => $plant->getWater(),
            'conditions' => $plant->getConditions(),
            'difficulty' => $plant->getDifficulty(),
        ];

        return $this->json($data);
    }

    #[Route('/plants/{id}/add', name: 'add_favourite', methods: ['GET','POST'])] 
        
        public function new(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $item = $entityManager->getRepository(Plants::class)->find($id);
        if (!$item) {
            return $this->json('No plant found for id ' .$id, 404);
        }
        $favourite = new Favourite();
        $favourite->setName($request->request->get('name'));
        $favourite->setImg($request->request->get('img'));
     
       
        $entityManager->persist($favourite);
        $entityManager->flush();

        return $this->json('Saved a new plant with id ' . $favourite->getId());

    }
}   
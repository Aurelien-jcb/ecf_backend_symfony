<?php

namespace App\Service;

use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class Random
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em, ProjectRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    public function getProjects()
    {
        return $this->repository->findAll();
    }

    public function getInt(int $min = 0, int $max = 100): int
    {
        return random_int($min, $max);
    }
}

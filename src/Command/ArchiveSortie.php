<?php

namespace App\Command;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArchiveSortie extends \Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'app:archive-sortie';

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input,
                               OutputInterface $output,
                               SortieRepository $sortieRepository,
                               EntityManagerInterface $entityManager,
                               EtatRepository $etatRepository
    ): int
    {
        $sorties = $sortieRepository ->findAll();

        foreach ($sorties as $sortie)
        {
            $now = new \DateTime("-1 month");
            if ($sortie->getDateHeureDebut()<$now){
                $etatPasse=$etatRepository->searchEtatPassee();
                $sortie->setEtat(array_values($etatPasse)[0]);
                $sortie->setEtat();
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

         }
        echo($now);
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }

}
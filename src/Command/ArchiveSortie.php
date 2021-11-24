<?php

namespace App\Command;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArchiveSortie extends \Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'app:archive-sortie';

    protected function configure(): void
    {

    }

    
}
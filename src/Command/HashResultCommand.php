<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\ORM\EntityManagerInterface;

class HashResultCommand extends Command
{
  //---------------------------------------------------------------------------------------

  protected static $defaultName = 'hash_result:generate';
  private $em;

  //---------------------------------------------------------------------------------------

  public function __construct(EntityManagerInterface $em)
  {
      parent::__construct();
      $this->em = $em;
  }

  //---------------------------------------------------------------------------------------

  protected function configure()
  {
    $this->addArgument('input_string', InputArgument::REQUIRED)
         ->addArgument('requests', InputArgument::REQUIRED);
  }

  //---------------------------------------------------------------------------------------

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $string   = $input->getArgument('input_string');
    $requests = intval($input->getArgument('requests'));
    $batch = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));

    for($requisitionIndex = 1; $requisitionIndex <= $requests; $requisitionIndex++)
    {
      $dataArray = \App\Controller\HashResultController::FindHash($string);
      \App\Controller\HashResultController::Save($this->em, $batch, $string, $dataArray["key"], $dataArray["hash"], $dataArray["numberOfTries"], $requisitionIndex);

      $output->writeln("#" . $requisitionIndex);
      $output->writeln("Input: " . $string);
      $output->writeln("hash: " . $dataArray["hash"]);
      $output->writeln("Key: " . $dataArray["key"]);
      $output->writeln("Attempts: " . $dataArray["numberOfTries"]);
      $output->writeln("-------------------------------------");

      $string = $dataArray["hash"];
    }

    $output->writeln("Requests: " . $requests);

    return Command::SUCCESS;
  }

  //---------------------------------------------------------------------------------------
}
<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for updating reoccuring debts
 */
class ReoccurringDebtsCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:reoccurringDebts')
            ->setDescription('Creates or updates reoccurring dsebts.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $model = $this->getHelper('container')->getByType('Models\ReoccurringDebtsModel');

        try {
            $output->writeLn('Tetst');
            return 0; // zero return code means everything is ok

        } catch (Exception $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');
            return 1; // non-zero return code means error

        }
    }
}

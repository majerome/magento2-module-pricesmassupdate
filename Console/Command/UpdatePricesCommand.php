<?php

declare(strict_types=1);

namespace Majerome\PricesMassUpdate\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Majerome\PricesMassUpdate\Model\CleanupManager;
use Majerome\PricesMassUpdate\Model\IndexerManager;
use Majerome\PricesMassUpdate\Model\PriceUpdater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class UpdatePricesCommand extends Command
{
    public function __construct(
        private readonly PriceUpdater   $priceUpdater,
        private readonly State          $state,
        private readonly IndexerManager $indexerManager,
        private readonly CleanupManager $cleanupManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('majerome:mass-update:prices')
            ->setDescription('Update all product prices in bulk with the same target value');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $translations = $this->getTranslations('EN');
        $helper = $this->getHelper('question');

        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);

            $languageQuestion = new ChoiceQuestion(
                'Enter EN or FR',
                ['EN' => 'In english please!', 'FR' => 'En français s\'il vous plait !'],
                'EN'
            );

            $languageQuestion->setErrorMessage("Enter EN or FR");
            $languageQuestion->setMaxAttempts(3);
            $language = $helper->ask($input, $output, $languageQuestion);
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $maxAttemptsReachedMessage = $translations['max_attempts'];
            $output->writeln('<error>' . $maxAttemptsReachedMessage . '</error>');
            return Cli::RETURN_FAILURE;
        }

        try {
            $translations = $this->getTranslations($language);
            $questionText = (string)$translations['price_question'];
            $question = new Question($questionText);
            // Add validation to ensure the input is a positive integer or float with up to two decimal places
            $question->setValidator(function ($answer) use ($translations) {
                if (!is_numeric($answer) || $answer <= 0) {
                    throw new InvalidArgumentException($translations['not_decimal_error']);
                }
                if (!preg_match('/^\d+(\.\d{1,2}(0+)?)?$/', $answer)) {
                    throw new InvalidArgumentException($translations['too_much_decimals_error']);
                }
                return (float)$answer;
            });
            $question->setMaxAttempts(3);
            // Get the target price from user input
            $targetPrice = $helper->ask($input, $output, $question);
            // Backup indexers state
            $this->indexerManager->backupIndexerStates();
            $info = $translations['indexers_state_saved'];
            $output->writeln("<info>$info</info>");
            // Disable indexers
            $this->indexerManager->disableIndexers();
            $info = $translations['indexers_disabled'];
            $output->writeln("<info>$info</info>");
            // Update prices
            $this->priceUpdater->updatePrices((float)$targetPrice);
            $info = $translations['prices_updated'];
            $output->writeln("<info>$info</info>");
            // Reindex
            $this->indexerManager->reindexAll();
            $info = $translations['reindexing_complete'];
            $output->writeln("<info>$info</info>");
            // Restore indexers state
            $this->indexerManager->restoreIndexerStates();
            $info = $translations['indexers_state_restored'];
            $output->writeln("<info>$info</info>");
            // Clear cache
            $this->cleanupManager->clearCache();
            $info = $translations['cache_cleared'];
            $output->writeln("<info>$info</info>");
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $maxAttemptsReachedMessage = $translations['max_attempts'];
            $output->writeln('<error>' . $maxAttemptsReachedMessage . '</error>');
            return Cli::RETURN_FAILURE;
        }
        return Cli::RETURN_SUCCESS;
    }

    private function getTranslations(string $language): array
    {
        $translationsDictionnary = [
            'price_question' => [
                'EN' => 'Please enter the target price: ',
                'FR' => 'Saisissez le prix cible svp : ',
            ],
            'not_decimal_error' => [
                'EN' => 'Enter a positive integer or decimal number, up to 2 decimal places, with the decimal point.',
                'FR' => 'Saisissez un nombre positif entier ou décimal, 2 décimales maximum, avec le point décimal.',
            ],
            'too_much_decimals_error' => [
                'EN' => 'The target price must have up to two decimal places.',
                'FR' => 'Le prix cible ne peut comporter que 2 décimales maximimum.',
            ],
            'indexers_state_saved' => [
                'EN' => 'Indexers state saved.',
                'FR' => 'État des indexers sauvegardé.',
            ],
            'indexers_disabled' => [
                'EN' => 'Indexers disabled.',
                'FR' => 'Indexers désactivés.',
            ],
            'prices_updated' => [
                'EN' => 'Prices updated successfully.',
                'FR' => 'Mise à jour des prix effectuée.',
            ],
            'reindexing_complete' => [
                'EN' => 'Reindexing complete.',
                'FR' => 'Réindexation terminée.',
            ],
            'indexers_state_restored' => [
                'EN' => 'Indexers state restored.',
                'FR' => 'État des indexers restauré.',
            ],
            'cache_cleared' => [
                'EN' => 'Cache cleared.',
                'FR' => 'Cache nettoyé.',
            ],
            'max_attempts' => [
                'EN' => 'You have reached the maximum number of attempts. Exiting.',
                'FR' => 'Vous avez atteint le nombre maximum de tentatives autorisées. Sortie.',
            ],
        ];
        return array_map(fn ($entry) => $entry[$language], $translationsDictionnary);
    }
}

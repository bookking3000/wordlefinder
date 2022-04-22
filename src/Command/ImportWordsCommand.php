<?php

namespace App\Command;

use App\Encoder;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportWordsCommand extends Command
{
    protected static $defaultName = 'import:words';
    protected static $defaultDescription = 'Imports Words into mySQL';

    protected EntityManagerInterface $entityManager;
    protected array $errors;
    private Encoder $encoder;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct(self::$defaultName);
        $this->entityManager = $entityManager;
        $this->encoder = new Encoder($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->info("Starting...");
            $this->insertFromFile($io);
        } catch (Exception $e) {
            $io->error($e->getMessage());
        }

        $io->success('Import ended.');
        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    protected function insertFromFile(SymfonyStyle $io)
    {
        $lines = 1908815;
        $filename = __DIR__ . '\..\..\resources\wordlist-german.txt';
        $fileStream = fopen($filename, 'r');

        $io->progressStart($lines);
        while (($word = fgets($fileStream)) !== false) {

            $word = $this->getWordUtf8Encoded($word);

            $this->insertWord($word);
            $io->progressAdvance();
        }

        fclose($fileStream);
        $io->progressFinish();
    }

    /**
     * @throws Exception
     */
    protected function insertWord($word)
    {
        $sql = "INSERT INTO word (word, length, created_at ) VALUES (:word, :length, :created) ON DUPLICATE KEY UPDATE created_at = :created ;";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $result = $stmt->executeQuery([
            'word' => $word,
            'length' => mb_strlen($word),
            'created' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);

        if ($result->rowCount() != 1)
            $this->errors[] = $word;
    }

    /**
     * @param $word
     * @return array|false|string|string[]|null
     */
    protected function getWordUtf8Encoded($word)
    {
        return $this->encoder->getWordUtf8Encoded($word);
    }

}

<?php

namespace Src;

use Src\ResultsContainer;

class Response
{
    private const RESULTS_FILE_NAME = 'test-results';

    private int $startTime;

    public function setStartTime(int $startTime): Response
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function handle()
    {
        switch (RESULT_TYPE) {
            case 'json':
                $this->asJson();
                break;
            case 'xml':
                $this->asXml();
                break;
            default:
                $this->printInTerminal();
        }
    }
    
    /**
     * Print results in terminal
     */
    public function printInTerminal(): void
    {
        echo PHP_EOL;
        foreach (ResultsContainer::getResults() as $result) {
            echo '====================' . PHP_EOL;
            echo $result . PHP_EOL;
        }
        echo '====================' . PHP_EOL;
        echo PHP_EOL;
        echo "\033[0;34m Time taken: " . (time() - $this->startTime) . " seconds \033[0m" . PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * Save results as JSON file
     */
    public function asJson(): void
    {
        // save as json file in parent directory
        file_put_contents(
            __DIR__ . '/../' . self::RESULTS_FILE_NAME . '.json',
            json_encode(ResultsContainer::getResults(), JSON_PRETTY_PRINT)
        );
    }

    /**
     * Save results as XML file
     */
    public function asXml(): void
    {
        $xml = new \SimpleXMLElement('<results/>');
        foreach (ResultsContainer::getResults() as $result) {
            $test = $xml->addChild('test');
            $test->addChild('status', $result['status']);
            $test->addChild('test_class', $result['test_class']);
            $test->addChild('test_method', $result['test_method']);
            if (isset($result['error_type'])) {
                $test->addChild('error_type', $result['error_type']);
            }
            if (isset($result['error_message'])) {
                $test->addChild('error_message', $result['error_message']);
            }
            if (isset($result['trace'])) {
                $test->addChild('trace', $result['trace']);
            }
        }
        $xml->asXML(__DIR__ . '/../' . self::RESULTS_FILE_NAME . '.xml');
    }
}
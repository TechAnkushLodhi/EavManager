<?php

namespace Icecube\EavManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class CommandManager extends AbstractHelper
{
    protected $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    public function executeCommands()
    {
        try {
            // Ensure correct Magento area is set
            try {
                $this->state->getAreaCode();
            } catch (LocalizedException $e) {
                $this->state->setAreaCode('adminhtml'); // Set to admin area
            }

            // Allowed Commands
            $commands = ['cron:run', 'indexer:reindex', 'cache:flush'];
            $outputResults = [];

            foreach ($commands as $command) {
                $fullCommand = 'php ' . BP . '/bin/magento ' . $command . ' 2>&1';
                $output = shell_exec($fullCommand);
                $outputResults[$command] = $output;
            }

            return $outputResults;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

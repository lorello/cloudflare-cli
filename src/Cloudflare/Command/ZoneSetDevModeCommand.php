<?php

namespace Cloudflare\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;

class ZoneSetDevModeCommand extends ContainerAwareCommand
{
    public function __construct($app, $name = null)
    {
        parent::__construct($app, $name);
        $this->setDescription('Toggle dev mode On or Off');
        $this->setHelp('This function allows you to toggle Development Mode on or off for a particular domain. When Development Mode is on the cache is bypassed. Development mode remains on for 3 hours or until when it is toggled back off.');
        $this->addArgument('domain', Console\Input\InputArgument::REQUIRED, 'The domain you want to change DevMode');
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $domain = $input->getArgument('domain');

        $data = $this->app['guzzle']->getData('cf');

        $mode = 1;
        $modeText = 'On';
        if ($this->getName() == 'dev:off') {
            $mode = 0;
            $modeText = 'Off';
        }

        $commandParams = [
            'u'   => $this->app['cf.user'],
            'tkn' => $this->app['cf.token'],
            'a'   => 'devmode',
            'z'   => $domain,
            'v'   => $mode,
        ];

        $response = $this->app['guzzle']['cf']->CachePurge($commandParams);

        if ($response['result'] == 'error') {
            $output->writeln("\n<error>Error setting DevMode on domain $domain:\n\t$response[msg]</error>\n");
        } else {
            $output->writeln("\n<info>Successfuly switched DevMode to '$modeText' for domain $domain</info>\n");
        }
        if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
            $output->writeln(var_dump($response->toArray()));
        }
    }
}

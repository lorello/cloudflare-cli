<?php
namespace Cloudflare\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

class PurgeCacheCommand extends ContainerAwareCommand
{

    public function __construct($app, $name = null)
    {
        parent::__construct($app, $name);
        $this->setDescription('Purge cache of a specific domain');
        $this->setHelp('Purge cache of the speficied domain');
        $this->addArgument('domain', Console\Input\InputArgument::REQUIRED, 'The domain to purge cache');
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $domain = $input->getArgument('domain');

        $data = $this->app['guzzle']->getData('cf');

        $commandParams = array(
            'u'   => $this->app['cf.user'],
            'tkn' => $this->app['cf.token'],
            'a'   => 'fpurge_ts',
            'z'   => $domain,
            'v'   => 1
        );

        $response = $this->app['guzzle']['cf']->CachePurge($commandParams);

        if ($response['result'] == 'error') {
            $output->writeln("\n<error>Error purging domain $domain:\n\t$response[msg]</error>\n");
        } else {
            $output->writeln("\n<info>Successfuly purged domain $domain</info>\n");
        }
        if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
            $output->writeln(var_dump($response->toArray()));
        }
    }
}
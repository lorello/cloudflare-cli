<?php

namespace Cloudflare\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;

class DnsGetCommand extends ContainerAwareCommand
{
    public function __construct($app, $name = null)
    {
        parent::__construct($app, $name);
        $this->setDescription('Get all Dns records for a specific domain');
        $this->setHelp('List al records created in a zone');
        $this->addArgument('domain', Console\Input\InputArgument::REQUIRED, 'The domain');
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $domain = $input->getArgument('domain');

        $data = $this->app['guzzle']->getData('cf');

        $commandParams = [
            'u'   => $this->app['cf.user'],
            'tkn' => $this->app['cf.token'],
            'a'   => 'rec_load_all',
            'z'   => $domain,
            'v'   => 1,
        ];

        $response = $this->app['guzzle']['cf']->GetDns($commandParams);

        if ($response['result'] == 'error') {
            $output->writeln("\n<error>Error getting records for domain $domain:\n\t$response[msg]</error>\n");
        } else {
            $output->writeln("\n<info>Records from $domain</info>:\n");

            for ($i = 0; $i < $response['response']['recs']['count']; $i++) {
                $table_rows[$i] = [
                    $response['response']['recs']['objs'][$i]['display_name'],
                    $response['response']['recs']['objs'][$i]['type'],
                    $response['response']['recs']['objs'][$i]['display_content'],
                    $response['response']['recs']['objs'][$i]['props']['proxiable'],
                ];
            }

            $table = $this->getApplication()->getHelperSet()->get('table');
            $table
                ->setHeaders(['name', 'type', 'content', 'proxiable'])
                ->setRows($table_rows);
            $table->render($output);
        }
        if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
            $output->writeln(var_dump($response->toArray())."\n\n".var_dump($response['response']['recs']));
        }
    }
}

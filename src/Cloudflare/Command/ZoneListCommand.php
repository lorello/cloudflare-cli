<?php
namespace Cloudflare\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

class ZoneListCommand extends ContainerAwareCommand
{

    public function __construct($app, $name = null)
    {
        parent::__construct($app, $name);
        $this->setDescription('Lists all domains in a CloudFlare account along with other data.');
        $this->setHelp('This lists all domains in a CloudFlare account along with other data.');
        #$this->addArgument('domain', Console\Input\InputArgument::REQUIRED, 'The domain');
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {

        #$domain = $input->getArgument('domain');

        $data = $this->app['guzzle']->getData('cf');

        $commandParams = array(
            'tkn'     => $this->app['cf.token'],
            'email'   => $this->app['cf.user'],
            'a'       => 'zone_load_multi'
        );

        $response = $this->app['guzzle']['cf']->ApiPost($commandParams);

        if ($response['result'] == 'error') {
            $output->writeln("\n<error>Error getting domains for account ".$this->app['cf.user'].
                              ":\n\t$response[msg]</error>\n");
        } else {
            $output->writeln("\n<info>Domains for user ".$this->app['cf.user']."</info>:\n");

            for ($i = 0; $i < $response['response']['zones']['count']; $i++) {
                $table_rows[$i] = array(
                    $response['response']['zones']['objs'][$i]['zone_name'],
                    $response['response']['zones']['objs'][$i]['zone_type'],
                    $response['response']['zones']['objs'][$i]['zone_status'],
                    $response['response']['zones']['objs'][$i]['zone_status_class'],
                    $response['response']['zones']['objs'][$i]['props']['pro'],
                );
            }

            $table = $this->getApplication()->getHelperSet()->get('table');
            $table
                ->setHeaders(array('name', 'type', 'status', 'status-class', 'pro'))
                ->setRows($table_rows);
            $table->render($output);

        }
        if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
            $output->writeln(var_dump($response->toArray()) . "\n\n" . var_dump($response['response']['recs']));
        }
    }
}

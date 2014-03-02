<?php
namespace Lorello\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;
#use Guzzle\GuzzleServiceProvider;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

class PurgeCacheCommand extends ContainerAwareCommand 
{

  public function __construct($app, $name = null){
    parent::__construct($app, $name);
    $this->setDescription('Purge cache of a specific domain');
    $this->setHelp('Purge cache of the speficied domain');
    $this->addArgument('domain', Console\Input\InputArgument::REQUIRED, 'The domain to purge cache');
  }

  protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
  {

    $domain = $input->getArgument('domain');
    
    /*
    # Create a generic client and add a webservice description
    $client = $this->app['guzzle.client'];
    $description = ServiceDescription::factory('src/cloudflare.json');
    $client->setDescription($description);
    */
    
    $commandParams = array(
      'u'   => $this->app['email'],
      'tkn' => $this->app['token'],
      'a'   =>'fpurge_ts', 
      'z'   => $domain, 
      'v'   =>1
    );

    # cf is configured in services.json
    $client = $this->app['guzzle']->get('cf');

    $data = $this->app['guzzle']->getData('cf');
    echo $data['u'].' '.$data['tkn'];
    # shorter way than:
    #   $command = $client->getCommand('CachePurge', $commandParams);
    #   $response = $command->execute();
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

<?php
namespace Lorello\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

class GetDnsCommand extends ContainerAwareCommand 
{

  public function __construct($app, $name = null){
    parent::__construct($app, $name);
    $this->setDescription('Get all Dns records for a specific domain');
    $this->setHelp('List al records created in a zone');
    $this->addArgument('domain', Console\Input\InputArgument::REQUIRED, 'The domain');
  }

  protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
  {

    $domain = $input->getArgument('domain');
    
    $commandParams = array(
      'u'   => $this->app['email'],
      'tkn' => $this->app['token'],
      'a'   =>'rec_load_all', 
      'z'   => $domain, 
    );

    $response = $this->app['guzzle']['cf']->GetDns($commandParams);

    if ($response['result'] == 'error') {
      $output->writeln("\n<error>Error getting records for domain $domain:\n\t$response[msg]</error>\n");
    } else {
      $output->writeln("\n<info>Records from $domain</info>\n");
    }
    if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
      $output->writeln(var_dump($response->toArray()));
    }
  }
}

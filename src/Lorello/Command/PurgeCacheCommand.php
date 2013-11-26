<?php
namespace Lorello\Command;

use Guzzle\Http\Client;
use Symfony\Component\Console as Console;

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
    $client = new Client('https://www.cloudflare.com');
    $a = 'fpurge_ts';

    $domain = $input->getArgument('domain');

    $request = $client->post('api_json.html', null, array(
        'tkn'       => $this->app['token'],
        'email'     => $this->app['email'],
        'a'         => $a,
        'z'         => $domain,
        'v'         => 1
    ));
    $data = $request->send()->json();
    print_r($data);

  }
}

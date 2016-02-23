<?php

namespace Cloudflare\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;

class DnsAddCommand extends ContainerAwareCommand
{
    public function __construct($app, $name = null)
    {
        parent::__construct($app, $name);
        $this->setDescription('Create a DNS record for the specified domain');
        $this->setHelp('Create a DNS record for the specified domain. More info on DNS record type may be found at http://en.wikipedia.org/wiki/List_of_DNS_record_types');
        $this->addArgument('domain', Console\Input\InputArgument::REQUIRED, 'The domain');
        $this->addArgument('type', Console\Input\InputArgument::REQUIRED, 'Record type, one of A/CNAME/MX/TXT/SPF/AAAA/NS/SRV/LOC');
        $this->addArgument('name', Console\Input\InputArgument::REQUIRED, 'Record name');
        $this->addArgument('content', Console\Input\InputArgument::REQUIRED, 'Record content');
        $this->addOption('ttl', 't', Console\Input\InputOption::VALUE_REQUIRED, 'Record Time-to-live (TTL)', 'auto');
        $this->addOption('priority', 'p', Console\Input\InputOption::VALUE_REQUIRED, 'The priority of the target host, lower value means more preferred (applies to MX/SRV record types)', 10);
        $this->addOption('service', 's', Console\Input\InputOption::VALUE_REQUIRED, 'Service for SRV record');
        $this->addOption('servicename', 'a', Console\Input\InputOption::VALUE_REQUIRED, 'Service name for SRV record', '@');
        $this->addOption('protocol', 'l', Console\Input\InputOption::VALUE_REQUIRED, 'Protocol for SRV record. Possible values are tcp, udp, tls', 'tcp');
        $this->addOption('weight', 'w', Console\Input\InputOption::VALUE_REQUIRED, 'Weight for SRV record: a relative weight for records with the same priority, higher value means more preferred');
        $this->addOption('port', 'o', Console\Input\InputOption::VALUE_REQUIRED, 'Port for SRV record');
        $this->addOption('target', 'g', Console\Input\InputOption::VALUE_REQUIRED, 'Target for SRV record: the canonical hostname of the machine providing the service');
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $domain = $input->getArgument('domain');
        $type = strtoupper($input->getArgument('type'));
        $name = $input->getArgument('name');
        $content = $input->getArgument('content');
        $ttl = ($input->getOption('ttl') == 'auto' ? 1 : $input->getOption('ttl'));

        $data = $this->app['guzzle']->getData('cf');

        $commandParams = [
            'u'       => $this->app['cf.user'],
            'tkn'     => $this->app['cf.token'],
            'a'       => 'rec_new',
            'z'       => $domain,
            'type'    => $type,
            'name'    => $name,
            'content' => $content,
            'ttl'     => $ttl,
        ];

        $response = $this->app['guzzle']['cf']->ChangeDns($commandParams);

        if ($response['result'] == 'error') {
            $output->writeln("\n<error>Error adding record $type\n\t$name.$domain -> $content\n\n\t$response[msg]</error>\n");
        } else {
            $output->writeln("\n<info>Record $type $name.$domain -> $content successfully created</info>:\n");
        }
        if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
            $output->writeln(var_dump($response->toArray()).'\n\n'.var_dump($response['response']['recs']));
        }
    }
}

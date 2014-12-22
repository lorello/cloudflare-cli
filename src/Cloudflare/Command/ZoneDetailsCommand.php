<?php
namespace Cloudflare\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

class ZoneDetailsCommand extends ContainerAwareCommand
{

    private static $labels = array();

    public function __construct($app, $name = null)
    {
        parent::__construct($app, $name);
        $this->setDescription('Get details about the specified zone');
        $this->setHelp('This command allows you to get a all details abount the requested domain.');
        $this->addArgument('domain', Console\Input\InputArgument::REQUIRED, 'The domain you want to get details');


        $this->labels['setting']['zone_id'] = 'Zone ID';
        $this->labels['setting']['user_id'] = 'User ID';
        $this->labels['setting']['zone_name'] = 'Zone name';
        $this->labels['setting']['display_name'] = 'Display name';

        $bool_values = array(0 => 'Off', 1=>'On');

        $this->labels['dns_cname'] = $bool_values;
        $this->labels['dns_partner'] = $bool_values;
        $this->labels['dns_anon_partner'] = $bool_values;
        $this->labels['pro'] = $bool_values;
        $this->labels['expired_pro'] = $bool_values;
        $this->labels['pro_sub'] = $bool_values;
        $this->labels['ssl'] = $bool_values;
        $this->labels['expired_ssl'] = $bool_values;
        $this->labels['expired_rs_pro'] = $bool_values;
        $this->labels['reseller_pro'] = $bool_values;
        $this->labels['force_interal'] = $bool_values;
        $this->labels['ssl_needed'] = $bool_values;
        $this->labels['alexa_rank'] = $bool_values;

        $this->labels['zone_status_desc'] = function ($value) { return preg_replace('/\(.*\)/', '', $value); };
    }

    protected function labelize($key, $value) {

        if (isset($this->labels[$key])) {
            if (is_callable($this->labels[$key])) {
                return $this->labels[$key]($value);
            } elseif (isset($this->labels[$key][$value])) {
                if (is_string($this->labels[$key][$value])) {
                    return $this->labels[$key][$value];
                }
            }
        }

        if(is_array($value)) {
            $results = array();
            foreach($value as $subk=>$subv) {
                if (is_integer($subk))
                    $results[] = '- '. $this->labelize($subk, $subv);
                else
                    $results[] = '* '. $this->labelize('setting', $subk) .
                    ': '. $this->labelize($subk, $subv);
            }
            return implode("\n", $results);
        }

        if (is_string($value))
            return $value;
    }



    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $domain = $input->getArgument('domain');

        $data = $this->app['guzzle']->getData('cf');

        $commandParams = array(
            'tkn'     => $this->app['cf.token'],
            'email'   => $this->app['cf.user'],
            'a'       => 'zone_load_multi'
        );

        $response = $this->app['guzzle']['cf']->ApiPost($commandParams);

        if ($response['result'] == 'error') {
            $output->writeln("\n<error>Error getting details on domain $domain:\n\t$response[msg]</error>\n");
        } else {

            $table_rows = array();

            for ($i = 0; $i < $response['response']['zones']['count']; $i++) {

                if ($response['response']['zones']['objs'][$i]['zone_name'] == $domain) {

                    $output->writeln("\n<info>Current settings for domain $domain</info>\n");

                    foreach($response['response']['zones']['objs'][$i] as $k=>$v)
                    {
                        $table_rows[] = array(
                            $this->labelize('setting', $k),
                            $this->labelize($k, $v)
                        );
                    }

                    $table = $this->getApplication()->getHelperSet()->get('table');
                    $table
                       ->setHeaders(array('Setting', 'Value'))
                       ->setRows($table_rows);
                    $table->render($output);

                    break;
                }
            }

            if (count($table_rows) == 0) {
                $output->writeln("\n<error>Domain $domain not found</error>\n");
            }

        }


        if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
            $output->writeln(var_dump($response->toArray()));
        }
    }
}

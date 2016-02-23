<?php

namespace Cloudflare\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;

class ZoneListCommand extends ContainerAwareCommand
{
    public function __construct($app, $name = null)
    {
        parent::__construct($app, $name);
        $this->setDescription('Lists all domains in a CloudFlare account along with other data.');
        $this->setHelp('This lists all domains in a CloudFlare account along with other data.');

        $this->labels['setting'][''] = '';

        $bool_values = [0 => 'Off', 1 => 'On'];

        $this->labels['zone-status']['V'] = 'Verified';
        $this->labels['zone-status']['P'] = 'Waiting DNS change';
        $this->labels['zone-status']['INI'] = 'Waiting Setup';

        $this->labels['pro'] = $bool_values;
        $this->labels['status-class'] = function ($value) { return preg_replace('/status-/', '', $value); };
    }

    protected function labelize($key, $value)
    {
        if (isset($this->labels[$key])) {
            if (is_callable($this->labels[$key])) {
                return $this->labels[$key]($value);
            } elseif (isset($this->labels[$key][$value])) {
                if (is_string($this->labels[$key][$value])) {
                    return $this->labels[$key][$value];
                }
            }
        }

        if (is_array($value)) {
            $results = [];
            foreach ($value as $subk => $subv) {
                if (is_int($subk)) {
                    $results[] = '- '.$this->labelize($subk, $subv);
                } else {
                    $results[] = '* '.$this->labelize('setting', $subk).
                    ': '.$this->labelize($subk, $subv);
                }
            }

            return implode("\n", $results);
        }

        if (is_string($value)) {
            return $value;
        }
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $data = $this->app['guzzle']->getData('cf');

        $commandParams = [
            'tkn'     => $this->app['cf.token'],
            'email'   => $this->app['cf.user'],
            'a'       => 'zone_load_multi',
        ];

        $response = $this->app['guzzle']['cf']->ApiPost($commandParams);

        if ($response['result'] == 'error') {
            $output->writeln("\n<error>Error getting domains for account ".$this->app['cf.user'].
                              ":\n\t$response[msg]</error>\n");
        } else {
            $output->writeln("\n<info>Domains for user ".$this->app['cf.user']."</info>:\n");

            for ($i = 0; $i < $response['response']['zones']['count']; $i++) {
                $table_rows[$i] = [
                    $response['response']['zones']['objs'][$i]['zone_name'],
                    $this->labelize('zone-status', $response['response']['zones']['objs'][$i]['zone_status']),
                    $this->labelize('status-class', $response['response']['zones']['objs'][$i]['zone_status_class']),
                    $this->labelize('pro', $response['response']['zones']['objs'][$i]['props']['pro']),
                ];
            }

            $table = $this->getApplication()->getHelperSet()->get('table');
            $table
                ->setHeaders(['name', 'status', 'status-class', 'pro'])
                ->setRows($table_rows);
            $table->render($output);
        }
        if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
            $output->writeln(var_dump($response->toArray())."\n\n".var_dump($response['response']['recs']));
        }
    }
}

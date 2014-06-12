<?php
namespace Cloudflare\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

class ZoneGetCommand extends ContainerAwareCommand
{

    private static $labels = array();

    public function __construct($app, $name = null)
    {
        parent::__construct($app, $name);
        $this->setDescription('Get current settings of the specified zone');
        $this->setHelp('This command allows you to get a full dump of all settings of the requested domain.');
        $this->addArgument('domain', Console\Input\InputArgument::REQUIRED, 'The domain you want to get settings');


        $this->labels['setting']['userSecuritySetting'] = 'User Security Setting';
        $this->labels['setting']['dev_mode'] = 'Development Mode';
        $this->labels['setting']['ob'] = 'Always online';
        $this->labels['setting']['chl_ttl'] = 'Challenge TTL (seconds)';
        $this->labels['setting']['exp_ttl'] = 'Expire TTL (for CloudFlare-cached items)';
        $this->labels['setting']['fpurge_ts'] = 'Cache Purge Time';
        $this->labels['setting']['hotlink'] = 'Hotlink protection';
        $this->labels['setting']['outlink'] = 'Outlink';
        $this->labels['setting']['sec_lvl'] = 'Basic Security Level';
        $this->labels['setting']['cache_lvl'] = 'Cache Level';
        $this->labels['setting']['cache_ttl'] = 'Cache TTL';
        $this->labels['setting']['outboundLinks'] = 'Outbound Links';
        $this->labels['setting']['async'] = 'Rocket Loader';
        $this->labels['setting']['minify'] = 'Minification';
        $this->labels['setting']['ipv46'] = 'IPV6';
        $this->labels['setting']['host_spf'] = 'Host SPF';
        $this->labels['setting']['bic'] = 'Browser Integrity Check';
        $this->labels['setting']['email_filter'] = 'Email obfuscation';
        $this->labels['setting']['sse'] = 'Server Side Exclude';
        $this->labels['setting']['geoloc'] = 'IP Geolocation';
        $this->labels['setting']['spdy'] = 'SPDY (>Pro)';
        $this->labels['setting']['ssl'] = 'SSL Status (>Pro)';
        $this->labels['setting']['lazy'] = 'Mirage: Lazy Load (>Pro)';
        $this->labels['setting']['img'] = 'Mirage: Auto-resize, Polish settings (>Pro)';
        $this->labels['setting']['preload'] = 'Cache preloader (>Pro)';
        $this->labels['setting']['waf_profile'] = 'Web Application Firewall (>Pro)';
        $this->labels['setting']['ddos'] = 'Advanced DDoS Protection (>Pro)';

        $bool_values = array(0 => 'Off', 1=>'On');

        $this->labels['dev_mode'] = $bool_values;
        $this->labels['ob'] = $bool_values;
        $this->labels['waf_profile'] = $bool_values;
        $this->labels['host_spf'] = $bool_values;

        $this->labels['sec_lvl']['eoff'] = 'Essentially Off';
        $this->labels['sec_lvl']['low'] = 'Low';
        $this->labels['sec_lvl']['med'] = 'Medium';
        $this->labels['sec_lvl']['high'] = 'High';
        $this->labels['sec_lvl']['help'] = 'I\'m Under Attack!';

        $this->labels['cache_lvl']['agg'] = 'Aggressive';
        $this->labels['cache_lvl']['iqs'] = 'Simplified';

        $this->labels['async']['0'] = 'Off';
        $this->labels['async']['a'] = 'Automatic';
        $this->labels['async']['m'] = 'Manual';

        $this->labels['minify'][0] = 'Off';
        $this->labels['minify'][1] = 'JavaScript only';
        $this->labels['minify'][2] = 'CSS only';
        $this->labels['minify'][3] = 'Javascript and CSS';
        $this->labels['minify'][4] = 'HTML only';
        $this->labels['minify'][5] = 'JavaScript and HTML';
        $this->labels['minify'][6] = 'CSS and HTML';
        $this->labels['minify'][7] = 'CSS, Javascript and HTML';
        
        $this->labels['ipv46'][0] = 'Off';
        $this->labels['ipv46'][3] = 'Full';

        $this->labels['bic'] = $bool_values;
        $this->labels['email_filter'] = $bool_values;
        $this->labels['sse'] = $bool_values;
        $this->labels['hotlink'] = $bool_values;
        $this->labels['outlink'] = $bool_values;
        $this->labels['geoloc'] = $bool_values;
        $this->labels['spdy'] = $bool_values;
        
        $this->labels['ssl'][0] = 'Off';
        $this->labels['ssl'][1] = 'Flexible';
        $this->labels['ssl'][2] = 'Full';
        $this->labels['ssl'][3] = 'Full (Strict)';

        $this->labels['lazy'] = $bool_values;

        $this->labels['img'][0] = 'Off';
        $this->labels['img'][100] = 'Auto-resize on';
        $this->labels['img'][200] = 'Polish: Basic';
        $this->labels['img'][201] = 'Polish: Basic, Mirage: Auto-resize on';
        $this->labels['img'][170] = 'Polish: Basic + JPEG';
        $this->labels['img'][171] = 'Polish: Basic + JPEG, Mirage: Auto-resize on';

        $this->labels['preload'] = $bool_values;

        $this->labels['fpurge_ts'] = function ($value) { return date('r', $value); };  
    }

    protected function labelize($key, $value) {

        if (isset($this->labels[$key])) {
          if (is_callable($this->labels[$key]))
              return $this->labels[$key]($value);
          elseif (isset($this->labels[$key][$value]))
              return $this->labels[$key][$value];
        }
        
        return $value;
    }
    


    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $domain = $input->getArgument('domain');

        $data = $this->app['guzzle']->getData('cf');

        $commandParams = array(
            'u'   => $this->app['cf.user'],
            'tkn' => $this->app['cf.token'],
            'a'   => 'zone_settings',
            'z'   => $domain,
        );

        $response = $this->app['guzzle']['cf']->CachePurge($commandParams);

        if ($response['result'] == 'error') {
            $output->writeln("\n<error>Error getting settings on domain $domain:\n\t$response[msg]</error>\n");
        } else {
            $output->writeln("\n<info>Current settings for domain $domain</info>\n");

            foreach($response['response']['result']['objs'][0] as $k=>$v)
            {
                 $table_rows[] = array(
                    $this->labelize('setting', $k), 
                    $this->labelize($k, $v));
             }

             $table = $this->getApplication()->getHelperSet()->get('table');
             $table
                 ->setHeaders(array('Setting', 'Value'))
                 ->setRows($table_rows);
             $table->render($output);

        }
        if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
            $output->writeln(var_dump($response->toArray()));
        }
    }
}

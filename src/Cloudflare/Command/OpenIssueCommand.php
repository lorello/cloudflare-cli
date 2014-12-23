<?php
namespace Cloudflare\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

class OpenIssueCommand extends ContainerAwareCommand
{

    public function __construct($app, $name = null)
    {
        parent::__construct($app, $name);
        $this->setDescription('Create an issue on Github');
        $this->setHelp("Create an issue so that developer can find&remove bugs in this software. Please be descriptive in the body anche check if the issue is already present at https://github.com/lorello/cloudflare-cli/issues.\n\n");
        $this->addArgument('title', Console\Input\InputArgument::REQUIRED, 'A synthetic description of the bug you have found or the request you want to submit.');
        $this->addArgument('body', Console\Input\InputArgument::OPTIONAL, 'An extended description of the request: add output of the error you encountered.');
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $title = $input->getArgument('title');
        $body = $input->getArgument('body');

        $apiPath = '/repos/lorello/cloudflare-cli/issues';
        $apiUrl = 'https://api.github.com'.$apiPath;

        # https://developer.github.com/v3/issues/#create-an-issue
        $request = $this->app['guzzle.client']->createRequest('POST', $apiUrl);

        # explicitly require API version 3
        $request->addHeader('Accept', 'application/vnd.github.v3+json');
        $postBody = $request->getBody() or die('karakiri');
        $postBody->setField('title', $title);
        $postBody->setField('body', $body);
        $response = $this->app['guzzle.client']->send($request);

        if ($response['result'] == 'error') {
            $output->writeln("\n<error>Error adding record $type\n\t$name.$domain -> $content\n\n\t$response[msg]</error>\n");
        } else {
            $output->writeln("\n<info>Record $type $name.$domain -> $content successfully created</info>:\n");

        }
        if (OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity()) {
            $output->writeln(var_dump($response->toArray()) . "\n\n" . var_dump($response['response']['recs']));
        }
    }
}

<?php
# Container-aware commands learned here:
# http://dcousineau.com/blog/2013/03/28/using-symfony-console-from-scratch/

namespace Cloudflare\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Knp\Command\Command as Kommand;

use Silex\Application;

abstract class ContainerAwareCommand extends Kommand
{
    /**
     * @var Silex\Application
     */
    protected $app;

    public function __construct(Application $app, $name = null)
    {
        parent::__construct($name);
        $this->app = $app;
    }
}

<?php

namespace Boxyback\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use Boxyback\Rotate;

class BackupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('backup')
            ->setDescription('Backup an or many Apps')
            ->addArgument(
                'yamlArgument',
                InputArgument::REQUIRED,
                "What's App do you want to backup (YML File)?"
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = '$(date +%Y-%m-%d-%H-%M-%S)';

        if ($yamlArgument = $input->getArgument('yamlArgument')) {
            $yaml = new Parser();
            $apps = $yaml->parse(file_get_contents($yamlArgument))['apps'];
        }

        foreach($apps as $app){
        	  $app = (object) $app;

            system('mkdir -p /home/matthieu/backups/'.$app->id);

            if(property_exists($app, 'database') && property_exists($app, 'password')){
                system('mysqldump --protocol=socket -S /run/mysql-default/mysqld.sock --user='.$app->user.' --password='.$app->password.' '.$app->database.' | gzip > /home/matthieu/backups/'.$app->id.'/dump_'.$date.'.sql.gz');
            }

            if($app->type=="all"){
            	system('tar -zcvf /home/matthieu/backups/'.$app->id.'/archive_'.$date.'.tar.gz -C /home/'.$app->user.'/'.$app->folder.' .');
			      }

            $rotate = new Rotate("/home/matthieu/backups/".$app->id."/", @$app->frequency);

        }

    }
}

<?php

namespace Boxyback\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

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

        $script = file_get_contents(__DIR__."/rotate.php");
        file_put_contents('rotate', $script);
        chmod('rotate', 0755);
        
        foreach($apps as $app){
        	$app = (object) $app;

            system('mkdir -p /home/matthieu/backups/'.$app->id);

            if(property_exists($app, 'database') && property_exists($app, 'password')){
                system('mysqldump --protocol=socket -S /run/mysql-default/mysqld.sock --user='.$app->user.' --password='.$app->password.' '.$app->database.' | gzip > /home/matthieu/backups/'.$app->id.'/dump_'.$date.'.sql.gz');
            }

            if($app->type=="all"){
            	system('tar -zcvf /home/matthieu/backups/'.$app->id.'/archive_'.$date.'.tar.gz -C /home/'.$app->user.'/'.$app->folder.' .');
			}

            //system('find /home/matthieu/backups/'.$app->id.'/ -type f -mtime +'.($app->save-1).' -exec rm -v {} \;');

            system('./rotate /home/matthieu/backups/'.$app->id.'/');

        }

        unlink('rotate');
    }
}
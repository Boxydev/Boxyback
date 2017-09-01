<?php

namespace Boxydev\Boxyback\Command;

use Boxydev\Boxyback\Configuration\BackupConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Parser;
use Boxydev\Boxyback\Rotate;

class BackupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('backup')
            ->setDescription('Backup an or many Apps')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                "What's App do you want to backup (YML File)?"
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($file = $input->getArgument('file')) {
            $yaml_parser = new Parser();

            $processor = new Processor();
            $datas = $processor->processConfiguration(
                new BackupConfiguration(),
                array($yaml_parser->parse(file_get_contents($file)))
            );
        }

        if ($apps = $datas['boxyback']['apps']) {
            foreach ($apps as $id => $app) {
                $localFolder = $datas['boxyback']['cloud']['local'];
                $appFolder = $app['folder'];
                $database = isset($app['mysql']) ? $app['mysql'] : null;

                if (!is_dir($localFolder.'/'.$id)) {
                    mkdir($localFolder.'/'.$id, 0755, true);
                }

                $date = date('Y-m-d-H-i-s');

                $process = new Process('tar -zcvf '.$localFolder.'/'.$id.'/archive_'.$date.'.tar.gz -C .'.$appFolder.' .');
                $process->run();

                if (!$process->isSuccessful()) {
                    throw new \RuntimeException($process->getErrorOutput());
                }

                if ($database) {
                    $process = new Process('mysqldump --host='.$database['host'].' --user='.$database['user'].' --password='.$database['password'].' '.$app['mysql']['database'].' | gzip > '.$localFolder.'/'.$id.'/dump_'.$date.'.sql.gz');
                    $process->run();

                    if (!$process->isSuccessful()) {
                        throw new \RuntimeException($process->getErrorOutput());
                    }
                }

                $rotate = new Rotate($localFolder.'/'.$id);
                $rotate->setDay(7);
                $rotate->run();

                $output->writeln('<info>Backup done !</info>');
            }
        } else {
            $output->writeln('<error>Missing boxyback or apps index in Yaml file</error>');
        }

    }
}

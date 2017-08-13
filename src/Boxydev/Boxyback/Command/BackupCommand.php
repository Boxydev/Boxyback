<?php

namespace Boxydev\Boxyback\Command;

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
            $datas = $yaml_parser->parse(file_get_contents($file));
        }

        if (isset($datas['boxyback']) && isset($datas['boxyback']['apps'])) {
            $apps = $datas['boxyback']['apps'];

            foreach ($apps as $id => $app) {
                if (!is_dir('.'.$datas['boxyback']['cloud']['local'].'/'.$id)) {
                    mkdir('.'.$datas['boxyback']['cloud']['local'].'/'.$id, 0755, true);
                }

                $date = date('Y-m-d-H-i-s');

                if (isset($datas['boxyback']['cloud']) && isset($datas['boxyback']['cloud']['local'])) {
                    $process = new Process('tar -zcvf .'.$datas['boxyback']['cloud']['local'].'/'.$id.'/archive_'.$date.'.tar.gz -C .'.$app['folder'].' .');
                    $process->run();

                    if (!$process->isSuccessful()) {
                        throw new \RuntimeException($process->getErrorOutput());
                    }
                }

                if (isset($app['mysql']) && isset($app['mysql']['database']) && isset($app['mysql']['user']) && isset($app['mysql']['password'])) {
                    $process = new Process('mysqldump --user='.$app['mysql']['user'].' --password='.$app['mysql']['password'].' '.$app['mysql']['database'].' | gzip > .'.$datas['boxyback']['cloud']['local'].'/'.$id.'/dump_'.$date.'.sql.gz');
                    $process->run();

                    if (!$process->isSuccessful()) {
                        throw new \RuntimeException($process->getErrorOutput());
                    }
                }

                $rotate = new Rotate('.'.$datas['boxyback']['cloud']['local'].'/'.$id);
                $rotate->setDay(7);
                $rotate->run();
            }
        } else {
            $output->writeln('<error>Missing boxyback or apps index in Yaml file</error>');
        }
    }
}

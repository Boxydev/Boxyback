<?php

namespace Boxyback\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class FtpCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ftp')
            ->setDescription('Sync backups with FTP')
            ->addArgument(
                'yamlArgument',
                InputArgument::REQUIRED,
                "What's FTP config (YML File)?"
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($yamlArgument = $input->getArgument('yamlArgument')) {
            $yaml = new Parser();
            $config = $yaml->parse(file_get_contents($yamlArgument))['backup'];
        }

        $config = (object) $config;
        $config->ftp = (object) $config->ftp;

        system('lftp ftp://'.$config->ftp->login.':'.$config->ftp->password.'@'.$config->ftp->host.' -e "set ftp:ssl-allow no; mirror -e -R '.$config->dir.' /'.$config->ftp->vm.'/apps; quit"');
        system('echo "Le backup FTP de '.$config->ftp->vm.' a bien été synchronisé." | mail -s "Backup FTP '.$config->ftp->vm.'" -aFrom:'.$config->ftp->vm.'\<'.$config->ftp->vm.'@boxydev.com\> '.$config->ftp->email);
    }
}

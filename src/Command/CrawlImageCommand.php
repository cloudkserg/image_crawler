<?php
/**
 * Created by PhpStorm.
 * User: kirya
 * Date: 01.07.17
 * Time: 17:04
 */

namespace src\Command;


use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;

class CrawlImageCommand extends Command
{

    const URL = 'https://arxip.com/project/42286/architecture-individualnyiy-jiloy-dom-v-pnovodarino-jiloy-dom-jile-kottedj-osobnyak-modernizm-modernizm-minimalizm-minimalizm-2-etaja-6m-500-1000-m2-42286/';
    //const URL = 'https://cdn.arxip.com/resize/object_detail/arxip/239/2399838e408f33c5ae7282be51f2fce1.jpg';
    //const URL = 'https://beta2.arxip.com/project/42286/architecture-individualnyiy-jiloy-dom-v-pnovodarino-jiloy-dom-jile-kottedj-osobnyak-modernizm-modernizm-minimalizm-minimalizm-2-etaja-6m-500-1000-m2-42286/?super=1';
    //const URL = 'https://beta2.arxip.com/index.html';
    //const URL = 'http://beta2.arxip.com:3000/';

    protected function configure()
    {
        $this->setName('image:crawl_all');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->section(
            sprintf('[url:%s] Begin image crawler [time:%s]', self::URL, Carbon::create()->toDateTimeString())
        );

        $client = new Client();
        $response = $client->request('GET', self::URL, [
            'on_stats' => function (TransferStats $stats) use ($io) {
                $handlerStats = $stats->getHandlerStats();
                //var_dump($handlerStats);
                $io->writeln($handlerStats['total_time']);
            }
        ]);

//        $crawler = new Crawler((string)$response->getBody());
//        $images = $crawler->selectImage('img');
//        var_dump($images);

        $io->section(
            sprintf('[url:%s] End image crawler [time:%s]', self::URL, Carbon::create()->toDateTimeString())
        );
    }


}
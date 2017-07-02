<?php
/**
 * Created by PhpStorm.
 * User: kirya
 * Date: 01.07.17
 * Time: 17:04
 */

namespace src\Command;


use Carbon\Carbon;
use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\TransferStats;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
class_alias('\RedBeanPHP\R','\R');
use R;


class CrawlImageCommand extends Command
{

    const LIMIT_TIME = 5;

    const DSN = 'mysql:host=localhost;dbname=%s';
    //const URL = 'https://arxip.com/project/';
    //const URL = 'https://cdn.arxip.com/resize/object_detail/arxip/239/2399838e408f33c5ae7282be51f2fce1.jpg';
    const URL = 'https://arxip.com/project/%s';
    //const URL = 'https://beta2.arxip.com/index.html';
    //const URL = 'http://beta2.arxip.com:3000/';

    protected function configure()
    {
        $this->setName('image:crawl_all');
    }


    private function loadConfigDb()
    {
        $dotenv = new Dotenv(dirname(dirname(__DIR__)));
        $dotenv->load();
    }

    private function connectDb()
    {
        $this->loadConfigDb();
        R::setup(sprintf(self::DSN, $_ENV['DATABASE_NAME']), $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASS']);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);


        $this->connectDb();
        $collection = R::findCollection('arxip_objects', "ACTIVE='Y' ORDER BY id DESC");
        while ($itemCol = $collection->next()) {
            $item = $itemCol->getProperties()['id'];
            $this->parseUrl($item, $io);
        }
    }

    private function parseUrl($id, SymfonyStyle $io)
    {
        $url = sprintf(self::URL, $id);
        $io->section(
            sprintf('[url:%s] Begin image crawler [time:%s]', $url, Carbon::create()->toDateTimeString())
        );

        $response = $this->request($url, $io);
        if (empty($response)) {
            $io->error(sprintf('[url:%s] error not load', $url));
            throw new \ErrorException('error');
        }

        $crawler = new Crawler((string)$response->getBody());
        collect($crawler->filter('img'))->map(function (\DOMElement $img) use ($io) {
            $url = $img->getAttribute('src');
            if (!empty($url)) {
                $this->request($url, $io);
            }
        });

        $io->section(
            sprintf('[url:%s] End image crawler [time:%s]', self::URL, Carbon::create()->toDateTimeString())
        );
    }
    private function request($url, SymfonyStyle $io)
    {
        if (substr($url, 0, 4) !== 'http') {
            $url = 'https://arxip.com' . $url;
        }



        $client = new Client();
        try {
            $response = $client->request('GET', $url, [
                'on_stats' => function (TransferStats $stats) use ($io, $url) {
                    $handlerStats = $stats->getHandlerStats();
                    $totalTime = $handlerStats['total_time'];
                    if ($totalTime > self::LIMIT_TIME) {
                        $io->error(sprintf('ERROR[%s] %s time:%s', Carbon::create()->toDateTimeString(), $url, $totalTime));
                        throw new \ErrorException('error');
                    }
                    $io->writeln(sprintf("\t url=%s [%s]", $url, $totalTime));
                }
            ]);
            return $response;
        } catch (ClientException $e) {
            return null;
        }
    }


}
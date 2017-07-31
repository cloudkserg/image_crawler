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
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\TransferStats;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
class_alias('\RedBeanPHP\R','\R');
use R;
use KzykHys\Parallel\Parallel;


class CrawlImageCommand extends Command
{

    const PAGE_LIMIT_TIME = 20;
    const LIMIT_TIME = 10;

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
        //$collection = R::findCollection('arxip_objects', "ACTIVE='Y' AND  ORDER BY id DESC");


	$ids = [];
        //while ($itemCol = $collection->next()) {
	//for ($i=1090240; $i>=1080000;$i--) {
        //    $ids[] = $i;//$itemCol->getProperties()['id'];
        //}

        //$ids = [id,1099551,1099550,1099549,1099547,1099546,1099545,1099544,1099542,1099541,1099540,1099538,1099537,1099534,1099533,1099532,1099531,1099530,1099529,1099528,1099527,1099526,1099525,1099524,1099523,1099522,1099521,1099520,1099519,1099518,1099517,1099516,1099515,1099514,1099513,1099512,1099511,1099510,1099509,1099508,1099507,1099506,1099505,1099502,1099501,1099500,1099498,1099497,1099496,1099495,1099494]
$ids = [
        //1090240,1090239,1090238,1090237,1090228,1090227,1090226,1090225,1090224,
        1100081,1100080,1100079,1100078,1100077,1100076,1100075,1100074,1100073,1100072,
         1100071,1100070,1100069,1100068,1100067,1100066,1100065,1100064,1100063,1100062,1100061,1100060,1100059,1100058,1100057,1100056,1100055,1100054,1100053,1100052,1100051,1100050,1100049,1100048,1100047,1100046,1100045,1100043,1100042,1100041,1100040,1100039,1100038,1100037,1100036,1100035,1100034,1100033,1100032,1100031,1100030,1100029,1100028,1100027,1100026,1100025,1100024,1100023,1100022,1100021,1100020,1100019,1100018,1100017,1100016,1100015,1100014,1100013,1100012,1100011,1100010,1100009,1100008,1100007,1100006,1100005,1100004,1100003,1100001,1099999,1099998,1099997,1099996,1099995,1099994,1099993,1099992,1099991,1099990,1099989,1099988,1099987,1099986,1099985,1099984,1099983,1099982,1099981,1099980,1099979,1099977,1099976,1099974,1099973,1099972,1099971,1099970,1099969,1099968,1099967,1099966,1099965,1099964,1099963,1099962,1099961,1099959,1099958,1099957,1099956,1099955,1099954,1099953,1099952,1099951,1099950,1099949,1099948,1099947,1099946,1099945,1099944,1099943,1099942,1099941,1099940,1099939,1099938,1099937,1099936,1099935,1099934,1099933,1099932,1099931,1099930,1099929,1099928,1099927,1099926,1099925,1099924,1099923,1099922,1099921,1099920,1099919,1099918,1099917,1099916,1099915,1099914,1099913,1099912,1099910,1099909,1099908,1099907,1099906,1099905,1099904,1099903,1099902,1099901,1099900,1099899,1099898,1099897,1099896,1099895,1099894,1099893,1099892,1099890,1099889,1099888,1099887,1099886,1099885,1099884,1099883,1099882,1099881,1099880,1099879,1099878,1099877,1099876,1099875,1099874,1099873,1099872,1099871,1099870,1099869,1099868,1099864,1099863,1099862,1099861,1099860,1099859,1099858,1099857,1099856,1099855,1099854,1099853,1099852,1099851,1099850,1099849,1099848,1099847,1099846,1099845,1099844,1099843,1099842,1099841,1099840,1099839,1099838,1099837,1099836,1099835,1099834,1099833,1099832,1099831,1099830,1099828,1099827,1099826,1099825,1099824,1099823,1099822,1099821,1099820,1099819,1099818,1099817,1099816,1099815,1099814,1099813,1099812,1099811,1099810,1099809,1099808,1099807,1099806,1099805,1099804,1099802,1099801,1099800,1099799,1099798,1099797,1099796,1099795,1099794,1099793,1099792,1099791,1099790,1099789,1099787,1099786,1099785,1099784,1099783,1099782,1099781,1099780,1099779,1099778,1099777,1099776,1099775,1099774,1099773,1099772,1099769,1099767,1099766,1099765,1099764,1099763,1099762,1099761,1099760,1099759,1099758,1099757,1099756,1099755,1099754,1099753,1099752,1099751,1099750,1099748,1099747,1099746,1099745,1099744,1099743,1099742,1099741,1099740,1099739,1099738,1099737,1099736,1099735,1099734,1099733,1099732,1099731,1099730,1099729,1099728,1099727,1099726,1099725,1099724,1099723,1099722,1099721,1099720,1099719,1099718,1099717,1099716,1099715,1099714,1099713,1099712,1099711,1099710,1099709,1099708,1099707,1099706,1099705,1099704,1099703,1099701,1099700,1099699,1099698,1099697,1099696,1099695,1099694,1099693,1099692,1099691,1099690,1099689,1099688,1099687,1099686,1099685,1099684,1099683,1099682,1099681,1099680,1099679,1099678,1099677,1099676,1099675,1099674,1099673,1099672,1099671,1099670,1099669,1099668,1099667,1099666,1099665,1099664,1099663,1099662,1099661,1099660,1099659,1099658,1099657,1099656,1099655,1099654,1099653,1099652,1099651,1099648,1099647,1099646,1099645,1099644,1099643,1099642,1099641,1099640,1099639,1099638,1099637,1099636,1099635,1099634,1099633,1099632,1099631,1099630,1099629,1099628,1099627,1099626,1099624,1099623,1099622,1099621,1099620,1099619,1099618,1099617,1099615,1099614,1099613,1099612,1099611,1099610,1099609,1099608,1099607,1099606,1099605,1099604,1099603,1099602,1099601,1099600,1099599,1099598,1099597,1099596,1099595,1099594,1099593,1099592,1099591,1099590,1099589,1099588,1099587,1099586,1099585,1099584,1099583,1099582,1099581,1099580,1099579,1099578,1099577,1099576,1099575,1099574,1099573,1099572,1099571,1099570,1099569,1099567,1099561,1099560,1099557,1099556,1099555,1099554,1099553,1099552,1099551
    ];
	$id_chunks = array_chunk($ids, 40);

	$parallel = new Parallel();
	foreach ($id_chunks as $chunkIds) {
		//foreach ($chunkIds as $id) {
		$parallel->each($chunkIds, function ($id) use ($io) {
			$this->parseUrl($id, $io);
		});
        sleep(1);
		//}
	}
        $io->section(sprintf('all ids=%s', count($ids)));
    }

    private function parseUrl($id, SymfonyStyle $io)
    {
        $url = sprintf(self::URL, $id);
        /*$io->section(
            sprintf('[url:%s] Begin image crawler [time:%s]', $url, Carbon::create()->toDateTimeString())
        );*/
        $io->writeln(sprintf("\n" . 'full url [url:%s]', $url));

        $response = $this->request($url, $io, self::PAGE_LIMIT_TIME);
        if (empty($response)) {
            return;
        }

        $crawler = new Crawler((string)$response->getBody());
        collect($crawler->filter('img'))->map(function (\DOMElement $img) use ($io) {
            $url = $img->getAttribute('src');
            if (!empty($url)) {
                $this->request($url, $io, self::LIMIT_TIME);
            }
        });

       /*$io->section(
            sprintf('[url:%s] End image crawler [time:%s]', self::URL, Carbon::create()->toDateTimeString())
        );*/
    }
    private function request($url, SymfonyStyle $io, $limitTime)
    {
        if (substr($url, 0, 4) !== 'http') {
            $url = 'https://arxip.com' . $url;
        }



        $client = new Client();
        try {
            $response = $client->request('GET', $url, [
                'on_stats' => function (TransferStats $stats) use ($io, $url, $limitTime) {
                    $handlerStats = $stats->getHandlerStats();
                    $totalTime = $handlerStats['total_time'];
                    $size = round($handlerStats['size_download'] / 1024);

                    if ($totalTime > $limitTime) {
                        $io->error(sprintf('ERROR[%s] %s time:%s size:%skb', Carbon::create()->toDateTimeString(), $url, $totalTime, $size));
                        throw new \ErrorException('error');
                    }
                   $io->writeln(sprintf("\t url=%s [%s] size[%skb]", $url, $totalTime, $size));
                }
            ]);
            if ($response->getStatusCode() != '200') {
                $io->error(sprintf('[error url=%s] status=%s body=%s', $url, $response->getStatusCode(), $response->getBody()));
                return null;
            }
            return $response;
        } catch (ClientException $e) {
            $io->error(sprintf('[error url=%s] message=%s', $url, $e->getMessage()));
            return null;
        } catch (ServerException $e) {
            $io->error(sprintf('[error url=%s] message=%s', $url, $e->getMessage()));
            return null;
        }

    }


}

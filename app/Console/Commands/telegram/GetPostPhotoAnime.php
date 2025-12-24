<?php

namespace App\Console\Commands\telegram;

use Illuminate\Console\Command;

use App\Models\Category;
use App\Models\Post;
use App\Models\Source;
use App\Models\Group;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class GetPostPhotoAnime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-post-photo-anime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Сбор постов с групп телеграмм аниме';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $list_blog_name = [
            'https://t.me/s/mysavedpicturesdontenterpls',
            'https://t.me/s/anime_art_Ai',
            'https://t.me/s/bluearchive3',
            'https://t.me/s/anime_pics_jpg',
            'https://t.me/s/animegirlzzz',
            'https://t.me/s/anime_foto_art_18'
        ];

        $options = [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36',
            ],
            'curl' => [CURLOPT_SSL_VERIFYPEER => false],
            'force_ip_resolve' => 'v4',
        ];
        shuffle($list_blog_name);


        foreach ($list_blog_name as $item_blog) {
            $client = new Client($options);
            $response = $client->request('GET', $item_blog)->getBody()->getContents();
            $crawler = new Crawler($response);

            //Получаем несколько фотографий
            $several_post_list = $crawler->filterXPath("//*[@class='tgme_widget_message_grouped_layer js-message_grouped_layer']");
            $group_count = 0;
            $link_group = '';
            foreach ($several_post_list as $item) {
                try {
                    //Обработка нескольких фотографий
                    $outerImageHTML = $item->ownerDocument->saveHTML($item);
                    $crawler_image_block = new Crawler($outerImageHTML);
                    $list_img_dom = $crawler_image_block->filterXPath("//*[@class='tgme_widget_message_photo_wrap grouped_media_wrap blured js-message_photo']");
                    $img_src = [];
                    foreach ($list_img_dom as $iamge) {
                        $raw_string_image = $iamge->attributes->getNamedItem('style')->nodeValue;
                        $regex = "/.*\(([^)]*)\)/";
                        preg_match($regex, $raw_string_image, $matches);
                        $img = trim($matches[1], "'");
                        $img_src[] = $img;
                        $link_group = $iamge->attributes->getNamedItem('href')->nodeValue;
                        echo "Несколько фотографий в посте";
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage() . "\n";
                    continue;
                }
                $list_img_group[] = $img_src;

                $is_publish = false;
                $is_hidden = false;
                $attachments = [];
                $attachments = [$img_src[0], $img_src];
                echo $img_src[0] . "\n";
                $model = Post::firstOrCreate(
                    ['link' => $link_group,],
                    [
                        'post_id' => 0,
                        'owner_id' => 313,
                        'text' => "",
                        'attachments' => $attachments,
                        'is_publish' => false,
                        'is_hidden' => false,
                        'network' => 'telegramm',
                        'type' => 'photo'
                    ]
                );


                $group_count++;
            }

            //$list_img = [];
            $post_list = $crawler->filterXPath("//*[@class='tgme_widget_message_wrap js-widget_message_wrap']");

            foreach ($post_list as $item_post) {
                $outerHTML = $item_post->ownerDocument->saveHTML($item_post);
                $crawler_block = new Crawler($outerHTML);
                try {
                    $raw_string =  $crawler_block->filterXPath("//*[@class='tgme_widget_message_bubble']/a")->attr('style');
                    $regex = "/.*\(([^)]*)\)/";
                    preg_match($regex, $raw_string, $matches);
                    $list_img = trim($matches[1], "'");
                    $link = $crawler_block->filterXPath("//*[@class='tgme_widget_message_bubble']/a")->attr('href');

                    $is_publish = false;
                    $is_hidden = false;
                    $attachments = [];
                    $attachments = [$list_img, $list_img];
                    echo $list_img . "\n";
                    $model = Post::firstOrCreate(
                        ['link' => $link,],
                        [
                            'post_id' => 0,
                            'owner_id' => 313,
                            'text' => "",
                            'attachments' => $attachments,
                            'is_publish' => false,
                            'is_hidden' => false,
                            'network' => 'telegramm',
                            'type' => 'photo'
                        ]
                    );
                } catch (\Exception $e) {
                    echo $e->getMessage() . "\n";
                    continue;
                }
                echo "break";
            }
        }
    }
}

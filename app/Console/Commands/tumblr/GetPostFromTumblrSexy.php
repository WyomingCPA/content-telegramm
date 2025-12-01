<?php

namespace App\Console\Commands\tumblr;

use Illuminate\Console\Command;

use App\Models\Category;
use App\Models\Post;
use App\Models\Source;
use App\Models\Group;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class GetPostFromTumblrSexy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-post-from-tumblr-sexy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $list_blog_name = [
            'modelcoutureee',
            'raydiofriendlyfires',
            'breelynnxoxoxoxo',
            'toripensa',
            'miki-my-love',
            'idolsgeneration', 
            'baddiesdaily',
            //'curious936p3',
            'calsevt',
            'nice-hills',
            'thepitstops',
            'danypurple-new',
            //'hedonismgirls',
            'littlelesbiansoul',
            'returnoftehfemmies',
            'dieslow',
            'rqdominique',
            'coldstonedreamery',
            'notglockkitty',
            'imawinnerforever',
            '3smilelake',
            'lexxylikes25',
            'neutrinofog',
            'fanta30',
            'warriorbrother',
            'gepetordi2'
        ];

        $options = [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36',
                'authorization' => 'Bearer aIcXSOoTtqrzR8L8YEIOmBeW94c3FmbSNSWAUbxsny9KKx5VFh',
            ],
            'curl' => [CURLOPT_SSL_VERIFYPEER => false],
        ];

        shuffle($list_blog_name);
        foreach ($list_blog_name as $item_blog) {
            $url_parce = "https://api.tumblr.com/v2/blog/$item_blog/posts?fields%5Bblogs%5D=%3Fadvertiser_name%2C%3Favatar%2C%3Fblog_view_url%2C%3Fcan_be_booped%2C%3Fcan_be_followed%2C%3Fcan_show_badges%2C%3Fdescription_npf%2C%3Ffollowed%2C%3Fis_adult%2C%3Fis_member%2Cname%2C%3Fprimary%2C%3Ftheme%2C%3Ftitle%2C%3Ftumblrmart_accessories%2Curl%2C%3Fuuid%2C%3Fask%2C%3Fcan_submit%2C%3Fcan_subscribe%2C%3Fis_blocked_from_primary%2C%3Fis_blogless_advertiser%2C%3Fis_password_protected%2C%3Fshare_following%2C%3Fshare_likes%2C%3Fsubscribed%2C%3Fupdated%2C%3Ffirst_post_timestamp%2C%3Fposts%2C%3Fdescription%2C%3Ftop_tags_all&npf=true&reblog_info=true&context=archive";
            $client = new Client($options);
            $response = $client->request('GET', $url_parce)->getBody()->getContents();

            $json_format_result = json_decode($response, true);
            foreach ($json_format_result['response']['posts'] as $value) {
                $tags = [];
                foreach ($value['tags'] as $item) {
                    $tags[] = $item;
                }
                $messageText = '';
                if (!empty($tags)) {
                    foreach ($tags as $tag) {
                        $tagText = str_replace(' ', '_', $tag);
                        $messageText .= " #$tagText";
                    }
                }
                $list_img = [];
                $list_video = [];
                $type_post = '';
                foreach ($value['content'] as $item) {
                    if ($item['type'] === 'image') {

                        try {
                            $list_img[] = $item['media'][0]['url'];
                            echo $value['post_url'], "\n";
                        } catch (\Exception $e) {
                            echo 'error';
                            continue;
                        }

                        if (exif_imagetype($item['media'][0]['url']) == IMAGETYPE_GIF) {
                            $list_video[] = $item['media'][0]['url'];
                            $type_post = 'video';
                        }
                    }
                    if ($item['type'] === 'video') {
                        $list_img[] = $item['poster'][0]['url'];
                        $list_video[] = $item['url'];
                        $type_post = 'video';
                    }
                }

                if ($type_post === 'video') {
                    $link = $value['post_url'];
                    $post_id = $value['id_string'];
                    $text = $messageText;
                    $type_post = 'video';
                    $network_post = 'tumblr';
                    $attachments = [$list_img[0], $list_video];
                    $is_publish = false;
                    $is_hidden = false;

                    $model = Post::firstOrCreate(
                        ['link' => $link,],
                        [
                            'post_id' => $post_id,
                            'owner_id' => 213,
                            'text' => $text,
                            'attachments' => $attachments,
                            'is_publish' => false,
                            'is_hidden' => false,
                            'network' => 'tumblr',
                            'type' => $type_post
                        ]
                    );
                } else {
                    $link = $value['post_url'];
                    $post_id = $value['id_string'];
                    $text = $messageText;
                    $type_post = 'photo';
                    $network_post = 'tumblr';
                    $attachments = [];
                    try {
                        $attachments = [$list_img[0], $list_img];
                        echo $value['post_url'], "\n";
                    } catch (\Exception $e) {
                        echo 'error';
                        continue;
                    }

                    $is_publish = false;
                    $is_hidden = false;
                    $model = Post::firstOrCreate(
                        ['link' => $link,],
                        [
                            'post_id' => $post_id,
                            'owner_id' => 213,
                            'text' => $text,
                            'attachments' => $attachments,
                            'is_publish' => false,
                            'is_hidden' => false,
                            'network' => 'tumblr',
                            'type' => $type_post
                        ]
                    );
                }
            }
        }

    }
}

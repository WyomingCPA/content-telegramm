<?php

namespace App\Console\Commands\vk;

use Illuminate\Console\Command;

use VK\OAuth\VKOAuth;
use VK\Client\VKApiClient;

use App\Models\Category;
use App\Models\Post;
use App\Models\Source;
use App\Models\Group;

use Illuminate\Support\Str;

class GetPostFromVk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-post-from-vk';

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
        $access_token = env('VK_ACCESS_TOKEN');
        $vk = new VKApiClient();
        //Get All post is status Is_Parce === True
        $array_source = Source::where('is_parce', true)->orderBy('updated_at', 'asc')->get()->toArray();
        shuffle($array_source);
        foreach ($array_source as $item_source) {
            echo $item_source['name'] . "\n";
            try {
                $response = $vk->wall()->get($access_token, [
                    'owner_id'  => $item_source['owner_id'],
                    'count'    => 50,
                ]);
            } catch (\Error $e) {
                echo $e->getMessage();
                continue;
            }

            $posts_image = [];
            foreach ($response['items'] as $item) {
                $img_src = [];
                foreach ($item['attachments'] as $image) {
                    if ($image['type'] == 'photo') {
                        $posts_image = $image['photo']['sizes'];
                        $numbers = array_column($posts_image, "width");
                        $max_value = max($numbers);
                        $filteredMaxSize = array_filter($posts_image, fn($image) => $image["width"] == $max_value);
                        $img_src[] = array_column($filteredMaxSize, "url");
                    }
                }

                $id_post = $item["id"];
                $text_post = $item["text"];

                $list_id_category = [];

                $slug = Group::find($item_source['groups_id'])->slug;

                $model = Category::firstOrCreate(['name' => $slug,],);
                $list_id_category[] = $model->id;
                $link = "wall" . str($item_source['owner_id']) . "_" . str($id_post);
                $model = Post::firstOrCreate(
                    ['link' => $link,],
                    [
                        'post_id' => $id_post,
                        'owner_id' => $item_source['owner_id'],
                        'text' => Str::limit($text_post, 50),
                        'attachments' => $img_src,
                        'is_publish' => false,
                        'is_hidden' => false,
                    ]
                );
                if ($model->wasRecentlyCreated) {
                    $model->categories()->attach($list_id_category);
                    echo $id_post . "\n";
                }
                $model->save();
            }
        }
    }
}

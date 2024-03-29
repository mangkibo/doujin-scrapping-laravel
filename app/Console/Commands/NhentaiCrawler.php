<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class NhentaiCrawler extends Command
{

    protected $signature = 'nhentai:get';

    protected $description = 'Command description';

    private string $baseUrl = "https://i7.nhentai.net/galleries";

    private string $storePath = "doujins/";

    public function handle()
    {
        $doujins = $this->getDoujins();

        foreach ($doujins as $dj) {
            $code = $dj["code"];
            $title =  $dj["title"];

            $folder = $title ?? $code;
        
            $isOk = true;
            $page = 121;
    
            while($isOk) {
    
                $content = $this->getContent($code, $folder, $page);

                if (!$content) {
                    $isOk = false;
                } else {
                    $storeAt = $content["storepath"];
                    $filedata = $content["filedata"];

                    Storage::disk('public')->put($storeAt, $filedata);
                    $this->info("STORE AT : " . $storeAt);
                    $page++;
                }
            }
        }
    }

    private function getContent($code, $folder, $page) {
        $filename = $page.".jpg";

        $url = $this->baseUrl."/".$code."/".$filename;
        $response = Http::get($url);
        $ok = $response->ok();

        if (!$ok) {
            $filename = $page.".png";

            $url = $this->baseUrl."/".$code."/".$filename;
            $response = Http::get($url);
            $ok = $response->ok();

            if (!$ok) {
                $this->error("FILE $url NOT FOUND!");
                return false;
            }
        }

        $storeAt = $this->storePath . $folder . "/" . $filename;
        $fileObject = $response->body();

        $this->info("GET URL : " . $url);

        return [
            "storepath" => $storeAt,
            "filedata" => $fileObject
        ];
    }

    private function getDoujins(): array {
        return [
            [
                "code" => "2314923",
                "title" => "COMIC Shingeki 2022-10 [Digital]"
            ],
        ];
    }
}

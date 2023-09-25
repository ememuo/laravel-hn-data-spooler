<?php

namespace App\Jobs;

use App\Models\story;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

$client = new Client();

class SpoolHNData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
     $response = $client->get('https://hacker-news.firebaseio.com/v0/topstories.json');
     $stories = json_deocde($response->getBody(), true);

     foreach ($stories as $storyId)
      {
        $existingStory = stories::where('id', $storyId)->first();

        if(!existingStory){
        $storyResponse = $client->get("https://hacker-news.firebaseio.com/v0/item/{$storyId}.json");
        $story = json_decode($storyResponse->getBody(), true);

        Redis::hmset('hn:story:{$storyId}', $story);

        $newStory = new Story();
        $newStory->id = $storyId;
        $newStory->save();
        }
      }

        $this->spoolStories();
        $this->spoolComments();
        $this->spoolAuthors();
        $this->spoolJobs();
        $this->spoolPolls();
        $this->spoolAsks();
    }

    private function spoolStories()
    {
        try{
        $response = Http::get('https://hacker-news.firebaseio.com/v0/newstories.json');
        $stories = $response->json();

        foreach($stories as $story) {
            DB::table('stories')->insert([
                'title' => $story['title'],
                'id' => $story['id'],
                'url'   => $story['url'],
                'category' => $story['category'],
                'score' => $story['score'],
                'by' => $story['by'],
                'time' => $story['time'],
                'descendants' => $story['descendants'],
                'type' => $story['type']
            ]);
        }
      }catch (\Exception $e){
        Log::error('Error spooling Stories: ' . $e->getMessage());
      }
    }

    private function spoolComments()
    {
        try{
            $response = Http::get('https://hacker-news/firebaseio.com/v0/item/{storyid}.json');
            $comments = $response->json();
    
            foreach($comments as $comment) {
                DB::table('comments')->insert([
                    'text' => $comment['text'],
                    'id' => $comment['id'],
                    'parent'   => $comment['parent'],
                    'kids' => $comment['kids'],
                    'by' => $comment['by'],
                    'time' => $comment['time'],
                    'type' => $comment['type']
                ]);
            }
          }catch (\Exception $e){
            Log::error('Error spooling Comments: ' . $e->getMessage());
          }
        }

        private function spoolAsks()
        {
            try{
                $response = Http::get('https://hacker-news/firebaseio.com/v0/item/{storyid}.json');
                $asks = $response->json();
        
                foreach($asks as $ask) {
                    DB::table('asks')->insert([
                        'text' => $ask['text'],
                        'id'   => $ask['id'],
                        'kids' => $ask['kids'],
                        'descendants' => $ask['descendants'],
                        'by' => $ask['by'],
                        'time' => $ask['time'],
                        'type' => $ask['type'],
                        'score' => $ask['score'],
                        'title' => $ask['title']
                    ]);
                }
              }catch (\Exception $e){
                Log::error('Error spooling Asks: ' . $e->getMessage());
              }
            }

            private function spoolJobs()
            {
                try{
                    $response = Http::get('https://hacker-news/firebaseio.com/v0/item/{id}.json');
                    $jobs = $response->json();
            
                    foreach($jobs as $job) {
                        DB::table('jobs')->insert([
                            'text' => $jov['text'],
                            'id'   => $job['id'],
                            'url' => $job['url'],
                            'by' => $job['by'],
                            'time' => $job['time'],
                            'type' => $job['type'],
                            'score' => $job['score'],
                            'title' => $job['title']
                        ]);
                    }
                  }catch (\Exception $e){
                    Log::error('Error spooling Jobss: ' . $e->getMessage());
                  }
                }

                private function spoolPolls()
                {
                    try{
                        $response = Http::get('https://hacker-news/firebaseio.com/v0/item/{id}.json');
                        $polls = $response->json();
                
                        foreach($polls as $poll) {
                            DB::table('polls')->insert([
                                'text' => $poll['text'],
                                'id'   => $poll['id'],
                                'kids' => $poll['kids'],
                                'descendants' => $poll['descendants'],
                                'by' => $poll['by'],
                                'time' => $poll['time'],
                                'type' => $poll['type'],
                                'score' => $poll['score'],
                                'title' => $poll['title']
                            ]);
                        }
                      }catch (\Exception $e){
                        Log::error('Error spooling Polls: ' . $e->getMessage());
                      }
                    }
    }

   




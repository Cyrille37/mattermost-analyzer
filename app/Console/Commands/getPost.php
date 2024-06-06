<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MatterMost\Channel;
use App\Models\MatterMost\ChannelHasMember;
use App\Models\MatterMost\ChannelStat;
use App\Models\MatterMost\MattermostModel;
use App\Models\MatterMost\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Pnz\MattermostClient\ApiClient;
use Pnz\MattermostClient\HttpClientConfigurator;
use \Pnz\MattermostClient\Model\Team\Team;
use \Pnz\MattermostClient\Exception\Domain\PermissionDeniedException;

class getPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mma:getPost'
        . ' {id}'
        ;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a post by Id.';

    /**
     * @var \Pnz\MattermostClient\ApiClient
     */
    protected $mm;
    protected $me;

    public function handle()
    {
        $post_id = $this->argument('id');

        $endpoint = Config::get('mattermost.servers.default.api');
        $username = Config::get('mattermost.servers.default.login');
        $password = Config::get('mattermost.servers.default.password');
        $this->info('Connecting at: ' . $endpoint);

        $configurator = (new HttpClientConfigurator())
            ->setEndpoint($endpoint)
            ->setCredentials($username, $password);
        $this->mm =  ApiClient::configure($configurator);

        $this->me = $this->mm->users()->getUserById('me');
        //$this->line( var_export($user,true) );
        $this->info('Logged in as: ' . $this->me->getUsername());

        $post = $this->mm->posts()->getPost($post_id);

        echo var_export($post,true),"\n";
    }

}
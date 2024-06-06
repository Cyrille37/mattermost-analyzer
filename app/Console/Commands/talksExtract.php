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

class talksExtract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mma:talksExtract'
        . ' {--channels=}'
        . ' {--start_at=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Browse some Mattermost channels to extract talks.';

    protected $mm;
    protected $me;

    protected $sleepµ = 1000 * 100;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $selectedChannels = explode(',', $this->option('channels'));

        if (!is_array($selectedChannels) || count($selectedChannels) == 0 || (count($selectedChannels) == 1 && $selectedChannels[0] == ''))
            throw new \InvalidArgumentException('channels are mandatory, separate them with a comma.');
        $start_at = new Carbon($this->option('start_at'));
        if (!$start_at->isValid())
            throw new \InvalidArgumentException('start_at non compris');
        //echo 'start_at unix: ',$start_at->unix(),', unix_ms: ',$start_at->getPreciseTimestamp(3),"\n";

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

        /**
         * 
         * @var \Pnz\MattermostClient\Model\Team\Team $team
         */
        $team = $this->mm->teams()->getTeamByName('artefacts');
        //$this->line( var_export($team,true) );
        $this->info('Using team: ' . $team->getDisplayName());

        $this->info('Loading team members...');
        $this->loadMembers($team);

        $this->info('Loading team channels...');
        $this->loadChannels($team, $selectedChannels);

        foreach ($selectedChannels as $channelName) {

            $this->info('Loading talks for channel ' . $channelName . '...');
            $this->loadTalks(Channel::where('name', $channelName)->first(), $start_at);
        }
    }

    /**
     * https://api.mattermost.com/v4/#tag/posts/operation/GetPostsForChannel
     * 
     * Failed: La pagination "de base" ne fonctionne pas :-(
     * Failed "since": Provide a non-zero value in Unix time milliseconds to select posts modified after that time
     */
    protected function loadTalks(Channel $channel, Carbon $start_at)
    {
        $posts = $this->mm->channels()->getChannelPosts($channel->id, ['per_page' => 1]);
        if ($posts->count() == 0) {
            $this->comment("\n" . 'Empty channel');
            return;
        }
        // Il peut y avoir plusieurs posts si le dernier est une réponse (root_id).
        /*if ($posts->count() > 1) {
            echo var_export($posts,true),"\n";
            echo 'order: ', var_export($posts->getOrder(),true),"\n";
            echo 'current id: ', $posts->current()->getId(),"\n";
            throw new \RuntimeException('Argh!');
        }*/
        // Par contre, dans ce cas le order n'en contient qu'un
        if (count($posts->getOrder()) > 1) {
            throw new \RuntimeException('Argh!');
        }

        $posts_count = 1;
        /**
         * @var \Pnz\MattermostClient\Model\Post\Post $apiPost
         */
        $apiPost = $posts->current();
        /**
         * @var \App\Models\MatterMost\Post $post
         */
        $post = Post::firstOrCreateFromApi($apiPost);
        $before = $apiPost->getId();

        $seen = [];
        /**
         * @var Carbon $oldest_at
         */
        $oldest_at = null; $newest_at = null ;
        $page = 0 ;
        do {
            $posts = $this->mm->channels()->getChannelPosts($channel->id, [
                'page'=>$page, 'per_page' => 250, 'before' => $before,
            ]);
            $posts_count += $posts->count();
            //$this->comment("\t" . $posts_count);
            foreach ($posts->getItems() as $apiPost) {
                //if (array_key_exists($apiPost->getId(), $seen))
                //    throw new \RuntimeException('DOUBLON!');
                $seen[$apiPost->getId()] = true;

                $post = Post::firstOrCreateFromApi($apiPost);

                $created_at = Carbon::createFromTimestampMs($apiPost->getCreateAt());
                if ((!$oldest_at) || ($oldest_at > $created_at)) {
                    $oldest_at = $created_at;
                    //$before = $apiPost->getId();
                }
                if ((!$newest_at) || ($newest_at < $created_at)) {
                    $newest_at = $created_at;
                }
 
            }
            $page ++ ;
            usleep($this->sleepµ);

        } while ($posts->count() > 0);

        $this->comment('Posts count: ' . $posts_count
        . ', oldest: ' . ($oldest_at ? $oldest_at->toDateTimeString() : 'null')
        . ', newest_at: ' . ($newest_at ? $newest_at->toDateTimeString() : 'null')
    );
    }

    /**
     * Load & store all public channels for the $team.
     */
    protected function loadChannels(Team $team, array $selectedChannels = [])
    {
        $page = 0;
        $items_per_page = 100;
        $channels_count = 0;
        do {
            $channelsApi = $this->mm->teams()->getTeamPublicChannels($team->getId(), ['page' => $page, 'per_page' => $items_per_page]);
            //$this->line( var_export($channelsApi,true) );

            /**
             * @var \Pnz\MattermostClient\Model\Channel\Channel $item
             */
            foreach ($channelsApi->getItems() as $item) {
                /*
                $this->line( var_export($item,true) );
                Pnz\MattermostClient\Model\Channel\Channel::__set_state(array(
                   'data' => 
                  array (
                    'id' => 'offxpzoy1tdg8mrjkmxjxisr1e',
                    'create_at' => 1577800892328,
                    'update_at' => 1577800892328,
                    'delete_at' => 0,
                    'team_id' => 'h4fwwegraidd9c6w9ecywbfj7c',
                    'type' => 'O',
                    'display_name' => 'AO CIDFF-RCVL motion-design',
                    'name' => 'ao-cidff-rcvl-motion-design',
                    'header' => '',
                    'purpose' => 'Équipe création motion-design pour appel d\'offre FR-CIDFF-RCVL',
                    'last_post_at' => 1578044977558,
                    'total_msg_count' => 15,
                    'extra_update_at' => 0,
                    'creator_id' => 'mxzyjrjgjbyrxneg3ycrbndo7c',
                  ),
                ))
                 */

                $channels_count++;

                /**
                 * @var \App\Models\MatterMost\Channel $channel
                 */
                $channel = Channel::firstOrCreateFromApi($item);
                //$this->comment("\t".$item->getName() );
                //$this->line( var_export($channel,true) );

                if ((count($selectedChannels) == 0) || in_array($item->getName(), $selectedChannels)) {

                    try {
                        //
                        // Retrieve channel's stats
                        //
                        $channelStat = new ChannelStat([
                            'channel_id' => $channel->id,
                            'last_post_at' => MattermostModel::mmDateToCarbon($item->getLastPostAt()),
                            'posts_count' => $item->getTotalMsgCount(),
                        ]);
                        $stats = $this->mm->channels()->getChannelStats($channel->id);
                        //$this->line( 'Channel "'.$channel->name.'" / "'.$channel->display_name.'" stats: '.var_export($stats,true));
                        $channelStat->members_count = $stats->getMemberCount();
                        $channelStat->save();
                        //
                        // Retrieve channel's members
                        //
                        $this->updateChannelMembers($channel);
                    } catch (PermissionDeniedException $ex) {
                        $this->error('Channel "' . $item->getName() . '": ' . $ex->getMessage());
                    }
                }

                usleep($this->sleepµ);
            }

            $page++;
        } while ($channelsApi->count() == $items_per_page);

        $this->comment('Public channels count: ' . $channels_count);
    }


    protected function loadMembers(Team $team)
    {
        $page = 0;
        $items_per_page = 100;
        $members_count = 0;
        do {
            $membersApi = $this->mm->teams()->getTeamMembers(
                $team->getId(),
                ['page' => $page, 'per_page' => $items_per_page]
            );

            /**
             * @var \Pnz\MattermostClient\Model\Team\TeamMember $item
             */
            foreach ($membersApi->getItems() as $teamMember) {
                /**
                 * @var \Pnz\MattermostClient\Model\User\User $apiMember
                 */
                $apiMember = $this->mm->users()->getUserById($teamMember->getUserId());
                $members_count++;

                /*
                if( $member->getId() == 'waoukshaejgjprqhstoardmeyr' )
                    $this->line( var_export($member,true) );
                Pnz\MattermostClient\Model\User\User::__set_state(array(
                   'data' => 
                  array (
                    'id' => 'waoukshaejgjprqhstoardmeyr',
                    'create_at' => 1491228547277,'update_at' => 1519340573277,'delete_at' => 0,
                    'roles' => 'system_user',
                    'allow_marketing' => NULL,
                    'locale' => 'en',
                    'username' => 'phil_3d',
                    'auth_data' => '',
                    'email' => '',
                    'email_verified' => NULL,
                    'notify_props' => NULL,
                    'last_password_update' => NULL,
                    'last_name' => '',
                    'nickname' => '','first_name' => '',
                  ),
                ))
                */

                /**
                 * @var \App\Models\MatterMost\Member $member
                 */
                $member = \App\Models\MatterMost\Member::firstOrCreateFromApi($apiMember);
                /*if( $member->username == 'bot-artefacts')
                    $this->line( var_export($apiMember,true) );
                */
            }
            $page++;
        } while ($membersApi->count() == $items_per_page);

        $this->comment('Members count: ' . $members_count);
    }

    protected function updateChannelMembers(\App\Models\MatterMost\Channel $channel)
    {
        // Store current membership state in database.

        $previously = $channel->getMemberships();

        // Processing channel's members

        $page = 0;
        $items_per_page = 100;
        do {
            $members = $this->mm->channels()->getChannelMembers($channel->id, ['page' => $page, 'per_page' => $items_per_page]);
            /**
             * @var \Pnz\MattermostClient\Model\Channel\ChannelMember $item
             */
            foreach ($members->getItems() as $item) {
                /*
                if( $item->getUserId() == 'waoukshaejgjprqhstoardmeyr' )
                    $this->line( var_export($item,true) );
                Pnz\MattermostClient\Model\Channel\ChannelMember::__set_state(array(
                   'data' => 
                  array (
                    'channel_id' => '4furei7zrbrcbcp51xexhugsja',
                    'user_id' => 'waoukshaejgjprqhstoardmeyr',
                    'roles' => 'channel_user channel_admin',
                    'last_viewed_at' => 0,
                    'msg_count' => 234, <= Ce n'est pas le nombre de messages du member !!
                    'mention_count' => 2,
                    'notify_props' => 
                    array (
                      'desktop' => 'default',
                      'email' => 'default',
                      'ignore_channel_mentions' => 'default',
                      'mark_unread' => 'all',
                      'push' => 'default',
                    ),
                    'last_update_at' => 1574408652355,
                  ),
                ))
                */

                /*
                if( in_array( $item->getUserId(), ['mxzyjrjgjbyrxneg3ycrbndo7c', '5apmguu3utdrxq8icuznxr5pcr','qnfkf7h6s7dp8biu3r8m5ksmbe']) )
                {
                    $this->line( $channel->display_name.' '.$item->getMsgCount());
                }
                */

                $prev = $previously->where('member_id', $item->getUserId());

                if ($prev->count() > 0) {
                    // Déjà abonné au channel
                    $prev = $prev->first();
                    // pour qu'il ne soit pas dans les $noMoreMembers
                    $prev->seen = true;
                    // On créé une ligne uniquement pour mettre à jour "is_member", "msg_count" ou "mention_count".
                    if (
                        $prev->is_member == 0
                        //|| ($prev->msg_count != $item->getMsgCount())
                        //|| ($prev->mention_count != $item->getMentionCount())
                    ) {
                        ChannelHasMember::create([
                            'channel_id' => $channel->id,
                            'member_id' => $item->getUserId(),
                            'roles' => $item->getRoles(),
                            //'msg_count' => $item->getMsgCount(),
                            'mention_count' => $item->getMentionCount(),
                            'is_member' => 1,
                        ]);
                    }
                } else {
                    // Un nouveau dans le channel
                    ChannelHasMember::create([
                        'channel_id' => $channel->id,
                        'member_id' => $item->getUserId(),
                        'roles' => $item->getRoles(),
                        //'msg_count' => $item->getMsgCount(),
                        'mention_count' => $item->getMentionCount(),
                        'is_member' => 1,
                    ]);
                }
            } // foreach $members->getItems()

            $page++;
        } while ($members->count() == $items_per_page);

        //
        // Processing no more members
        // ils sont dans $previously mais pas vu lors de l'itération getChannelMembers()
        //

        /*
        if( $channel->id == '8zczr5aywb8ziq3onq96xm1j5y')
        {
        $this->line( '$previously: '.$previously->count() );
        $prev = $previously->where( 'member_id', 'mxzyjrjgjbyrxneg3ycrbndo7c' );
        $this->line('$previously has Cyrille37: '.$prev->count() );
        }
        */

        $noMoreMembers = $previously->filter(function ($item, $key) {
            return !isset($item->seen);
        });

        /*
        if( $channel->id == '8zczr5aywb8ziq3onq96xm1j5y')
        {
        $this->line( '$noMoreMembers: '.$noMoreMembers->count() );
        $prev = $noMoreMembers->where( 'member_id', 'mxzyjrjgjbyrxneg3ycrbndo7c' );
        $this->line('$noMoreMembers has Cyrille37: '.$prev->count() );
        }
        */

        foreach ($noMoreMembers->all() as $item) {
            ChannelHasMember::create([
                'channel_id' => $channel->id,
                'member_id' => $item->member_id,
                'roles' => '',
                'is_member' => 0,
            ]);
        }
    }
}

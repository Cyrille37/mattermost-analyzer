<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Pnz\MattermostClient\ApiClient;
use Pnz\MattermostClient\HttpClientConfigurator ;
use App\Models\MatterMost\ChannelStat;
use App\Models\MatterMost\ChannelHasMember ;
use Illuminate\Support\Facades\DB;

class browseServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mma:browseServer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Browse the Mattermost server to gather data.';

    protected $mm ;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $endpoint = Config::get('mattermost.servers.default.api');
        $username = Config::get('mattermost.servers.default.login');
        $password = Config::get('mattermost.servers.default.password');
        $this->info( 'Connecting at: '.$endpoint );

        $start_at = microtime(true);

        $configurator = (new HttpClientConfigurator())
            ->setEndpoint($endpoint)
            ->setCredentials($username, $password);
        $this->mm =  ApiClient::configure($configurator);

        $user = $this->mm->users()->getUserById('me');
        //$this->line( var_export($user,true) );
        $this->info( 'Logged in as: '.$user->getUsername() );

        //$r = $this->mm->server()->getRoles();
        //$this->line( var_export($r,true ) );

        /**
         * 
         * @var \Pnz\MattermostClient\Model\Team\Team $team
         */
        $team = $this->mm->teams()->getTeamByName('artefacts');
        //$this->line( var_export($team,true) );
        $this->info( 'Using team: '.$team->getDisplayName() );

        $this->comment( 'Initialisation took '. (microtime(true)-$start_at).' seconds.' );

        $start1_at = microtime(true);

        $this->info('Loading team members...');
        $this->loadMembers( $team );

        $this->comment( 'Loading team members took '. (microtime(true)-$start1_at).' seconds.' );

        $start1_at = microtime(true);

        $this->info('Loading team channels...');
        $this->loadChannels( $team );

        $this->comment( 'Loading team channels took '. (microtime(true)-$start1_at).' seconds.' );

        $this->comment( 'All done in '. (microtime(true)-$start_at).' seconds.' );
    }

    protected function loadChannels( \Pnz\MattermostClient\Model\Team\Team $team )
    {
        $me = $this->mm->users()->getUserById('me');

        $page = 0 ;
        $items_per_page = 100 ;
        $channels_count = 0 ;
        do
        {
            $channelsApi = $this->mm->teams()->getTeamPublicChannels( $team->getId(), ['page'=>$page,'per_page'=>$items_per_page]);
            //$this->line( var_export($channelsApi,true) );
            
            /**
             * @var \Pnz\MattermostClient\Model\Channel\Channel $item
             */
            foreach( $channelsApi->getItems() as $item )
            {
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

                $channels_count ++ ;

                /**
                 * @var \App\Models\MatterMost\Channel $channel
                 */
                $channel = \App\Models\MatterMost\Channel::firstOrCreate(
                    [
                        'id' => $item->getId()
                    ],
                    [
                        'id' => $item->getId(),
                        'name' => $item->getName(),
                        'display_name'=> $item->getDisplayName(),
                        'header'=> $item->getHeader(),
                        'purpose'=> $item->getPurpose(),
                        'create_at'=> $item->getCreateAt(),
                        'delete_at'=> $item->getDeleteAt(),
                        'creator_id'=> $item->getCreatorId(),
                    ]
                );
                //$this->line( var_export($channel,true) );

                //
                // Before reading a channel, current user must be a channel's member.
                //

                $this->mm->channels()->addChannelMember($channel->id, $me->getId() );
                //$this->line( 'Channel "'.$channel->name.'" / "'.$channel->display_name.'" : "me" added');

                //
                // Retreive channel's stats
                //

                $channelStat = new ChannelStat([
                    'channel_id' => $channel->id,
                    'last_post_at' => $item->getLastPostAt() ,
                    'posts_count' => $item->getTotalMsgCount(),
                ]);

                try
                {
                    $stats = $this->mm->channels()->getChannelStats( $channel->id );
                    /*
                    Channel "ao-saint-jean-debraye" / "AO  saint jean deBraye" stats: Pnz\MattermostClient\Model\Channel\ChannelStats::__set_state(array(
                       'data' => 
                      array (
                        'channel_id' => 'tc31tp3gn3yeuk5thrz3a5mq4e',
                        'member_count' => 1,
                      ),
                    ))
                     */
                    //$this->line( 'Channel "'.$channel->name.'" / "'.$channel->display_name.'" stats: '.var_export($stats,true));

                    $channelStat->members_count = $stats->getMemberCount() ;
                }
                catch( \Pnz\MattermostClient\Exception\Domain\PermissionDeniedException $ex )
                {
                    $this->warn('Channel "'.$channel->name.'" / "'.$channel->display_name.'" :  getChannelStats() permissionDenied');
                }

                $channelStat->save();

                //
                // Retrieve channel's members
                //

                $this->updateChannelMembers($channel);

            }

            $page ++ ;
        }
        while( $channelsApi->count() == $items_per_page );
        $this->info('Public channels count: '.$channels_count );
        
    }

    protected function updateChannelMembers( \App\Models\MatterMost\Channel $channel )
    {

        // Store current membership state in database.

        $previously = $channel->getMemberships();

        /*if( $channel->id == '8zczr5aywb8ziq3onq96xm1j5y' )
        {
            $this->line( $channel->id.' '. print_r($previously,true) );
            //exit();
        }*/

        // Processing channel's members

        $page = 0 ;
        $items_per_page = 100 ;
        do
        {
            $members = $this->mm->channels()->getChannelMembers( $channel->id, ['page'=>$page,'per_page'=>$items_per_page] );

            /**
             * @var \Pnz\MattermostClient\Model\Channel\ChannelMember $item
             */
            foreach( $members->getItems() as $item )
            {
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

                $prev = $previously->where( 'member_id', $item->getUserId() );

                if( $prev->count() > 0 )
                {
                    // Déjà abonné au channel

                    $prev = $prev->first() ;

                    // pour qu'il ne soit pas dans les $noMoreMembers
                    $prev->seen = true ;

                    // On créé une ligne uniquement pour mettre à jour "is_member", "msg_count" ou "mention_count".
                    if( $prev->is_member == 0
                        //|| ($prev->msg_count != $item->getMsgCount())
                        //|| ($prev->mention_count != $item->getMentionCount())
                        )
                    {
                        ChannelHasMember::create([
                            'channel_id' => $channel->id,
                            'member_id' => $item->getUserId(),
                            'roles' => $item->getRoles(),
                            //'msg_count' => $item->getMsgCount(),
                            'mention_count' => $item->getMentionCount(),
                            'is_member' => 1,
                        ]);
                    }
                }
                else 
                {
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

            }// foreach $members->getItems()

            $page ++ ;
        }
        while( $members->count() == $items_per_page );

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

        $noMoreMembers = $previously->filter(function ($item, $key)
        {
            return !isset($item->seen) ;
        });

        /*
        if( $channel->id == '8zczr5aywb8ziq3onq96xm1j5y')
        {
        $this->line( '$noMoreMembers: '.$noMoreMembers->count() );
        $prev = $noMoreMembers->where( 'member_id', 'mxzyjrjgjbyrxneg3ycrbndo7c' );
        $this->line('$noMoreMembers has Cyrille37: '.$prev->count() );
        }
        */

        foreach( $noMoreMembers->all() as $item )
        {
            ChannelHasMember::create([
                'channel_id' => $channel->id,
                'member_id' => $item->member_id ,
                'roles' => '',
                'is_member' => 0,
            ]);
        }
    }

    protected function loadMembers( \Pnz\MattermostClient\Model\Team\Team $team )
    {
        $page = 0 ;
        $items_per_page = 100 ;
        $members_count = 0 ;
        do
        {
            $membersApi = $this->mm->teams()->getTeamMembers( $team->getId(),
                ['page'=>$page,'per_page'=>$items_per_page]
            );
            
            /**
             * @var \Pnz\MattermostClient\Model\Team\TeamMember $item
             */
            foreach( $membersApi->getItems() as $teamMember )
            {
                /**
                 * @var \Pnz\MattermostClient\Model\User\User $member
                 */
                $member = $this->mm->users()->getUserById( $teamMember->getUserId() );
                $members_count ++ ;

                /*
                if( $member->getId() == 'waoukshaejgjprqhstoardmeyr' )
                    $this->line( var_export($member,true) );
                Pnz\MattermostClient\Model\User\User::__set_state(array(
                   'data' => 
                  array (
                    'id' => 'waoukshaejgjprqhstoardmeyr',
                    'create_at' => 1491228547277,
                    'update_at' => 1519340573277,
                    'delete_at' => 0,
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
                    'nickname' => '',
                    'first_name' => '',
                  ),
                ))
                 */

                /**
                 * @var \App\Models\MatterMost\Member $member
                 */
                $member = \App\Models\MatterMost\Member::firstOrCreate(
                    [
                        'id' => $member->getId()
                    ],
                    [
                        'id' => $member->getId(),
                        'roles' => $member->getRoles(),
                        'username' => $member->getUsername(),
                        'nickname' => $member->getNickname(),
                        'create_at' => $member->getCreateAt(),
                        'delete_at' => $member->getDeleteAt(),
                    ]
                    );
                //if( $member->username == 'philippe.cozian')
                //    $this->line( $member->create_at .' '
                //      . Carbon::createFromTimestampMs( $member->create_at )->toDateTimeString() );
            }
            $page ++ ;
        }
        while( $membersApi->count() == $items_per_page );

        $this->info('Members count: '.$members_count );

    }

}

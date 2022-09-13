<?php

namespace App\Console\Commands;

use App\Models\UserPoints;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QueryRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:ranking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $week = date('w') == 0 ? 7 : date('w');
        $time = date('Y-m-d', strtotime('today -' . ($week - 1) . 'day'));
//        $time = date('Y-m-d', time());
        $start_time = date('Y-m-d 00:00:00', strtotime("$time - 7 days"));  //上周开始日期
        $end_time = date('Y-m-d 23:59:59', strtotime("$time - 1 days"));  //上周结束日期
        UserPoints::where('created_at', '>=', $start_time)
            ->where('created_at', '<=', $end_time)
            ->with('user')
            ->selectRaw(" sum(points) as sumpoints, user_id")
            ->groupBy('user_id')
            ->orderBy('sumpoints', 'desc')
            ->limit(10)
            ->get()->each(function ($query, $i) {
                Log::info('用户 ' . $query['user']['name'] . ' 上周排名 ' . ($i + 1) . ' 获得了 ' . $query['sumpoints'] . ' 分');
            });


    }




}

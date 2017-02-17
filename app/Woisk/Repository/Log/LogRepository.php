<?php
/**
 * Created by PhpStorm.
 * User: woisk
 * Date: 2017/2/13 0013
 * Time: 11:28
 */

namespace App\Woisk\Repository\Log;


use App\Woisk\Models\AccountLoginLog;
use App\Woisk\Models\AccountSignupLog;
use Carbon\Carbon;

class LogRepository
{

    /**
     * 注册记录
     * @param $account
     * @param $ip
     * @param $browser
     * @param $terminal
     * @return mixed
     */
    public function register($account, $ip, $browser, $terminal)
    {
        return AccountSignupLog::create([
            'uid' => $account->uid,
            'time' => Carbon::now(),
            'com_ip_id' => $ip->id,
            'com_terminal_id' => $terminal->id,
            'com_browser_id' => $browser->id
        ]);


    }

    public function login($uid,$login_name,$terminal,$ip,$browser)
    {
        return AccountLoginLog::create([
            'uid'=>$uid,
            'time'=> Carbon::now(),
            'type'=>$login_name,
            'com_terminal_id'=>$terminal->id,
            'com_ip_id'=>$ip->id,
            'com_browser_id'=>$browser->id
        ]);
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: woisk
 * Date: 2017/2/15 0015
 * Time: 14:24
 */

namespace App\Woisk\Repository\Email;


class AuthEmailRepository
{
    public function upAccount($uid,$email)
    {
        $ema = Account::find($uid)->update(['email' => $email]);
        return response()->json($ema);
    }
}
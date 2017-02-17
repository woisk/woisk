<?php
/**
 * Created by PhpStorm.
 * User: woisk
 * Date: 2017/2/15 0015
 * Time: 14:25
 */

namespace App\Woisk\Service;


use App\Mail\BindEmail;
use App\Woisk\Repository\Account\AccountRepository;
use App\Woisk\Repository\Email\AuthEmailRepository;
use App\Woisk\Validation\Email\EmailValidation;
use Carbon\Carbon;

class AuthEmailService
{
    protected $validation;
    protected $email;
    protected $account;

    public function __construct(EmailValidation $validation, AuthEmailRepository $email, AccountRepository $account)
    {
        $this->validation = $validation;
        $this->email = $email;
        $this->account = $account;
    }


    /**
     * 发送邮件绑定邮箱
     * @param $request
     * @return mixed
     */
    public function sendEmail($request)
    {
        $payload = \JWTFactory::sub(\JWTAuth::parseToken()->toUser()->uid)
            ->email($request->get('email'))
            ->setTTL(60 * 48)
            ->make();
        $token = \JWTAuth::encode($payload);

        $date = [
            'uid' => \JWTAuth::parseToken()->toUser()->uid,
            'username' => \JWTAuth::parseToken()->toUser()->username,
            'email' => $request->get('email'),
            'token' => $token
        ];
        return \Mail::to($request->get('email'))->send(new BindEmail($date));
    }

    /**
     * 验证邮件更新邮箱到账号
     * @param $request
     * @return mixed
     */
    public function authEmail($request)
    {
        $uid = \JWTAuth::parseToken()->toUser()->uid;
        $email = \JWTAuth::getPayload()->get('email');

        if ($this->account->upAccountEmail($uid, $email)) {
            \JWTAuth::invalidate();
            return response('true')->setStatusCode(200);
        }
    }


//    public function authEmailUpAccountEmail($request)
//    {
//        $uid = \JWTAuth::parseToken()->toUser()->uid;
//        $email = \JWTAuth::getPayload()->get('email');
//
//        if ($this->account->upAccountEmail($uid, $email)) {
//            \JWTAuth::invalidate();
//            return response('true')->setStatusCode(200);
//        }
//    }


    public function authEmailUpAccountPassword($request)
    {
        if ($this->account->upAccountPassword(\JWTAuth::parseToken()->toUser()->uid, $request->get('password'))) {
            \JWTAuth::invalidate();
            return response('true')->setStatusCode(200);
        }
    }

}
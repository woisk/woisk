<?php
namespace App\Woisk\Service;

use App\Woisk\Repository\Account\AccountRepository;
use App\Woisk\Repository\Terminal\BrowserRepository;
use App\Woisk\Repository\Terminal\IPRepository;
use App\Woisk\Repository\Terminal\TerminalRepository;
use App\Woisk\Repository\Log\LogRepository;
use App\Woisk\Validation\Account\AccountValidation;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


/**
 * Created by PhpStorm.
 * User: woisk
 * Date: 2017/2/13 0013
 * Time: 10:31
 */
class AccountService
{
    protected $validation;
    protected $account;
    protected $ip;
    protected $browser;
    protected $terminal;
    protected $log;

    /**
     * AccountService constructor.
     * @param AccountValidation $validation
     * @param AccountRepository $account
     * @param IPRepository $ip
     * @param BrowserRepository $browser
     * @param TerminalRepository $terminal
     * @param LogRepository $log
     */
    public function __construct(AccountValidation $validation, AccountRepository $account,
                                IPRepository $ip, BrowserRepository $browser, TerminalRepository $terminal, LogRepository $log)
    {
        $this->account = $account;
        $this->validation = $validation;
        $this->ip = $ip;
        $this->browser = $browser;
        $this->terminal = $terminal;
        $this->log = $log;
    }

    /**
     * 注册
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        //验证
        $validation = $this->validation->accountCreate($request);
        if ($validation->fails()) {
            return response($validation->messages())->setStatusCode(422);
        }
        //保存账号信息
        $account = $this->account->create($request->all());
        //终端
        $ip = $this->ip->firstOrCreate($request);
        $browser = $this->browser->firstOrCreate($request);
        $terminal = $this->terminal->firstOrCreate($request);
        //注册记录
        $this->log->register($account, $ip, $browser, $terminal);
        //登陆
        return redirect()->route('account.login', ['login_name' => $request->get('username'), 'password' => $request->get('password')]);

    }

    /**
     * 登录
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        //验证
        $validation = $this->validation->accountLogin($request);
        if ($validation->fails()) {
            return response($validation->messages())->setStatusCode(422);
        }

        //转换登录名
        $login_name = $this->account->GetFilter($request->get('login_name'));

        //账号状态
        if (empty($this->account->accountState($login_name, $request))) {

            //登录
            $login = [
                $login_name => $request->get('login_name'),
                'password' => $request->get('password')];
            try {
                // attempt to verify the credentials and create a token for the user
                if (!$token = JWTAuth::attempt($login)) {
                    return response()->json(['error' => '密码错误'], 401);
                }
            } catch (JWTException $e) {
                // something went wrong whilst attempting to encode the token
                return response()->json(['error' => '服务器开小差了，请稍后重试！'], 500);
            }

            //登录记录
            $ip = $this->ip->firstOrCreate($request);
            $browser = $this->browser->firstOrCreate($request);
            $terminal = $this->terminal->firstOrCreate($request);

            //注册记录
            $this->log->login(auth()->user()->uid, $login_name, $ip, $browser, $terminal);

            //登录次数
            $this->account->loginCount(auth()->user()->uid);

            //登录成功返回信息并设置cookie
            return response()->json(['token' => $token, 'username' => auth()->user()->username, 'uid' => auth()->user()->uid])
                ->cookie('token', $token)
                ->cookie('username', auth()->user()->username)
                ->cookie('uid', auth()->user()->uid)
                ->cookie('lostatus', 1);
        }
        return $this->account->accountState($login_name, $request);

    }


    /**
     * 验证token
     * @param Request $request
     * @return mixed
     */
    public function getAuthenticatedUser(Request $request)
    {
        $token = JWTAuth::parseToken()->authenticate();
        return response()->json($token);
    }


    /**
     * 刷新Token
     * @param Request $request
     * @return mixed
     */
    public function refreshToken(Request $request)
    {
        $token = JWTAuth::refresh();
        return response()->json($token)->cookie('token', $token);

    }


}
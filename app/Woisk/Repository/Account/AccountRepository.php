<?php
namespace App\Woisk\Repository\Account;

use App\Woisk\Models\Account;

/**
 * Created by PhpStorm.
 * User: woisk
 * Date: 2017/2/13 0013
 * Time: 10:33
 */
class AccountRepository
{

    /**
     * 登录名判断
     * 参数 login_name  $filter
     *
     * return $filter
     */
    public function GetFilter($filter)
    {
        $login = $filter;
        if (filter_var($login, FILTER_VALIDATE_INT)) {

            return 'uid';

        } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {

            return 'email';

        }
        return 'username';

    }

    /**
     * 创建账号
     * @param array $account
     * @return mixed
     */
    public function create(array $account)
    {
        return Account::create([
            'username' => $account['username'],
            'password' => bcrypt($account['password'])
        ]);
    }

    /**
     * 账号状态
     * @param $login_name
     * @param $request
     * @return bool
     */
    public function accountState($login_name, $request)
    {
        $account = Account::where($login_name, $request->get('login_name'))->first();
        if (empty($account)){
            return response()->json('账号不存在')->setStatusCode(422);
        }
        if ($account->state != 1) {
            return response()->json('您的账号已停用，请联系管理员！')->setStatusCode(422);
        }
    }

    //登录次数
    public function loginCount($uid)
    {
        $account = Account::find($uid);
        $account->login_count += 1;
        $account->save();
        if (!empty($account)) {
            return true;
        }
        return false;
    }

    public function findAccount($request)
    {
        if ($request->has('email')){
            return Account::where('email',$request->get('email'));
        }

        if ($request->has('mobile')){
            return Account::where('mobile',$request->get('mobile'));
        }
    }

    /**
     * 验证通过更新账号邮箱号
     * @param $uid
     * @param $email
     * @return mixed
     */
    public function upAccountEmail($uid,$email)
    {
       return Account::find($uid)->update(['email' => $email]);
    }

    /**
     * 验证通过更新账号密码
     * @param $uid
     * @param $password
     * @return mixed
     */
    public function upAccountPassword($uid,$password)
    {
        return Account::find($uid)->update(['password' => bcrypt($password)]);
    }

}
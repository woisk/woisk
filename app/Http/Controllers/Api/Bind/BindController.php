<?php
namespace App\Http\Controllers\Api\Bind;

use App\Http\Controllers\Api\BaseController;
use App\Woisk\Service\EmailService;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: woisk
 * Date: 2017/2/14 0014
 * Time: 15:39
 */
class BindController extends BaseController
{
    protected $service;

    /**
     * BindController constructor.
     * @param EmailService $service
     */
    public function __construct(EmailService $service)
    {
        $this->service = $service;
    }

    /**
     * 绑定邮箱  发送邮件
     * @param Request $request
     * @return $this
     */
    public function bindEmail(Request $request)
    {
        return $this->service->create($request);
    }

    /**
     * 验证邮件
     * @param Request $request
     * @return true
     */
    public function authEmail(Request $request)
    {
        return $this->service->authEmail($request);
    }

}
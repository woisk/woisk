<?php
/**
 * Created by PhpStorm.
 * User: woisk
 * Date: 2017/2/15 0015
 * Time: 14:25
 */

namespace App\Http\Controllers\Api\Bind;


use App\Http\Controllers\Api\BaseController;
use App\Woisk\Service\AuthEmailService;
use Illuminate\Http\Request;

class EmailController extends BaseController
{
    protected $service;
    public function __construct(AuthEmailService $service)
    {
        $this->service = $service;
    }

    /**
     * 绑定邮箱
     * @param Request $request
     * @return mixed
     */
    public function bindEmail(Request $request)
    {
        return $this->service->sendEmail($request);
    }

    public function authEmail(Request $request)
    {
        return $this->service->authEmail($request);
    }

}
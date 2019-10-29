<?php


namespace Chenlin\JWT;
use EasyWeChat\Factory;

/** 自定义助手类 @author:chenlin @date:2019/8/22 */
class Util
{
    use SingleTon;

    public function __construct()
    {

    }

    /**
     * 生成随机code
     * @return int
     */
    public function generateCode()
    {
        return mt_rand(10000, 99999);
    }

    /**
     * 操作成功返回的数据
     * @param array $data 数据
     * @param string $msg　信息描述
     * @return json
     * @author  chenlin
     * @date 2019/8/23
     */
    public function ajaxSuccess(array $data, string $msg)
    {
        return $this->ajaxReturn($data, $msg, SUCCESS_CODE);
    }

    /**
     * 操作失败返回的结果
     * @param array $data
     * @param string $msg
     * @return false|string
     * @author  chenlin
     * @date 2019/8/23
     */
    public function ajaxError(array $data = [], string $msg = '系统出错')
    {
        return $this->ajaxReturn($data, $msg, ERROR_CODE);
    }

    /**
     * 返回json数据
     * @param array $data
     * @param string $msg
     * @param int $code
     * @return false|string
     * @author  chenlin
     * @date 2019/8/23
     */
    public function ajaxReturn(array $data, string $msg, int $code)
    {
        $result = [
            'data' => $data,
            'msg' => $msg,
            'code' => $code
        ];
        return json_encode($result);
    }

    /**
     * 获取easywechat实例
     * @return \EasyWeChat\MiniProgram\Application
     * @author chenlin
     * @date 2019/8/23
     */
    public function getEasyWechat()
    {
        $config = [
            'app_id' => env('APP_ID', ''),
            'secret' => env('APP_SECRET', ''),

            //下面为可选项
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => storage_path('logs/wechat.log')
            ]
        ];

        return Factory::miniProgram($config);

    }

    /**
     * 根据code、iv、encryptData来进行解密获取open_id
     * @param string $code
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function getOpenId(string $code)
    {
        $app = $this->getEasyWechat();
        $session = $app->auth->session($code);
        //这里暂时用open_id key来代替
        return $session['openid'];

    }

    /**
     * 组装二维码url
     * @param string $role
     * @param int $code
     * @param int $coach_id
     * @return mixed
     * @author chenlin
     * @date 2019/8/26
     */
    public function getCoachBindPageUrl(string $role, int $code, int $coach_id)
    {
        $param = [
            'type' => config('dict.code_type')[$role],
            'code' => $code
        ];
        //生成临时推广码
        $response = $this->getEasyWechat()->app_code->getQrCode(config('dict.app_bind_page') . '?' . http_build_query($param));
        //保存二维码　然后返回前端
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            return $fileName = $response->saveAs(storage_path('app/public/code/coach'), 'coach_' . $coach_id . '.png');
        } else {
            return false;
        }
    }


    /**
     * 组装二维码url
     * @param string $role
     * @param int $code
     * @param int $student_id
     * @return mixed
     * @author chenlin
     * @date 2019/8/26
     */
    public function getStudentBindPageUrl(string $role, int $code, int $student_id)
    {
        $param = [
            'type' => config('dict.code_type')[$role],
            'code' => $code
        ];
        //生成临时推广码
        $response = $this->getEasyWechat()->app_code->getQrCode(config('dict.app_bind_page') . '?' . http_build_query($param));
        //保存二维码　然后返回前端
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            return $fileName = $response->saveAs(storage_path('app/public/code/student'), 'student_' . $student_id . '.png');
        } else {
            return false;
        }
    }

    public function micro_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}


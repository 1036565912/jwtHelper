<?php

namespace Chenlin\JWT;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class JwtHelper
{
    use SingleTon;

    public $secret; //秘钥


    public function __construct()
    {
        $this->secret = env('JWT_SECRET','huaxin!@#123');
    }

    /**
     * 生成token
     * @param array $claim 需要保存的数据
     * @return string
     * @author chenlin
     * @date 2019/8/19
     */
    public function generateToken(array $claim) :string
    {
        //初始化加密部分
        $builder = new Builder(); //构造类
        $signer = new Sha256();   //签名类
        $key = new Key($this->secret); //加密类

        //设置header和payload
        $builder->issuedBy(env('JWT_ISSUE'))  //设置发布者
        ->permittedFor(env('JWT_PERMIT')) //设置接受者
        ->identifiedBy(env('JWT_TOKEN_ID','test'),true) //设置token id
        ->issuedAt(time()) //设置发布时间  当前的时间戳
        ->expiresAt(time() + env('JWT_LIFE_TIME')) //设置过期时间  默认是一个月
        ->canOnlyBeUsedAfter(time() + 1);  //可以使用的时间

        //设置payload
        foreach ($claim as $k => $value) {
            $builder->withClaim($k,$value);
        }

        //设置签名
        $token = $builder->getToken($signer,$key);
        return (string)$token;  //由于builder类实现了__toString魔术方法
    }

    /**
     * 校验token是否可用
     * @param string $token
     * @return array
     * @author  chenlin
     * @date 2019/8/19
     */
    public function checkToken(string $token) :array
    {
        $signer = new Sha256();
        $parser = new Parser();
        $parser = $parser->parse($token);

        //验证token合法性
        if (!$parser->verify($signer,$this->secret)) {
            return [
                'code' => JWT_ERROR_CODE,
                'msg'  => 'TOKEN_INVALID'
            ];
        }

        //验证token是否过期
        if ($parser->isExpired()) {
            return [
                'code' => JWT_TOKEN_EXPIRED,
                'msg'  => 'TOKEN_EXPIRE'
            ];
        }

        return [
            'code' => JWT_SUCCESS_CODE,
            'msg'  => ''
        ];
    }

    /**
     * 根据传递的claim来获取对应的数据
     * @param string $token
     * @param string $key
     * @return mixed
     * @author chenlin
     * @date 2019/8/19
     */
    public function getClaim(string $token,string $key)
    {
        $parser = new Parser();
        $parser = $parser->parse($token);

        return $parser->getClaim($key);
    }
}
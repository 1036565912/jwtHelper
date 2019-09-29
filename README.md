这里需要提供包需要的参数配置信息 ----[laravel框架]   ----env配置
key:
    JWT_SECRET   jwt密匙
    JWT_ISSUE    发布者
    JWT_PERMIT   接受者
    JWT_TOKEN_ID token id
    JWT_LIFE_TIME  过期时间


token校验 根据返回的code 来判断
 200 校验成功
 202 token过期
 500 校验失败

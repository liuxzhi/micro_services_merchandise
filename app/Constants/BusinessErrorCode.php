<?php
declare(strict_types=1);


namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 * @method static getMessage($code, array $options = [])
 */
class BusinessErrorCode extends AbstractConstants
{
	/**
	 * @Message("非法请求")
	 */
	const ILLEGAL_REQUEST_ERROR = 10001;

	/**
	 * @Message("重复请求")
	 */
	const DUPLICATE_REQUEST_ERROR = 10002;

	/**
	 * @Message("参数验证错误")
	 */
	const PARAMS_VALIDATE_FAIL = 10003;

	/**
	 * @Message("参数AUTHORIZATION验证错误")
	 */
	const PARAMS_VALIDATE_AUTHORIZATION_FAIL = 10004;

	/**
	 * @Message("参数USER验证错误")
	 */
	const PARAMS_VALIDATE_USER_FAIL = 10005;

	/**
	 * @Message("参数APP_SECRET验证错误")
	 */
	const PARAMS_VALIDATE_APP_FAIL = 10006;

	/**
	 * @Message("远程HTTP服务错误")
	 */
	const HTTP_API_SERVICE_ERROR = 10101;

	/**
	 * @Message("HTTP请求错误")
	 */
	const HTTP_API_REQUEST_ERROR = 10102;

	/**
	 * @Message("HTTP服务响应错误1")
	 */
	const HTTP_API_RESPONSE_ERROR1 = 10103;

	/**
	 * @Message("HTTP服务响应错误2")
	 */
	const HTTP_API_RESPONSE_ERROR2 = 10104;

    /**
     * @Message("商品属性值异常")
     */
    const MERCHANDISE_ATTRIBUTE_VALUE_ERROR = 20004;

    /**
     * @Message("商品属性异常")
     */
    const MERCHANDISE_ATTRIBUTE_ERROR = 20005;


}

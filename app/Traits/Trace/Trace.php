<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Traits\Trace;

use Hyperf\Utils\Context;

/**
 * 设置trace_id
 * Trait Trace
 * @package App\Traits\Trace
 */
trait Trace
{
	/**
	 * 设置traceId.
	 *
	 * @param $traceId
	 * @param mixed $coverContext
	 */
	protected function putTraceId($traceId = false, $coverContext = true)
	{
		if ($coverContext || ! Context::get('trace_id')) {
			$traceId || $traceId = $this->getTraceId();
			Context::set('trace_id', $traceId);
		}
	}

	protected function clearTraceId()
	{
		Context::destroy('trace_id');
	}

	/**
	 * 获取TraceId.
	 *
	 * @return string
	 */
	private function getTraceId()
	{
		return sha1(uniqid(
			            '',
			            true
		            ) . str_shuffle(str_repeat(
			                            '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
			                            16
		                            )));
	}
}
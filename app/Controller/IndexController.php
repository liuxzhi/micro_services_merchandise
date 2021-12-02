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
namespace App\Controller;
use Hyperf\Engine\Http\Client;
use OOvertrue\CosClient\Signature;

class IndexController extends AbstractController
{
    public function index()
    {
        $file = $this->request->file('file');
        $contents = $file->getStream()->getContents();
        $saveName = "2021120110504041969.png";
        $client =  new Client("dev-1303873333.cos.ap-beijing.myqcloud.com");
        $result = $client->request("PUT", $saveName, ["body" => $contents,'head' => [
            'Content-MD5' => "TtLxlqPs2rv6iHW0UH2pig==",
            'Authorization' => 'q-sign-algorithm=sha1&q-ak=AKIDwf8KFwhebJfa2Dqj7JFuylLhpNEo9PK2&q-sign-time=1638330882;1638334542&q-key-time=1638330882;1638334542&q-header-list=content-md5;host&q-url-param-list=&q-signature=c5e635f70810ad5cf4665498ed9d947dc9234d94',
            ]]);
        print_r($result);
    }
}

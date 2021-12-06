<?php
declare(strict_types=1);

namespace App\Helper;

use App\Constants\BusinessErrorCode;
use App\Exception\BusinessException;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Guzzle\ClientFactory;

class ApiCurl
{

    /**
     * @var ClientFactory
     */
    private $guzzleClientFactory;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->guzzleClientFactory = $clientFactory;
    }

    public function request($method, $url, $params = [], $respt = 'json')
    {
        try {
            $client = $this->guzzleClientFactory->create();

            // 默认5秒超时
            isset($params[\GuzzleHttp\RequestOptions::TIMEOUT]) || $params[\GuzzleHttp\RequestOptions::TIMEOUT] = 5;

            Log::info('http_api_request', [
                'method' => $method,
                'url' => $url,
                'params' => $params,
            ]);
            $requestStart = (int)(microtime(true) * 1000);
            $response = $client->request($method, $url, $params);

            $result   = $response->getBody()->getContents();
            $requestEnd = (int)(microtime(true) * 1000);
            Log::info('http_api_response', [
                'result' => $result,
                'cost' => $requestEnd - $requestStart,
            ]);

            if (!$result) {
                throw new BusinessException(BusinessErrorCode::HTTP_API_RESPONSE_ERROR1);
            }

            switch ($respt) {
                case "json":
                    $result = json_decode($result, true);
                    break;
                case "xml":
                    libxml_disable_entity_loader(true);
                    $result = json_decode(json_encode(simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                    break;
                default:
                    $result = json_decode($result, true);
            }

            if (!$result) {
                throw new BusinessException(BusinessErrorCode::HTTP_API_RESPONSE_ERROR2);
            }

            return $result;
        } catch (GuzzleException $ex) {
            Log::notice('http_api_error', [
                'message' => $ex->getMessage(),
            ]);
            throw new BusinessException(BusinessErrorCode::HTTP_API_SERVICE_ERROR);
        }
    }


}
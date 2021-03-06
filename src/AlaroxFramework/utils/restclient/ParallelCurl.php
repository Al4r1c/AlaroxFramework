<?php
// Original code by Pete Warden <pete@petewarden.com>
//http://petewarden.typepad.com for more

namespace AlaroxFramework\utils\restclient;

class ParallelCurl
{
    /**
     * @var int
     */
    public $maxRequests;

    /**
     * @var array
     */
    public $outstandingRequests;

    /**
     * @var resource
     */
    public $multiHandle;

    /**
     * @param int $inMaxRequests
     */
    public function __construct($inMaxRequests = 10)
    {
        $this->setMaxRequests($inMaxRequests);

        $this->outstandingRequests = array();

        $this->multiHandle = curl_multi_init();
    }

    public function __destruct()
    {
        $this->finishAllRequests();
    }

    /**
     * @param int $inMaxRequests
     */
    public function setMaxRequests($inMaxRequests)
    {
        $this->maxRequests = $inMaxRequests;
    }

    /**
     * @param Curl $curlObject
     * @return string
     */
    public function executerCurl($curlObject)
    {
        $curlHandle = $curlObject->getCurl();

        if ($this->maxRequests > 0) {
            $this->waitForOutstandingRequestsToDropBelow($this->maxRequests);
        }


        curl_multi_add_handle($this->multiHandle, $curlHandle);

        $ch_array_key = (int)$curlHandle;

        $this->outstandingRequests[$ch_array_key] = true;


        do {
            $result = $this->checkForCompletedRequests('ko');
            usleep(1);
        } while ($result == null);

        return $result;
    }

    public function finishAllRequests()
    {
        $this->waitForOutstandingRequestsToDropBelow(1);
    }

    private function checkForCompletedRequests()
    {
        do {
            $mrc = curl_multi_exec($this->multiHandle, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($this->multiHandle) != -1) {
                do {
                    $mrc = curl_multi_exec($this->multiHandle, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            } else {
                return null;
            }
        }

        while ($info = curl_multi_info_read($this->multiHandle)) {
            $curlHandle = $info['handle'];
            $ch_array_key = (int)$curlHandle;

            if (!isset($this->outstandingRequests[$ch_array_key])) {
                die("Error - handle wasn't found in requests: '$curlHandle' in " .
                    print_r($this->outstandingRequests, true));
            }

            $content = curl_multi_getcontent($curlHandle);

            unset($this->outstandingRequests[$ch_array_key]);

            curl_multi_remove_handle($this->multiHandle, $curlHandle);

            return array($content, curl_getinfo($curlHandle));
        }
    }

    /**
     * @param int $max
     */
    private function waitForOutstandingRequestsToDropBelow($max)
    {
        while (1) {
            $this->checkForCompletedRequests();

            if (count($this->outstandingRequests) < $max) {
                break;
            }

            usleep(10000);
        }
    }
}
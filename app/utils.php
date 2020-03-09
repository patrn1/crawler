<?php

/**
 * Returns a callback which sends and caches the responses.
 *
 * @param string $cacheKey - The cache key for the response data.
 * @param int $defaultCode - The default HTTP code for the response.
 * @return Closure
 */
function get_response_handler(string $cacheKey = null, int $defaultCode = 200)
{
    return function ($data, $code = null) use ($cacheKey, $defaultCode) {
        global $ResponseCache;
        $code = $code ?? $defaultCode;

        http_response_code($code);

        $dataStr = is_string($data) ? $data : json_encode($data);
        if (!empty($cacheKey)) {
            $cacheTime = 300; // 5 minutes
            if ($code >= 400) {
                $ResponseCache->setError($cacheKey, $dataStr, $cacheTime);
            } else {
                $ResponseCache->set($cacheKey, $dataStr, $cacheTime);
            }
        }
        die($dataStr);
    };
}

/**
 * Returns a callback which sends and caches the error responses.
 *
 * @param string $cacheKey - The cache key for the response data.
 * @return Closure
 */
function get_error_handler(string $cacheKey)
{
    $handler = get_response_handler($cacheKey, 400);

    return function ($msg) use ($handler) {
        $handler([ "message" => $msg ]);
    };
}

/**
 * Tests if a connection to the IP address is possible.
 *
 * @param $address - The ip address for the connection.
 * @return bool - Indicates if a connection is possible.
 */
function test_connection(string $address)
{
    $splited = explode(':', $address); // Separate IP and port
    $con = @fsockopen($splited[0], $splited[1], $eroare, $eroare_str, 1.5);
    if ($con) {
        fclose($con); // Close the socket handle
        return true;
    }
    return false;
}

/**
 * Fetches data by the URL.
 *
 * @param $url - The URL of the resource to fetch.
 * @param bool $useProxy - Indicates if the proxy should be used.
 * @return bool|string - The result of the request.
 */
function fetch(string $url, bool $useProxy = false)
{
    $userAgents = include 'data/user-agents.php';
    $randUserAgent = $userAgents[ array_rand($userAgents, 1) ];

    $ch = curl_init();

    $headers = [
        "Accept: test/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Language: en-gb",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
    ];

    if ($useProxy) {
        $proxies = (include 'config/proxies.php') ?? [];
        $proxyIdx = array_rand($proxies);
        while (count($proxies)) {
            $proxy = $proxies[$proxyIdx];
            array_splice($proxies, $proxyIdx, 1);
            if (test_connection($proxy)) {
                break;
            } else {
                $proxy = null;
            }
        }

        // set options
        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            // to make the request go through as though proxy didn't exist
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
        }
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // read more about HTTPS http://stackoverflow.com/questions/31162706/how-to-scrape-a-ssl-or-https-url/31164409#31164409
    curl_setopt($ch, CURLOPT_USERAGENT, $randUserAgent);
    curl_setopt($ch, CURLOPT_REFERER, "http://www.google.com/");
    curl_setopt($ch, CURLOPT_ACCEPT_ENCODING, "br, gzip, deflate");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // $output contains the output string
    return curl_exec($ch);
}

/**
 * Rounds the float number with 2 decimal places.
 *
 * @param $number string | number - The number to be formatted.
 * @return string - The result of the formatting.
 */
function round_to_2dp($number)
{
    return number_format((float)$number, 2, '.', '');
}

/**
 * Removes the forbidden substrings ( HTML markup etc. ) from the user input.
 *
 * @param $text - The text to be sanitized.
 * @param bool $isUrl - Indicates if the text is an url.
 * @return string - The sanitized text.
 */
function sanitize(string $text, bool $isUrl = false)
{
    // Strip HTML Tags
    $text = strip_tags($text);
    if (!$isUrl) {
        // Clean up things like &amp;
        $text = html_entity_decode($text);
        // Strip out any url-encoded stuff
        $text = urldecode($text);
        // Replace non-AlNum characters with space
        $text = preg_replace('/[^A-Za-z0-9]/', ' ', $text);
    }
    // Replace Multiple spaces with single space
    $text = preg_replace('/ +/', ' ', $text);
    // Trim the string of leading/trailing space
    return trim($text);
}

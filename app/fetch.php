<?php
use App\Models\Domain;
use App\Models\Request;
use App\Models\Path;
use App\Models\Element;

global $ResponseCache;

$urlStr = sanitize($_GET[ 'url' ], true);
$elemStr = sanitize($_GET[ 'element' ]);
$cacheKey = 'count_element.' . md5("$elemStr $urlStr");

$send = get_response_handler($cacheKey);
$send_error = get_error_handler($cacheKey);
$send_without_caching = get_response_handler();

# Return the cached response if it exists
if ($ResponseCache->has($cacheKey)) {
    $send_without_caching($ResponseCache->get($cacheKey));
}
if ($ResponseCache->hasError($cacheKey)) {
    $send_without_caching($ResponseCache->getError($cacheKey), 400);
}

if (!filter_var($urlStr, FILTER_VALIDATE_URL)) {
    $send_error('Invalid URL!');
}
if (empty($elemStr)) {
    $send_error('Invalid element!');
}

$parsedUrl = parse_url($urlStr);
$pathStr = ltrim(parse_url($urlStr, PHP_URL_PATH), '/');
$hostStr = $parsedUrl['host'];
$dateTimeDbFormat = "Y-m-d H:i:s";
$dateTimeDisplayFormat = "d/m/Y H:i:s";

$requestSent = microtime(true);

# Request the page by the URL
# Use a random proxy if the request fails.
$html = fetch($urlStr) ?? fetch($urlStr, true);

if (empty($html)) {
    $send_error('Inaccessible URL!');
}
$requestDuration = microtime(true) - $requestSent;

$domain = Domain::where('name', $hostStr)->first();
if (empty($domain)) {
    $domain = new Domain();
    $domain->name = $hostStr;
    $domain->save();
}

$path = Path::where('value', $pathStr)->first();
if (empty($path)) {
    $path = new Path();
    $path->value = $pathStr;
    $path->save();
}

$element = Element::where('name', $elemStr)->first();
if (empty($element)) {
    $element = new Element();
    $element->name = $elemStr;
    $element->save();
}

$dom = new DOMDocument;
$domLoadResult = $dom->loadHTML($html);
if (empty($domLoadResult)) {
    $send_error('Invalid HTML content!');
}

$request = new Request();
$request->domain_id = $domain->id;
$request->path_id = $path->id;
$request->element_id = $element->id;
$request->element_count = $dom->getElementsByTagName($elemStr)->length;
$request->time = date($dateTimeDbFormat);
$request->duration = (int) ($requestDuration * 1000);
$request->save();

$responseData = [
    "request" => [
        "url" => $urlStr,
        "domain" => $hostStr,
        "element" => $elemStr,
        "time" => date($dateTimeDisplayFormat, $requestSent),
        "duration" => $request->duration,
        "elementCount" => $request->element_count,
    ],
    "general" => [
        "domainUrlsChecked" => $domain
            ->paths()
            ->groupBy('paths.id')
            ->toBase()
            ->getCountForPagination(),
        "avgRequestDuration" => round_to_2dp(
            Request::where('time', '>=', date($dateTimeDbFormat, strtotime("-1 day")))
                ->whereDomainId($domain->id)
                ->avg('duration')
        ),
        "elementCountOnDomain" => $element->requests()->whereDomainId($domain->id)->sum('element_count'),
        "elementCountAllRequests" => $element->requests()->sum('element_count'),
    ],
];

$send($responseData);

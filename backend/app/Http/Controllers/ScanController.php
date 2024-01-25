<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

$allUrls = [];
$visitedUrls = [];

class ScanController extends Controller
{
    public $allUrls;
    public $visitedUrls;
    public $domain;

    public function __construct()
    {
        $this->allUrls = [];
        $this->visitedUrls = [];
        $this->domain = '';
    }

    public function crawlPage($url, $client)
    {
        // Check if the URL has already been visited to avoid infinite loops
        if (in_array($url, $this->visitedUrls)) {
            return;
        }

        // Check if the URL is part of the specified domain
        if (!$this->isSameDomain($url, $this->domain)) {
            return;
        }

        // Add the current URL to the visited list
        $this->visitedUrls[] = $url;
        // print_r($url);
        // print_r('-----');
        try {
            // Make an HTTP GET request to the current URL
            $response = $client->get($url);

            // Extract information from the response
            $body = (string) $response->getBody();

            // Your additional parsing logic goes here

            // Example: Follow links to other pages (modify as needed)
            $matches = [];
            preg_match_all('/<a\s+href=["\'](https?:\/\/[^"\']+)["\']/i', $body, $matches);

            foreach ($matches[1] as $nextUrl) {
                // Check if the next URL is part of the specified domain
                if ($this->isSameDomain($nextUrl, $this->domain)) {
                    $this->visitedUrls[] = $nextUrl;
                    $this->crawlPage($nextUrl, $client);
                    // print_r($nextUrl);
                    // print_r('-----');
                }
            }
        } catch (\Exception $e) {
            // Handle request errors, if needed
            echo "Error fetching $url: " . $e->getMessage() . "\n";
        }
    }

    public function isSameDomain($url, $domain)
    {
        // Use parse_url to extract the host from the URL
        $urlParts = parse_url($url);

        // Check if the host is the same as the target domain or a subdomain
        return isset($urlParts['host']) && (fnmatch("$domain", $urlParts['host']) || fnmatch("*.{$domain}", $urlParts['host']));
    }

    public function index(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }
        $this->domain = $input['url'];
        $client = new Client();
        $startUrl = 'https://' . $this->domain;
        $this->crawlPage($startUrl, $client);
        $links = $this->visitedUrls;
        $responseUrls = [];
        foreach ($links as $link) {
            $response = $client->get($startUrl);
            $status = $response->getStatusCode();
            array_push($responseUrls, [
                "url" => $link,
                "code" => $status
            ]);
        }
        Link::insert($responseUrls);
        return response()->json($responseUrls);
    }
}

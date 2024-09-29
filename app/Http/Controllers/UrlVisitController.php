<?php
namespace App\Http\Controllers;

use App\Models\UrlVisit;
use App\Models\Category;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class UrlVisitController extends Controller
{
    private $api_key = 'fd03345befd8c07118ee867e15ba7c147beec2f4f8104f4034855c17e22a7581';
    private $base_url = 'https://www.virustotal.com/api/v3';

    public function analyzeUrl(Request $request)
    {
        // Validate the input URL
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');

        // Encode the URL to Base64
        $url_encoded = rtrim(strtr(base64_encode($url), '+/', '-_'), '=');

        // Fetch the VirusTotal report
        $report = $this->getVirusTotalReport($url_encoded);

        if (isset($report['error'])) {
            return response()->json(['error' => 'Failed to retrieve the report.'], 500);
        }

        // Parse the report data
        $data = $report['data'] ?? [];
        $attributes = $data['attributes'] ?? [];

        // Prepare the response
        $result = [
            'domain' => $data['id'] ?? null,
            'last_modification_date' => $attributes['last_modification_date'] ?? null,
            'creation_date' => $attributes['creation_date'] ?? null,
            'last_update_date' => $attributes['last_update_date'] ?? null,
            'registrar' => $attributes['registrar'] ?? null,
            'whois' => $attributes['whois'] ?? null,
            'categories' => $attributes['categories'] ?? [],
            'last_analysis_results' => $attributes['last_analysis_results'] ?? []
        ];

        return response()->json($result, 200);
    }

    private function getVirusTotalReport($url)
    {
        try {
            $client = new Client();

            // Set headers including API key
            $response = $client->request('GET', "{$this->base_url}/urls/{$url}", [
                'headers' => [
                    'x-apikey' => $this->api_key,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                return json_decode($response->getBody(), true);
            }

            return ['error' => "Error fetching data: {$statusCode}"];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public function index()
    {
        $urlVisits = UrlVisit::with('category')->get();
        return response()->json($urlVisits);
    }

 
        public function store(Request $request)
        {
            $request->validate([
                'url' => 'required|url',
                'screenshot' => 'required|string',
            ]);
    
            // Store the URL visit in the database
            $urlVisit = UrlVisit::create([
                'user_id' => auth()->id(), // Get the authenticated user ID
                'url' => $request->input('url'),
                'screenshot' => $request->input('screenshot'),
                'visit_time' => now(),
                'duration' => 0, // Set default duration if needed
            ]);
    
            return response()->json($urlVisit, 201);
        }
    
    public function show($id)
    {
        $urlVisit = UrlVisit::with('category')->findOrFail($id);
        return response()->json($urlVisit);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'url' => 'required|url',
            'visit_time' => 'required|date',
            'duration' => 'required|integer',
        ]);

        $urlVisit = UrlVisit::findOrFail($id);
        $urlVisit->update($request->all());
        return response()->json($urlVisit);
    }

    public function destroy($id)
    {
        $urlVisit = UrlVisit::findOrFail($id);
        $urlVisit->delete();
        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\UrlVisited;
use App\Models\Member;
use App\Models\UrlVisit;
use GuzzleHttp\Client;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

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
    public function show($id)
    {
        try {
            // Find the activity by ID, including the related user
            $activity = Activity::with('user')->findOrFail($id);
    
            return response()->json($activity, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return a 404 error if the activity is not found
            return response()->json(['error' => 'Activity not found.'], 404);
        } catch (\Exception $e) {
            // Return a 500 error if there's another issue
            return response()->json(['error' => 'Failed to retrieve activity.'], 500);
        }
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

    // Private function to get the browser name based on User-Agent string
    private function getBrowserName($userAgent)
    {
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Edg') !== false) return 'Edge';
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Brave') !== false) return 'Brave';
        if (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) return 'Opera';
        return 'Unknown';
    }

    public function start(Request $request)
    {
        Log::info('Start activity called', ['user_id' => $request->user()->id]);
    
        $userAgent = $request->header('User-Agent');
        $browser = $this->getBrowserName($userAgent);
    
        // Validate the incoming request (no need to validate member_id if we will determine it in the method)
        $validatedData = $request->validate([
            // Other validation rules if necessary
        ]);
    
        $userId = $request->user()->id;
    
        // Check if the user is a member and get member_id
        $member = Member::where('user_id', $userId)->first();
        
        if ($member) {
            Log::info('Member found', ['member_id' => $member->id]);
            $memberId = $member->id; // Get member_id if the member exists
        } else {
            Log::info('No member found for user', ['user_id' => $userId]);
            $memberId = null; // Set to null if not a member
        }
    
        try {
            // Create a new activity for the authenticated user
            $activity = Activity::create([
                'user_id' => $userId,
                'member_id' => $memberId, // Store member_id
                'browser' => $browser,
                'start_time' => now(),
                'end_time' => null, // Set end_time to null initially
            ]);
    
            Log::info('Activity started successfully', ['activity_id' => $activity->id]);
    
            // Return all relevant attributes of the created activity
            return response()->json($activity->only([
                'id',
                'user_id',
                'member_id',  // Include member_id in the response
                'browser',
                'start_time',
                'end_time',
                'duration',
                'created_at',
                'updated_at'
            ]), 201);
        } catch (\Exception $e) {
            Log::error('Error starting activity', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return response()->json(['error' => 'Failed to start activity.'], 500);
        }
    }
    

    

// Stop an ongoing activity
// Stop an ongoing activity
public function stop(Request $request, $id)
{
    try {
        // Fetch the ongoing activity for the user
        $activity = Activity::where('user_id', $request->user()->id)
                            ->whereNull('end_time') // Ensure it's not already stopped
                            ->findOrFail($id);
    
        // Update the end_time to mark it as stopped
        $activity->end_time = now(); // Use the current time for end_time
        $activity->save(); // Save the changes

        // Calculate duration
        $startTime = $activity->start_time;
        $endTime = $activity->end_time; // This now has the correct end_time
        $duration = $endTime->diff($startTime); // Get the duration as a DateInterval

        // Update duration in the activity record
        $activity->duration = $duration->format('%h h %i m %s s');
        $activity->save(); // Save the updated duration

        Log::info('Activity stopped', ['activity_id' => $activity->id]);
    
        // Return all relevant attributes of the updated activity, including member_id
        return response()->json($activity->only([
            'id',
            'user_id',
            'member_id', // Include member_id in the response
            'browser',
            'start_time',
            'end_time',
            'duration',
            'created_at',
            'updated_at'
        ]), 200);
    } catch (\Exception $e) {
        Log::error('Error stopping activity', [
            'error' => $e->getMessage(),
            'activity_id' => $id
        ]);
        return response()->json(['error' => 'Failed to stop activity.'], 500);
    }
}


    // Save URL visit information
    public function saveUrl(Request $request, $id)
    {
        // Validate incoming URL
        $request->validate(['url' => 'required|url']);

        try {
            // Fetch the ongoing activity for the user
            $activity = Activity::where('user_id', $request->user()->id)
                                ->whereNull('end_time')
                                ->findOrFail($id);

            // Store URLs in an array or a JSON column
            $urls = $activity->urls ?? [];
            $urls[] = $request->url;

            $activity->update(['urls' => json_encode($urls)]); // Save URLs as JSON
            Log::info('URL saved', ['url' => $request->url, 'activity_id' => $activity->id]);

            // Trigger the UrlVisited event
            event(new UrlVisited($request->user()->id, $request->url));

            return response()->json(['message' => 'URL saved', 'activity' => $activity], 200);
        } catch (\Exception $e) {
            Log::error('Error saving URL', [
                'error' => $e->getMessage(),
                'activity_id' => $id
            ]);
            return response()->json(['error' => 'Failed to save URL.'], 500);
        }
    }

    // Track URL visit explicitly
    public function trackUrl(Request $request)
    {
        $userId = $request->user()->id;
        $url = $request->input('url'); // Assuming you're sending the URL from the client
        $activityId = $request->input('activity_id'); // Get activity_id from the request

        try {
            // Create a record of the URL visit
            UrlVisit::create([
                'user_id' => $userId,
                'url' => $url,
                'activity_id' => $activityId, // Include the activity_id here
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Log::info('URL tracked successfully', ['user_id' => $userId, 'url' => $url]);

            return response()->json(['message' => 'URL tracked successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error tracking URL', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return response()->json(['error' => 'Failed to track URL.'], 500);
        }
    }


    public function index(Request $request)
    {
        // Get the authenticated user's ID and active status
        $userId = $request->user()->id;
        $userStatus = $request->user()->is_active; 
    
        // Get the members invited by the authenticated user
        $invitedMembers = Member::where('invited_by', $userId)->pluck('id')->toArray();
    
        // Log the IDs of invited members for debugging
        Log::info('Invited Members IDs:', $invitedMembers);
    
        // Fetch activities for those members or users without members (if necessary)
        $activities = Activity::whereIn('member_id', $invitedMembers)
            ->orWhereNull('member_id')
            ->with(['member:id,name', 'user:id,name,is_active'])
            ->get();
    
        // Log the fetched activities for debugging
        Log::info('Fetched Activities:', $activities->toArray());
    
        // Format the response to include member name and their active status
        $formattedActivities = $activities->map(function ($activity) use ($userStatus) {
            $member = $activity->member; // Get the member object
            $memberName = $member ? $member->name : 'Unknown'; // Default to 'Unknown' if member is null
    
            // Log the member name and active status for each activity
            Log::info('Activity Member Info:', [
                'member_name' => $memberName,
              
            ]);
    
            return [
                'id' => $activity->id,
                'user_id' => $activity->user_id,
                'member_id' => $activity->member_id,
                'member_name' => $memberName,
                'user_is_active' => $userStatus, // Include user's active status
                'browser' => $activity->browser,
                'start_time' => $activity->start_time,
                'end_time' => $activity->end_time,
                'duration' => $activity->duration,
                'created_at' => $activity->created_at,
                'updated_at' => $activity->updated_at,
            ];
        });
    
        // Return the formatted response
        return response()->json($formattedActivities);
    }
    

    
    
public function delete($id)
{
    try {
        // Find the activity by ID
        $activity = Activity::findOrFail($id);
        
        // Delete the activity
        $activity->delete();
        
        return response()->json(['message' => 'Activity deleted successfully.'], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'Activity not found.'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to delete activity.'], 500);
    }
}
public function showUserActivities(Request $request)
{
    // Get the authenticated user's ID
    $userId = $request->user()->id;

    // Fetch activities for the authenticated user
    $activities = Activity::where('user_id', $userId)
        ->with(['member:id,name']) // Load related member's name (adjust as necessary)
        ->get();

    return response()->json($activities);

}


}

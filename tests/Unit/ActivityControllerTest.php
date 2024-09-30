<?php

namespace Tests\Unit;

use App\Http\Controllers\ActivityController;
use App\Models\Activity;
use App\Models\Member;
use App\Models\UrlVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Mockery;

class ActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $activityController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityController = new ActivityController();
    }

    public function test_analyze_url_success()
    {
        // Mock a valid request
        $request = Request::create('/analyze-url', 'POST', ['url' => 'http://example.com']);
        $response = $this->activityController->analyzeUrl($request);
        
        $this->assertEquals(200, $response->getStatusCode());
    }

   

    public function test_show_activity_success()
    {
        $activity = Activity::factory()->create();
        $request = Request::create('/activities' . $activity->id, 'GET');

        $response = $this->activityController->show($activity->id);
        
        $this->assertEquals(200, $response->getStatusCode());
    }

   
    // public function test_start_activity_success()
    // {
    //     $user = User::factory()->create();
    //     $this->actingAs($user);
    //     $request = Request::create('/activities/start', 'POST', []);
        
    //     $response = $this->activityController->start($request);
        
    //     $this->assertEquals(201, $response->getStatusCode());
    // }

    // public function test_stop_activity_success()
    // {
    //     $user = User::factory()->create();
    //     $activity = Activity::factory()->create(['user_id' => $user->id, 'end_time' => null]);
    //     $this->actingAs($user);
        
    //     $request = Request::create('/activities/stop' . $activity->id, 'POST');
    //     $response = $this->activityController->stop($request, $activity->id);
        
    //     $this->assertEquals(200, $response->getStatusCode());
    // }

   

  



    public function test_delete_activity_success()
    {
        $activity = Activity::factory()->create();
        
        $response = $this->activityController->delete($activity->id);
        
        $this->assertEquals(200, $response->getStatusCode());
    }
    
  
}

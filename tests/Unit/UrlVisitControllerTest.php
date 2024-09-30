<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\UrlVisit;
use App\Models\User;

class UrlVisitControllerTest extends TestCase
{
    // use RefreshDatabase; // This will reset the database for each test

    // public function test_user_can_store_url_visit()
    // {
    //     // Create a user for testing using the factory

    //     $data = [
    //         'url' => 'https://example.com',
    //         'screenshot' => 'https://example.com/screenshot.png',
    //         'visit_time' => now(),
    //         'duration' => 30,
    //     ];

    //     $response = $this->postJson('/api/url-visits', $data);

    //     $response->assertStatus(201)
    //              ->assertJson([
    //                  'message' => 'URL visit stored successfully',
    //                  'data' => [
    //                      'url' => 'https://example.com',
    //                      'screenshot' => 'https://example.com/screenshot.png',
    //                  ],
    //              ]);

    //     $this->assertDatabaseHas('url_visits', $data);
    // }

    // public function test_user_can_show_url_visit()
    // {
    //     // Create a user and a URL visit for testing
    //     $user = User::factory()->create();
    //     $urlVisit = UrlVisit::factory()->create([
    //         'user_id' => $user->id, // Link the visit to the created user
    //     ]);

    //     $response = $this->getJson("/api/url-visits/{$urlVisit->id}");

    //     $response->assertStatus(200)
    //              ->assertJson([
    //                  'id' => $urlVisit->id,
    //                  'url' => $urlVisit->url,
    //                  'screenshot' => $urlVisit->screenshot,
    //              ]);
    // }

    // public function test_user_can_update_url_visit()
    // {
      
    //     $data = [
    //         'url' => 'https://updatedexample.com',
    //         'screenshot' => 'https://updatedexample.com/screenshot.png',
    //         'visit_time' => now(),
    //         'duration' => 45,
    //     ];

    //     $response = $this->putJson("/api/url-visits", $data);

    //     $response->assertStatus(200)
    //              ->assertJson($data);

    //     $this->assertDatabaseHas('url_visits', $data);
    // }

    // public function test_user_can_delete_url_visit()
    // {
    //     // Create a user and a URL visit for testing
    //     $user = User::factory()->create();
    //     $urlVisit = UrlVisit::factory()->create([
    //         'user_id' => $user->id, // Link the visit to the created user
    //     ]);

    //     $response = $this->deleteJson("/api/url-visits/{$urlVisit->id}");

    //     $response->assertStatus(204);
    //     $this->assertDatabaseMissing('url_visits', ['id' => $urlVisit->id]);
    // }

    // public function test_analyze_url_returns_correct_data()
    // {
    //     $data = [
    //         'url' => 'https://example.com',
    //     ];

    //     $response = $this->postJson('/api/analyze-url', $data);

    //     $response->assertStatus(200)
    //              ->assertJsonStructure([
    //                  'domain',
    //                  'last_modification_date',
    //                  'creation_date',
    //                  'last_update_date',
    //                  'registrar',
    //                  'whois',
    //                  'categories',
    //                  'last_analysis_results',
    //              ]);
    // }
}

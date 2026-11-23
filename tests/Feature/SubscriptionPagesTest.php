<?php

namespace Tests\Feature;

use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_renders_overview_and_navigation_links(): void
    {
        Subscription::create([
            'name' => 'Gemini Pro',
            'price' => 'RM 20.00',
            'frequency' => 'Monthly',
            'billing' => 'Monthly',
            'category' => 'AI Tools',
            'firstBill' => 'Nov 20, 2026',
            'paymentMethod' => 'Card',
            'color' => '#cfe2fe',
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Subscription Overview')
            ->assertSee('Gemini Pro')
            ->assertSee('RM 20.00')
            ->assertDontSee('Live DB')
            ->assertDontSee('Mobile Sync')
            ->assertDontSee('http://localhost:8000')
            ->assertSee('href="/subscriptions"', false)
            ->assertSee('href="/subscriptions/create"', false)
            ->assertSee('href="/analytics"', false);
    }

    public function test_all_subscriptions_page_lists_subscriptions(): void
    {
        $subscription = Subscription::create([
            'name' => 'Spotify Duo',
            'price' => '$14.99',
            'frequency' => 'Monthly',
            'billing' => 'Monthly',
            'category' => 'Entertainment',
            'firstBill' => 'Nov 15, 2026',
            'paymentMethod' => 'Apple Pay',
            'color' => '#ff7666',
        ]);

        $response = $this->get(route('subscriptions.index'));

        $response->assertOk()
            ->assertSee('All Subscriptions')
            ->assertSee('Spotify Duo')
            ->assertSee('RM 14.99')
            ->assertDontSee('http://localhost:8000')
            ->assertSee('href="/subscriptions/'.$subscription->id.'"', false);
    }

    public function test_create_subscription_page_renders_form(): void
    {
        $response = $this->get(route('subscriptions.create'));

        $response->assertOk()
            ->assertSee('New Subscription')
            ->assertSee('action="/subscriptions"', false)
            ->assertDontSee('http://localhost:8000')
            ->assertSee('Price (MYR)')
            ->assertDontSee('Price (USD)')
            ->assertSee('name="name"', false)
            ->assertSee('name="price"', false)
            ->assertSee('name="frequency"', false)
            ->assertSee('name="category"', false)
            ->assertSee('Plan Preview')
            ->assertDontSee('Live Plan Preview')
            ->assertDontSee('Auto-Sync');
    }

    public function test_web_store_creates_subscription_and_redirects_to_index(): void
    {
        $response = $this->post(route('subscriptions.store'), [
            'name' => 'GitHub Copilot',
            'price' => '100',
            'frequency' => 'Yearly',
            'category' => 'Developer Tools',
            'paymentMethod' => 'Visa',
            'firstBill' => 'Nov 23, 2026',
        ]);

        $response->assertRedirect(route('subscriptions.index'))
            ->assertSessionHas('success', 'Subscription added!');

        $this->assertDatabaseHas('subscriptions', [
            'name' => 'GitHub Copilot',
            'price' => 'RM 100.00',
            'frequency' => 'Yearly',
            'billing' => 'Yearly',
            'category' => 'Developer Tools',
            'paymentMethod' => 'Visa',
            'firstBill' => 'Nov 23, 2026',
            'color' => '#ded2f9',
        ]);
    }

    public function test_subscription_detail_page_renders_and_returns_404_for_missing(): void
    {
        $subscription = Subscription::create([
            'name' => 'Figma Pro',
            'price' => 'RM 144.00',
            'frequency' => 'Yearly',
            'billing' => 'Yearly',
            'category' => 'Design',
            'firstBill' => 'Nov 01, 2026',
            'paymentMethod' => 'Card',
            'color' => '#eed766',
        ]);

        $response = $this->get(route('subscriptions.show', $subscription));

        $response->assertOk()
            ->assertSee('Figma Pro')
            ->assertSee('RM 144.00')
            ->assertSee('RM 12.00')
            ->assertSee('Design')
            ->assertSee('Plan ID')
            ->assertDontSee('API Sync ID');

        $this->get('/subscriptions/99999')->assertNotFound();
    }

    public function test_web_destroy_deletes_subscription_and_redirects_to_index(): void
    {
        $subscription = Subscription::create([
            'name' => 'Linear',
            'price' => 'RM 8.00',
            'frequency' => 'Monthly',
            'billing' => 'Monthly',
            'category' => 'Productivity',
            'firstBill' => 'Nov 10, 2026',
            'paymentMethod' => 'Card',
            'color' => '#eadece',
        ]);

        $response = $this->delete(route('subscriptions.destroy', $subscription));

        $response->assertRedirect(route('subscriptions.index'))
            ->assertSessionHas('success', 'Subscription cancelled!');

        $this->assertDatabaseMissing('subscriptions', [
            'id' => $subscription->id,
        ]);
    }

    public function test_analytics_page_renders_category_and_billing_breakdown(): void
    {
        Subscription::create([
            'name' => 'Claude Pro',
            'price' => 'RM 20.00',
            'frequency' => 'Monthly',
            'billing' => 'Monthly',
            'category' => 'AI Tools',
            'color' => '#cfe2fe',
        ]);

        Subscription::create([
            'name' => 'JetBrains All Products',
            'price' => 'RM 240.00',
            'frequency' => 'Yearly',
            'billing' => 'Yearly',
            'category' => 'Developer Tools',
            'color' => '#ded2f9',
        ]);

        $response = $this->get(route('subscriptions.analytics'));

        $response->assertOk()
            ->assertSee('Spending Analytics')
            ->assertSee('AI Tools')
            ->assertSee('Developer Tools')
            ->assertSee('RM 40.00')
            ->assertSee('RM 480.00')
            ->assertSee('Billing Cycle Split');
    }

    public function test_api_endpoints_remain_functional(): void
    {
        $createResponse = $this->postJson('/api/subscriptions', [
            'name' => 'Notion Plus',
            'price' => 'RM 10.00',
            'frequency' => 'Monthly',
            'billing' => 'Monthly',
            'category' => 'Productivity',
            'firstBill' => 'Nov 23, 2026',
            'paymentMethod' => 'Card',
            'color' => '#eadece',
        ]);

        $createResponse->assertCreated()
            ->assertJsonFragment(['name' => 'Notion Plus']);

        $subscriptionId = $createResponse->json('id');

        $this->getJson('/api/subscriptions')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Notion Plus']);

        $this->deleteJson('/api/subscriptions/'.$subscriptionId)
            ->assertOk()
            ->assertJson(['message' => 'Subscription deleted successfully']);

        $this->assertDatabaseMissing('subscriptions', ['id' => $subscriptionId]);
    }

    public function test_redirects_honor_forwarded_ngrok_proxy_headers(): void
    {
        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'demo.ngrok-free.app',
        ])->post('/subscriptions', [
            'name' => 'Claude Pro',
            'price' => '20',
            'frequency' => 'Monthly',
            'category' => 'AI Tools',
        ]);

        $response->assertRedirect('https://demo.ngrok-free.app/subscriptions');
    }
}

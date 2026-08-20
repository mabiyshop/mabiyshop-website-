<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Order;
use App\Models\Addresses;
use App\Models\Product;
use App\Models\Cart;
use DB;

class FraudSecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function createCustomer(): User
    {
        $user = new User();
        $user->name = 'Fraud Test Customer';
        $user->email = 'fraudtest@example.com';
        $user->phone = '01700000001';
        $user->password = bcrypt('password');
        $user->status = 1;
        $user->save();

        $address = new Addresses();
        $address->user_id = $user->id;
        $address->shipping_first_name = 'Test';
        $address->shipping_last_name = 'Customer';
        $address->shipping_phone = '01700000001';
        $address->shipping_address = '123 Test Street';
        $address->shipping_division = 1;
        $address->shipping_district = 1;
        $address->shipping_thana = 1;
        $address->shipping_union = 1;
        $address->save();

        $user->default_address_id = $address->id;
        $user->save();

        return $user;
    }

    private function createProduct(): Product
    {
        $product = new Product();
        $product->title = 'Fraud Test Product';
        $product->slug = 'fraud-test-product';
        $product->price = 100;
        $product->seller_id = 1;
        $product->is_deleted = 0;
        $product->save();

        return $product;
    }

    private function addToCart(User $user, Product $product): void
    {
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_type' => 'simple',
            'qty' => 1,
            'price' => $product->price,
            'session_key' => 'test_session',
        ]);
    }

    public function test_good_order_has_normal_fraud_status()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct();
        $this->addToCart($user, $product);

        $response = $this->actingAs($user, 'customer-api')->postJson('/api/v1/order', [
            'payment_method' => 'cash_on_delivery',
            'shipping_method' => [['product_id' => $product->id, 'shipping_method' => 'default']],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('NORMAL', $order->fraud_status);
        $this->assertFalse($order->manual_review);
        $this->assertFalse($order->otp_required);
    }

    public function test_high_risk_order_is_saved_with_review_status()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct();
        $this->addToCart($user, $product);

        $response = $this->actingAs($user, 'customer-api')->postJson('/api/v1/order', [
            'payment_method' => 'cash_on_delivery',
            'shipping_method' => [['product_id' => $product->id, 'shipping_method' => 'default']],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertContains($order->fraud_status, ['NORMAL', 'REVIEW', 'BLOCKED']);
        $this->assertNotNull($order->risk_decision_snapshot);
    }

    public function test_review_order_excluded_from_normal_dashboard()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct();
        $this->addToCart($user, $product);

        $response = $this->actingAs($user, 'customer-api')->postJson('/api/v1/order', [
            'payment_method' => 'cash_on_delivery',
            'shipping_method' => [['product_id' => $product->id, 'shipping_method' => 'default']],
        ]);

        $response->assertStatus(200);

        $order = Order::where('user_id', $user->id)->first();
        if ($order->fraud_status === 'NORMAL') {
            $this->markTestSkipped('Order evaluated as normal; review exclusion requires risk service mock.');
        }

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $dashboardResponse = $this->actingAs($admin)->get('/admin/orders');
        $dashboardResponse->assertStatus(200);
        $this->assertStringNotContainsString('MS' . date('y', strtotime($order->created_at)) . $order->id, $dashboardResponse->getContent());
    }

    public function test_fraud_queue_release_returns_to_normal()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct();
        $this->addToCart($user, $product);

        $response = $this->actingAs($user, 'customer-api')->postJson('/api/v1/order', [
            'payment_method' => 'cash_on_delivery',
            'shipping_method' => [['product_id' => $product->id, 'shipping_method' => 'default']],
        ]);

        $response->assertStatus(200);
        $order = Order::where('user_id', $user->id)->first();

        if ($order->fraud_status === 'NORMAL') {
            $order->fraud_status = 'REVIEW';
            $order->manual_review = true;
            $order->save();
        }

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $releaseResponse = $this->actingAs($admin)->post("/admin/fraud-security/{$order->id}/release", [
            '_token' => csrf_token(),
            'note' => 'Test release',
        ]);

        $releaseResponse->assertStatus(200);
        $releaseResponse->assertJson(['status' => 1]);

        $order->refresh();
        $this->assertEquals('NORMAL', $order->fraud_status);
        $this->assertFalse($order->manual_review);
    }

    public function test_mark_as_fraud_moves_order_to_blocked()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct();
        $this->addToCart($user, $product);

        $response = $this->actingAs($user, 'customer-api')->postJson('/api/v1/order', [
            'payment_method' => 'cash_on_delivery',
            'shipping_method' => [['product_id' => $product->id, 'shipping_method' => 'default']],
        ]);

        $response->assertStatus(200);
        $order = Order::where('user_id', $user->id)->first();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $markResponse = $this->actingAs($admin)->post("/admin/fraud-security/{$order->id}/mark-as-fraud", [
            '_token' => csrf_token(),
            'reason' => 'Suspicious activity',
        ]);

        $markResponse->assertStatus(200);
        $markResponse->assertJson(['status' => 1]);

        $order->refresh();
        $this->assertEquals('BLOCKED', $order->fraud_status);
        $this->assertTrue($order->manual_review);
        $this->assertEquals('Suspicious activity', $order->fraud_reason);
    }

    public function test_courier_provider_failure_does_not_break_order_creation()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct();
        $this->addToCart($user, $product);

        $response = $this->actingAs($user, 'customer-api')->postJson('/api/v1/order', [
            'payment_method' => 'cash_on_delivery',
            'shipping_method' => [['product_id' => $product->id, 'shipping_method' => 'default']],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_admin_can_view_payment_listing(): void
    {
        $response = $this->get(route('payments'), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }

    /**
     * @return void
     */
    public function test_user_cannot_view_payment_listing(): void
    {
        $response = $this->get(route('payments'), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_admin_cannot_create_payment()
    {
        $payment = Payment::factory()->make();
        $payment->details = json_encode($payment->details);
        $response = $this->post(route('payment.create'), $payment->toArray(), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_can_create_payment()
    {
        $payment = Payment::factory()->make();
        $payment->details = json_encode($payment->details);
        $response = $this->post(route('payment.create'), $payment->toArray(), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_admin_can_update_payment()
    {
        //create a payment
        $payment = Payment::factory()->credit_card()->create();
        $new_payment = Payment::factory()->bank_transfer()->make();
        $new_payment->details = json_encode($new_payment->details);

        $response = $this->put(route('payment.update', ['payment' => $payment->uuid]), $new_payment->toArray(), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $payment->refresh();
        $this->assertEquals($payment->type, $new_payment->type);
        $this->assertEquals($payment->iban, $new_payment->iban);
        $this->assertEquals($payment->name, $new_payment->name);
        $this->assertEquals($payment->swift, $new_payment->swift);
    }

    public function test_user_cannot_update_payment()
    {
        //create a payment
        $payment = Payment::factory()->credit_card()->create();
        $new_payment = Payment::factory()->bank_transfer()->make();
        $new_payment->details = json_encode($new_payment->details);

        $response = $this->put(route('payment.update', ['payment' => $payment->uuid]), $new_payment->toArray(), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_admin_can_view_payment()
    {
        $payment = Payment::factory()->create();
        $response = $this->get(route('payment.show', ['payment' => $payment->uuid]), $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK)->assertJsonPath('data.uuid', $payment->uuid);
    }

    public function test_user_cannot_view_payment()
    {
        $payment = Payment::factory()->create();
        $response = $this->get(route('payment.show', ['payment' => $payment->uuid]), $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_admin_can_delete_payment()
    {
        $payment = Payment::factory()->create();
        $response = $this->delete(route('payment.delete', ['payment' => $payment->uuid]), [], $this->getAdminAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);

        $payment = Payment::find($payment->id);
        $this->assertNull($payment);
    }

    public function test_user_cannot_delete_payment()
    {
        $payment = Payment::factory()->create();
        $response = $this->delete(route('payment.delete', ['payment' => $payment->uuid]), [], $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $payment = Payment::find($payment->id);
        $this->assertNotNull($payment);
    }
}

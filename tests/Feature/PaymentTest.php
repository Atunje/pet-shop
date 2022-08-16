<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Payment;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    //use RefreshDatabase;


    /**
     * @return void
     */
    public function test_payment_listing(): void
    {
        $response = $this->get(self::PAYMENT_ENDPOINT, $this->getUserAuthHeaders());
        $response->assertStatus(200)
            //confirm if record is paginated
            ->assertJsonPath('data.current_page', 1);
    }



    public function test_creation_of_payment()
    {
        $payment = Payment::factory()->make();
        $payment->details = json_encode($payment->details);
        $response = $this->post(self::PAYMENT_ENDPOINT . "create", $payment->toArray(), $this->getUserAuthHeaders());
        $response->assertStatus(200);
    }


    public function test_updating_of_payment()
    {
        //create a payment
        $payment = Payment::factory()->credit_card()->create();
        $new_payment = Payment::factory()->bank_transfer()->make();
        $new_payment->details = json_encode($new_payment->details);

        $response = $this->put(self::PAYMENT_ENDPOINT . $payment->uuid, $new_payment->toArray(), $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $payment->refresh();
        $this->assertEquals($payment->type, $new_payment->type);
        $this->assertEquals($payment->iban, $new_payment->iban);
        $this->assertEquals($payment->name, $new_payment->name);
        $this->assertEquals($payment->swift, $new_payment->swift);
    }


    public function test_viewing_of_a_payment()
    {
        $payment = Payment::factory()->create();
        $response = $this->get(self::PAYMENT_ENDPOINT . $payment->uuid, $this->getUserAuthHeaders());
        $response->assertStatus(200)->assertJsonPath('data.uuid', $payment->uuid);
    }


    public function test_deleting_a_payment()
    {
        $payment = Payment::factory()->create();
        $response = $this->delete(self::PAYMENT_ENDPOINT . $payment->uuid, [], $this->getUserAuthHeaders());
        $response->assertStatus(200);

        $payment = Payment::find($payment->id);
        $this->assertNull($payment);
    }
}

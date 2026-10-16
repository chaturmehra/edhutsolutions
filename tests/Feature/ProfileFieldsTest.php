<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_update_gender_city_and_country_on_profile(): void
    {
        $student = User::factory()->create(['phone' => '9876543210']);

        $this->actingAs($student)->get(route('account.profile.edit'))
            ->assertOk()
            ->assertSee('name="gender"', false)
            ->assertSee('name="city"', false)
            ->assertSee('name="country_code"', false)
            ->assertSee('India');

        $this->actingAs($student)->put(route('account.profile.update'), [
            'name' => 'Asha Rao',
            'phone' => '9876543210',
            'gender' => 'female',
            'city' => 'Bengaluru',
            'country_code' => 'IN',
        ])->assertRedirect(route('account.profile.edit'))->assertSessionHas('status', 'Your profile was saved.');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Asha Rao',
            'gender' => 'female',
            'city' => 'Bengaluru',
            'country_code' => 'IN',
        ]);

        $this->actingAs($student)->put(route('account.profile.update'), [
            'name' => 'Asha Rao',
            'phone' => '9876543210',
            'gender' => 'robot',
            'city' => 'Bengaluru',
            'country_code' => 'ZZ',
        ])->assertSessionHasErrors(['gender', 'country_code']);
    }
}

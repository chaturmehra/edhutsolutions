<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Users\UserCsvImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserCsvImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_imports_students_from_csv_and_skips_existing_emails(): void
    {
        User::factory()->create(['email' => 'existing@edhut.test']);
        $admin = User::factory()->superAdmin()->create();
        $path = storage_path('framework/testing-users.csv');
        file_put_contents($path, implode("\n", [
            'name,email,phone,gender,city,country_code',
            'Rehan Khan,sakzishu016@gmail.com,7809788888,male,New Delhi,IN',
            'Existing Learner,existing@edhut.test,9999999999,female,Delhi,IN',
            'Bad Country,badland@edhut.test,1111111111,male,Somewhere,ZZ',
            'Asha Learner,asha@import.test,9876543210,female,Bengaluru,IN',
        ])."\n");

        $this->actingAs($admin)
            ->post(route('admin.users.import'), ['csv' => new UploadedFile($path, 'users.csv', 'text/csv', null, true)])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'sakzishu016@gmail.com',
            'gender' => 'male',
            'city' => 'New Delhi',
            'country_code' => 'IN',
            'role' => 'student',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'asha@import.test',
            'gender' => 'female',
            'country_code' => 'IN',
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'badland@edhut.test']);
        $this->assertTrue(Hash::check(UserCsvImporter::TEMP_PASSWORD, User::query()->where('email', 'asha@import.test')->value('password')));

        $this->actingAs($admin)->get(route('admin.users.import.template'))
            ->assertOk()
            ->assertHeader('content-disposition');
    }
}

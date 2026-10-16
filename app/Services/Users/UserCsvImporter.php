<?php

namespace App\Services\Users;

use App\Enums\UserRole;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserCsvImporter
{
    public const HEADERS = ['name', 'email', 'phone', 'gender', 'city', 'country_code'];

    /**
     * Temporary password set for every imported account. Learners should use Forgot password.
     */
    public const TEMP_PASSWORD = 'EdhutWelcome#2026';

    /**
     * @return array{created: int, skipped: int, errors: list<string>}
     */
    public function import(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            throw ValidationException::withMessages(['csv' => 'The CSV file could not be read.']);
        }

        try {
            $header = $this->readHeader($handle);
            $countries = Country::query()->active()->pluck('code')->map(fn ($code) => strtoupper((string) $code))->all();
            $existing = User::withTrashed()->pluck('email')->map(fn ($email) => strtolower((string) $email))->flip()->all();
            $password = Hash::make(self::TEMP_PASSWORD);
            $created = 0;
            $skipped = 0;
            $errors = [];
            $line = 1;

            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                if ($this->isBlank($row)) {
                    continue;
                }

                $data = $this->mapRow($header, $row);
                $email = strtolower(trim((string) ($data['email'] ?? '')));
                $name = trim((string) ($data['name'] ?? ''));

                if ($name === '' || $email === '') {
                    $errors[] = "Row {$line}: name and email are required.";
                    $skipped++;

                    continue;
                }

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$line}: invalid email ({$email}).";
                    $skipped++;

                    continue;
                }

                if (isset($existing[$email])) {
                    $skipped++;

                    continue;
                }

                $gender = $this->gender(trim((string) ($data['gender'] ?? '')));
                if ($gender === false) {
                    $errors[] = "Row {$line}: invalid gender for {$email}.";
                    $skipped++;

                    continue;
                }

                $country = strtoupper(trim((string) ($data['country_code'] ?? '')));
                if ($country !== '' && ! in_array($country, $countries, true)) {
                    $errors[] = "Row {$line}: unknown country_code ({$country}) for {$email}.";
                    $skipped++;

                    continue;
                }

                User::query()->create([
                    'name' => Str::limit($name, 120, ''),
                    'email' => $email,
                    'phone' => Str::limit(trim((string) ($data['phone'] ?? '')), 30, '') ?: null,
                    'gender' => $gender,
                    'city' => Str::limit(trim((string) ($data['city'] ?? '')), 120, '') ?: null,
                    'country_code' => $country !== '' ? $country : null,
                    'password' => $password,
                    'role' => UserRole::Student,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);

                $existing[$email] = true;
                $created++;
            }
        } finally {
            fclose($handle);
        }

        return compact('created', 'skipped', 'errors');
    }

    /**
     * @param  resource  $handle
     * @return list<string>
     */
    private function readHeader($handle): array
    {
        $header = fgetcsv($handle);
        if ($header === false || $header === [null] || $header === []) {
            throw ValidationException::withMessages(['csv' => 'The CSV file is empty.']);
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]) ?? $header[0];
        $normalized = array_map(fn ($value) => strtolower(trim((string) $value)), $header);

        foreach (self::HEADERS as $required) {
            if (! in_array($required, $normalized, true)) {
                throw ValidationException::withMessages([
                    'csv' => 'CSV must include columns: '.implode(', ', self::HEADERS).'.',
                ]);
            }
        }

        return $normalized;
    }

    /**
     * @param  list<string>  $header
     * @param  list<null|string>  $row
     * @return array<string, string>
     */
    private function mapRow(array $header, array $row): array
    {
        $data = [];
        foreach ($header as $index => $key) {
            $data[$key] = (string) ($row[$index] ?? '');
        }

        return $data;
    }

    /**
     * @param  list<null|string>  $row
     */
    private function isBlank(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function gender(string $value): string|false|null
    {
        if ($value === '') {
            return null;
        }

        $value = strtolower($value);

        return array_key_exists($value, User::GENDERS) ? $value : false;
    }
}

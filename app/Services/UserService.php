<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserService
{
    public const TYPE_MANAGER = 'manager';
    public const TYPE_OFFICE_STAFF = 'office_staff';
    public const TYPE_SALES_STAFF = 'sales_staff';
    public const TYPE_GENERAL_STAFF = 'general_staff';
    public const TYPE_CUSTOMER = 'customer';

    /**
     * @return array<string, string>
     */
    public static function staffTypeOptions(): array
    {
        return [
            self::TYPE_MANAGER => 'Manager',
            self::TYPE_OFFICE_STAFF => 'Office Staff',
            self::TYPE_SALES_STAFF => 'Sales Staff',
            self::TYPE_GENERAL_STAFF => 'Staff',   
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array{user: User, temporary_password: string}
     */
    public function createByType(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $type = (string) ($data['user_type'] ?? self::TYPE_OFFICE_STAFF);
            $temporaryPassword = (string) ($data['temporary_password'] ?? Str::password(12));

            $profileType = $data['profile_type'] ?? null;
            $profileId = $data['profile_id'] ?? null;

            if ($type === self::TYPE_CUSTOMER && filled($data['customer_id'] ?? null)) {
                $profileType = Customer::class;
                $profileId = (int) $data['customer_id'];
            }

            if ($this->isStaffType($type)) {
                $Employee = $this->createOrUpdateBranchEmployee(null, $data);
                $profileType = Employee::class;
                $profileId = $Employee->getKey();
            }

            $fullName = $this->resolveFullName($data);
            $username = filled($data['username'] ?? null)
                ? (string) $data['username']
                : $this->generateUniqueUsername($fullName);
            $email = $this->resolveUserEmail($data);

            $user = User::query()->create([
                'name' => $fullName,
                'username' => $username,
                'phone' => filled($data['phone'] ?? null) ? (string) $data['phone'] : null,
                'email' => $email,
                'password' => Hash::make($temporaryPassword),
                'profile_type' => $profileType,
                'profile_id' => $profileId,
            ]);

            $user->syncRoles($this->rolesForType($type));

            return [
                'user' => $user,
                'temporary_password' => $temporaryPassword,
            ];
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateByType(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $type = (string) ($data['user_type'] ?? self::TYPE_OFFICE_STAFF);

            if ($this->isStaffType($type)) {
                $Employee = $this->createOrUpdateBranchEmployee($user, $data);

                $user->forceFill([
                    'profile_type' => Employee::class,
                    'profile_id' => $Employee->getKey(),
                ]);
            }

            $user->fill([
                'name' => $this->resolveFullName($data),
                'phone' => filled($data['phone'] ?? null) ? (string) $data['phone'] : null,
                'email' => $this->resolveUserEmail($data, $user),
                'start_date' => $data['start_date'] ?? null,
            ]);

            $user->save();
            $user->syncRoles($this->rolesForType($type));

            return $user->fresh(['roles', 'profile']);
        });
    }

    /**
     * @return array<int, string>
     */
    protected function rolesForType(string $type): array
    {
        return match ($type) {
            self::TYPE_MANAGER => ['staff', 'manager'],
            self::TYPE_OFFICE_STAFF => ['staff', 'office_staff'],
            self::TYPE_SALES_STAFF => ['staff', 'sales_staff'],
            self::TYPE_CUSTOMER => ['customer'],
            default => ['staff', 'office_staff'],
        };
    }

    protected function generateUniqueUsername(string $name): string
    {
        $baseUsername = User::generateUsername($name);
        $username = $baseUsername;
        $counter = 2;

        while (User::query()->where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }

        return $username;
    }

    protected function isStaffType(string $type): bool
    {
        return in_array($type, [
            self::TYPE_MANAGER,
            self::TYPE_OFFICE_STAFF,
            self::TYPE_SALES_STAFF,
        ], true);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function resolveFullName(array $data): string
    {
        $firstName = trim((string) ($data['first_name'] ?? ''));
        $lastName = trim((string) ($data['last_name'] ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        if (filled($fullName)) {
            return $fullName;
        }

        return trim((string) ($data['name'] ?? ''));
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function resolveUserEmail(array $data, ?User $user = null): string
    {
        $providedEmail = filled($data['email'] ?? null) ? (string) $data['email'] : null;

        if (filled($providedEmail)) {
            return $providedEmail;
        }

        do {
            $candidate = 'change_me.' . Str::lower(Str::random(12)) . '@gmrj.online';

            $query = User::query()->where('email', $candidate);

            if ($user) {
                $query->whereKeyNot($user->getKey());
            }
        } while ($query->exists());

        return $candidate;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function createOrUpdateBranchEmployee(?User $user, array $data): Employee
    {
        $existingBranchEmployee = null;

        if ($user && $user->profile_type === Employee::class && filled($user->profile_id)) {
            $existingBranchEmployee = Employee::query()
                ->with(['person.address'])
                ->find((int) $user->profile_id);
        }

        $addressPayload = [
            'address' => Arr::get($data, 'address_line'),
            'location' => Arr::get($data, 'location'),
            'territory' => Arr::get($data, 'territory'),
            'postal_code' => Arr::get($data, 'postal_code'),
            'country_id' => Arr::get($data, 'country_id'),
        ];

        $address = $existingBranchEmployee?->person?->address;

        if ($address) {
            $address->update($addressPayload);
        } else {
            $address = Address::query()->create($addressPayload);
        }

        $personPayload = [
            'first_name' => Arr::get($data, 'first_name'),
            'last_name' => Arr::get($data, 'last_name'),
            'phone' => Arr::get($data, 'phone'),
            'email' => Arr::get($data, 'email'),
            'address_id' => $address->getKey(),
        ];

        $person = $existingBranchEmployee?->person;

        if ($person) {
            $person->update($personPayload);
        } else {
            $person = Person::query()->create($personPayload);
        }

        if ($existingBranchEmployee) {
            $existingBranchEmployee->update([
                'company_id' => (int) Arr::get($data, 'company_id'),
                'person_id' => $person->getKey(),
                'manager_id' => filled(Arr::get($data, 'manager_id')) ? (int) Arr::get($data, 'manager_id') : null,
            ]);

            return $existingBranchEmployee;
        }

        return Employee::query()->create([
            'company_id' => (int) Arr::get($data, 'company_id'),
            'person_id' => $person->getKey(),
            'manager_id' => filled(Arr::get($data, 'manager_id')) ? (int) Arr::get($data, 'manager_id') : null,
        ]);
    }
}

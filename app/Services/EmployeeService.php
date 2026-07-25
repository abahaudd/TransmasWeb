<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    /**
     * @var array<int, string>
     */
    protected const PERSON_FIELDS = [
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'nationality',
        'national_id',
        'phone',
        'mobile',
        'email',
        'blood_group',
        'address_id',
    ];

    /**
     * @var array<int, string>
     */
    protected const ADDRESS_FIELDS = [
        'address_type',
        'address',
        'location',
        'territory',
        'postal_code',
        'country_id',
        'is_primary',
        'remarks',
    ];

    protected const RELATIONS = [
        'person',
        'person.addresses',
        'company',
        'department',
        'designation',
        'employmentType',
        'employmentStatus',
        'reportingTo.person',
    ];

    public function __construct(protected SequenceNumberService $sequenceNumbers)
    {
    }

    public function getAllEmployees(): Collection
    {
        return Employee::with(self::RELATIONS)
            ->join('persons', 'persons.id', '=', 'employees.person_id')
            ->orderBy('persons.first_name')
            ->select('employees.*')
            ->get();
    }

    public function getActiveEmployees(): Collection
    {
        return Employee::with(self::RELATIONS)
            ->whereHas('employmentStatus', fn ($query) => $query->where('is_terminal', false))
            ->get();
    }

    public function getEmployeeById(int $id): ?Employee
    {
        return Employee::with([...self::RELATIONS, 'directReports.person'])->find($id);
    }

    /**
     * Create an Employee together with its backing Person record, any
     * submitted addresses, and an auto-generated employee_code.
     *
     * @param array<string, mixed> $data
     */
    public function createEmployee(array $data): Employee
    {
        return DB::transaction(function () use ($data): Employee {
            $addresses = $data['addresses'] ?? [];

            $person = Person::create(Arr::only($data, self::PERSON_FIELDS));
            $this->syncAddresses($person, $addresses);

            $employee = Employee::create([
                ...Arr::except($data, [...self::PERSON_FIELDS, 'addresses']),
                'person_id' => $person->getKey(),
                'employee_code' => $this->sequenceNumbers->next('employee'),
            ]);

            return $employee->fresh(self::RELATIONS);
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateEmployee(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data): Employee {
            $addresses = $data['addresses'] ?? [];

            $employee->person?->update(Arr::only($data, self::PERSON_FIELDS));

            if ($employee->person) {
                $this->syncAddresses($employee->person, $addresses);
            }

            $employee->update(Arr::except($data, [...self::PERSON_FIELDS, 'addresses']));

            return $employee->fresh(self::RELATIONS);
        });
    }

    public function deleteEmployee(Employee $employee): bool
    {
        return (bool) $employee->delete();
    }

    /**
     * Create/update/remove a Person's addresses to match the submitted set.
     * Existing rows are matched by 'id'; anything no longer present is deleted.
     *
     * @param array<int, array<string, mixed>> $addresses
     */
    protected function syncAddresses(Person $person, array $addresses): void
    {
        $keptIds = [];

        foreach ($addresses as $addressData) {
            $id = $addressData['id'] ?? null;
            $payload = Arr::only($addressData, self::ADDRESS_FIELDS);

            $existing = $id ? $person->addresses()->whereKey($id)->first() : null;

            if ($existing) {
                $existing->update($payload);
                $keptIds[] = $existing->getKey();

                continue;
            }

            $keptIds[] = $person->addresses()->create($payload)->getKey();
        }

        $person->addresses()->whereNotIn('id', $keptIds)->delete();
    }
}

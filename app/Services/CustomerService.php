<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

class CustomerService
{
    public function getAllCustomers(): Collection
    {
        return Customer::with(['address.country', 'parent'])
            ->orderBy('name')
            ->get();
    }

    public function getActiveCustomers(): Collection
    {
        return Customer::with(['address.country', 'parent'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getCustomerById(int $id): ?Customer
    {
        return Customer::with(['address.country', 'parent', 'children'])->find($id);
    }

    public function createCustomer(array $data): Customer
    {
        return Customer::create($data)->fresh(['address.country', 'parent']);
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->fresh(['address.country', 'parent']);
    }

    public function addBranchToCustomer(Customer $customer, array $data): Customer
    {
        $branch = new Customer($data);
        $branch->parent()->associate($customer);
        $branch->save();

        return $branch->fresh(['address.country', 'parent']);
    }

    public function deleteCustomer(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }
}

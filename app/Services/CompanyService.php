<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class CompanyService
{
    public function getAllCompanies(): Collection
    {
        return Company::with(['parent', 'taxCountry'])
            ->orderBy('legal_name')
            ->get();
    }

    public function getActiveCompanies(): Collection
    {
        return Company::with(['parent', 'taxCountry'])
            ->where('status', Company::STATUS_ACTIVE)
            ->orderBy('legal_name')
            ->get();
    }

    public function getCompanyById(int $id): ?Company
    {
        return Company::with(['parent', 'children', 'taxCountry', 'phones', 'governmentRegistrations'])
            ->find($id);
    }

    public function createCompany(array $data): Company
    {
        return Company::create($data)->fresh(['parent', 'taxCountry']);
    }

    public function updateCompany(Company $company, array $data): Company
    {
        $company->update($data);

        return $company->fresh(['parent', 'taxCountry']);
    }

    public function addChildCompany(Company $parent, array $data): Company
    {
        $child = new Company($data);
        $child->parent()->associate($parent);
        $child->save();

        return $child->fresh(['parent', 'taxCountry']);
    }

    public function deleteCompany(Company $company): bool
    {
        return (bool) $company->delete();
    }
}

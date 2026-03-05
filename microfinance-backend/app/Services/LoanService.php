<?php

namespace App\Services;

use App\Repositories\Interfaces\LoanRepositoryInterface;

class LoanService
{
    protected $repository;

    public function __construct(LoanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllLoans()
    {
        return $this->repository->all();
    }

    public function getLoan($id)
    {
        return $this->repository->find($id);
    }

    public function createLoan(array $data)
    {
        // Add business logic for loan computation here
        return $this->repository->create($data);
    }

    public function updateLoan($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteLoan($id)
    {
        return $this->repository->delete($id);
    }

    public function approveLoan($id)
    {
        return $this->repository->update($id, ['status' => 'active']);
    }
}

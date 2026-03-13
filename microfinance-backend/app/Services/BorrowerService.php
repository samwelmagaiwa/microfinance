<?php

namespace App\Services;

use App\Repositories\Interfaces\BorrowerRepositoryInterface;

class BorrowerService
{
    protected $repository;

    public function __construct(BorrowerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllBorrowers($status = null)
    {
        return $this->repository->all($status);
    }

    public function getBorrower($id)
    {
        return $this->repository->find($id);
    }

    public function createBorrower(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateBorrower($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteBorrower($id)
    {
        return $this->repository->delete($id);
    }
}

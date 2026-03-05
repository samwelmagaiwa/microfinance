<?php

namespace App\Services;

use App\Repositories\Interfaces\PaymentRepositoryInterface;

class PaymentService
{
    protected $repository;

    public function __construct(PaymentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllPayments()
    {
        return $this->repository->all();
    }

    public function getPayment($id)
    {
        return $this->repository->find($id);
    }

    public function createPayment(array $data)
    {
        // Add business logic to update loan status and schedules
        return $this->repository->create($data);
    }

    public function updatePayment($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deletePayment($id)
    {
        return $this->repository->delete($id);
    }
}

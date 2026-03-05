<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    protected $model;

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with('loan.borrower')->get();
    }

    public function find($id)
    {
        return $this->model->with('loan.borrower')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $payment = $this->find($id);
        $payment->update($data);
        return $payment;
    }

    public function delete($id)
    {
        $payment = $this->find($id);
        return $payment->delete();
    }
}

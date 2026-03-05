<?php

namespace App\Repositories\Eloquent;

use App\Models\Loan;
use App\Repositories\Interfaces\LoanRepositoryInterface;

class LoanRepository implements LoanRepositoryInterface
{
    protected $model;

    public function __construct(Loan $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with('borrower')->get();
    }

    public function find($id)
    {
        return $this->model->with(['borrower', 'schedules', 'payments'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $loan = $this->find($id);
        $loan->update($data);
        return $loan;
    }

    public function delete($id)
    {
        $loan = $this->find($id);
        return $loan->delete();
    }
}

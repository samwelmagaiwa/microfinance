<?php

namespace App\Repositories\Eloquent;

use App\Models\Borrower;
use App\Repositories\Interfaces\BorrowerRepositoryInterface;

class BorrowerRepository implements BorrowerRepositoryInterface
{
    protected $model;

    public function __construct(Borrower $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $borrower = $this->find($id);
        $borrower->update($data);
        return $borrower;
    }

    public function delete($id)
    {
        $borrower = $this->find($id);
        return $borrower->delete();
    }
}

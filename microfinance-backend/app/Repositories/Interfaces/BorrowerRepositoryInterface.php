<?php

namespace App\Repositories\Interfaces;

interface BorrowerRepositoryInterface
{
    public function all($status = null);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}

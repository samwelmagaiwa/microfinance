<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\BorrowerService;
use Illuminate\Http\Request;

class BorrowerController extends Controller
{
    protected $service;

    public function __construct(BorrowerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getAllBorrowers()
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getBorrower($id)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:borrowers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'id_number' => 'required|string|unique:borrowers,id_number',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Borrower registered successfully.',
            'data' => $this->service->createBorrower($data)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Borrower information updated.',
            'data' => $this->service->updateBorrower($id, $request->all())
        ]);
    }

    public function destroy($id)
    {
        $this->service->deleteBorrower($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Borrower record deleted successfully.'
        ]);
    }
}

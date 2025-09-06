<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\EmployeeRepository;

class EmployeeController
{
    private EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function index(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $companyId = $user['company_id'] ?? 1; // Assume from user
        $employees = $this->employeeRepository->findAll($companyId);
        $response->getBody()->write(json_encode(array_map(fn($e) => $e->toArray(), $employees)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $employee = $this->employeeRepository->findById($args['id']);
        if (!$employee) {
            $response->getBody()->write(json_encode(['error' => 'Employee not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode($employee->toArray()));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $user = $request->getAttribute('user');
        $data['company_id'] = $user['company_id'] ?? 1;
        $employee = $this->employeeRepository->create($data);
        $response->getBody()->write(json_encode($employee->toArray()));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $employee = $this->employeeRepository->update($args['id'], $data);
        if (!$employee) {
            $response->getBody()->write(json_encode(['error' => 'Employee not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode($employee->toArray()));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $success = $this->employeeRepository->delete($args['id']);
        if (!$success) {
            $response->getBody()->write(json_encode(['error' => 'Employee not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(204);
    }
}
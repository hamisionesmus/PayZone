<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\LeaveRepository;
use App\Repositories\EmployeeRepository;

class LeaveController
{
    private LeaveRepository $leaveRepository;
    private EmployeeRepository $employeeRepository;

    public function __construct(LeaveRepository $leaveRepository, EmployeeRepository $employeeRepository)
    {
        $this->leaveRepository = $leaveRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function index(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $companyId = $user['company_id'] ?? 1;

        $params = $request->getQueryParams();
        $limit = $params['limit'] ?? 10;
        $offset = $params['offset'] ?? 0;

        $leaves = $this->leaveRepository->findAll($companyId, $limit, $offset);

        $response->getBody()->write(json_encode(array_map(function($leave) {
            return [
                'id' => $leave->id,
                'employee_id' => $leave->employee_id,
                'employee_name' => $leave->first_name . ' ' . $leave->last_name,
                'employee_email' => $leave->employee_email,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'type' => $leave->type,
                'status' => $leave->status,
                'reason' => $leave->reason,
                'created_at' => $leave->created_at,
                'updated_at' => $leave->updated_at
            ];
        }, $leaves)));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $leave = $this->leaveRepository->findById($args['id']);

        if (!$leave) {
            $response->getBody()->write(json_encode(['error' => 'Leave request not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode([
            'id' => $leave->id,
            'employee_id' => $leave->employee_id,
            'employee_name' => $leave->first_name . ' ' . $leave->last_name,
            'employee_email' => $leave->employee_email,
            'start_date' => $leave->start_date,
            'end_date' => $leave->end_date,
            'type' => $leave->type,
            'status' => $leave->status,
            'reason' => $leave->reason,
            'created_at' => $leave->created_at,
            'updated_at' => $leave->updated_at
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        // Validate required fields
        if (!isset($data['employee_id'], $data['start_date'], $data['end_date'], $data['type'])) {
            $response->getBody()->write(json_encode(['error' => 'Missing required fields']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Validate dates
        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            $response->getBody()->write(json_encode(['error' => 'Start date cannot be after end date']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $leave = $this->leaveRepository->create($data);

        $response->getBody()->write(json_encode([
            'id' => $leave->id,
            'employee_id' => $leave->employee_id,
            'start_date' => $leave->start_date,
            'end_date' => $leave->end_date,
            'type' => $leave->type,
            'status' => $leave->status,
            'reason' => $leave->reason,
            'created_at' => $leave->created_at
        ]));

        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $leave = $this->leaveRepository->update($args['id'], $data);

        if (!$leave) {
            $response->getBody()->write(json_encode(['error' => 'Leave request not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode([
            'id' => $leave->id,
            'employee_id' => $leave->employee_id,
            'start_date' => $leave->start_date,
            'end_date' => $leave->end_date,
            'type' => $leave->type,
            'status' => $leave->status,
            'reason' => $leave->reason,
            'updated_at' => $leave->updated_at
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function approve(Request $request, Response $response, array $args): Response
    {
        $leave = $this->leaveRepository->updateStatus($args['id'], 'approved');

        if (!$leave) {
            $response->getBody()->write(json_encode(['error' => 'Leave request not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode([
            'message' => 'Leave request approved successfully',
            'leave' => [
                'id' => $leave->id,
                'status' => $leave->status,
                'updated_at' => $leave->updated_at
            ]
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function reject(Request $request, Response $response, array $args): Response
    {
        $leave = $this->leaveRepository->updateStatus($args['id'], 'rejected');

        if (!$leave) {
            $response->getBody()->write(json_encode(['error' => 'Leave request not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode([
            'message' => 'Leave request rejected',
            'leave' => [
                'id' => $leave->id,
                'status' => $leave->status,
                'updated_at' => $leave->updated_at
            ]
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $success = $this->leaveRepository->delete($args['id']);

        if (!$success) {
            $response->getBody()->write(json_encode(['error' => 'Leave request not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        return $response->withStatus(204);
    }

    public function getMyLeaves(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');

        // Find employee by user ID
        $stmt = $this->employeeRepository->pdo->prepare("SELECT id FROM employees WHERE user_id = ?");
        $stmt->execute([$user['sub']]);
        $employee = $stmt->fetch();

        if (!$employee) {
            $response->getBody()->write(json_encode(['error' => 'Employee not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $leaves = $this->leaveRepository->findByEmployee($employee['id']);

        $response->getBody()->write(json_encode(array_map(function($leave) {
            return [
                'id' => $leave->id,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'type' => $leave->type,
                'status' => $leave->status,
                'reason' => $leave->reason,
                'created_at' => $leave->created_at,
                'updated_at' => $leave->updated_at
            ];
        }, $leaves)));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPendingRequests(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $companyId = $user['company_id'] ?? 1;

        $leaves = $this->leaveRepository->getPendingRequests($companyId);

        $response->getBody()->write(json_encode(array_map(function($leave) {
            return [
                'id' => $leave->id,
                'employee_id' => $leave->employee_id,
                'employee_name' => $leave->first_name . ' ' . $leave->last_name,
                'employee_email' => $leave->employee_email,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'type' => $leave->type,
                'status' => $leave->status,
                'reason' => $leave->reason,
                'created_at' => $leave->created_at
            ];
        }, $leaves)));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
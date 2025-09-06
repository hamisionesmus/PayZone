-- Seed data for Payroll Management System

INSERT INTO companies (name, address, phone, email) VALUES
('TechCorp', '123 Tech Street', '123-456-7890', 'info@techcorp.com');

INSERT INTO roles (name) VALUES
('Admin'), ('HR'), ('Employee');

INSERT INTO permissions (name) VALUES
('create_user'), ('view_employee'), ('edit_employee'), ('delete_employee');

INSERT INTO role_permissions (role_id, permission_id) VALUES
(1,1), (1,2), (1,3), (1,4), (2,2), (2,3), (3,2);

INSERT INTO users (company_id, username, email, password_hash, role_id) VALUES
(1, 'admin', 'admin@techcorp.com', '$2y$10$examplehash', 1),
(1, 'hr', 'hr@techcorp.com', '$2y$10$examplehash', 2),
(1, 'employee1', 'emp1@techcorp.com', '$2y$10$examplehash', 3);

INSERT INTO employees (company_id, user_id, first_name, last_name, email, hire_date, salary, position, department) VALUES
(1, 3, 'John', 'Doe', 'emp1@techcorp.com', '2023-01-01', 50000.00, 'Developer', 'IT');
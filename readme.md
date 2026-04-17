HR Login
email - eugochukwu910@gmail.com
password - Awesome619$
staff_id - STAF001

Retail HR Login
email - retail@company.com
password - retail123
staff_id - RET-HR001

RETAIL USER
email - retailuser@example.com
password - retailuser123
staff_id - RET-002

email - user@example.com
password - user123@$
staff_id - STAF002
for other login
the password is -  Password123
then get the email and password from the database

-- Add HR type column
ALTER TABLE users ADD COLUMN hr_type ENUM('MAIN', 'RETAIL', 'CORPORATE') NULL DEFAULT NULL;
ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') NULL DEFAULT 'active';
ALTER TABLE payslip ADD COLUMN extra_data JSON DEFAULT NULL;

ALTER TABLE payroll_batches ADD COLUMN hr_type VARCHAR(20) DEFAULT 'MAIN';

-- Create Retail HR user (password: retail123)
INSERT INTO users (name, staff_id, email, password, role, hr_type, created_at) VALUES 
('Retail HR', 'RET-HR001', 'retail@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'RETAIL', NOW())
ON DUPLICATE KEY UPDATE hr_type='RETAIL';
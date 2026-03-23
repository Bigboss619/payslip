USE nepal_payslip;

CREATE TABLE IF NOT EXISTS departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) UNIQUE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data
INSERT IGNORE INTO departments (name) VALUES 
('IT'),
('HR'),
('Finance'),
('Sales'),
('Marketing');

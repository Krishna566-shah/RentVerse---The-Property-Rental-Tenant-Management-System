CREATE DATABASE IF NOT EXISTS rentverse;
USE rentverse;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Agent', 'Owner', 'Tenant', 'Technician') NOT NULL
);

CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    location VARCHAR(255) NOT NULL,
    owner_id INT NOT NULL,
    image VARCHAR(255),
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE property_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    owner_id INT NOT NULL,
    tenant_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE maintenance_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    tenant_id INT NOT NULL,
    issue TEXT NOT NULL,
    status ENUM('Pending', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    property_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    screenshot_path VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Default Admin (Password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('System Admin', 'admin@rentverse.com', '$2y$10$e/o.v8Jv9H/.lJ1n4.Qk.eDqY8P.1V9eWq8v.3D/8Jv9H/.lJ1n4.', 'Agent');
-- NAM Builders and Supply Corp Database

-- Create Database
CREATE DATABASE IF NOT EXISTS nam_builders;
USE nam_builders;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Clients Table
CREATE TABLE IF NOT EXISTS clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(150) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_name VARCHAR(150) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    icon_class VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    service_needed VARCHAR(150),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KLm', 'admin@nambuilders.com');

-- Insert sample services
INSERT INTO services (service_name, description, sort_order, is_active) VALUES
('General Construction', 'Complete construction solutions for residential, commercial, and industrial projects with expert project management.', 1, 1),
('Renovation & Remodeling', 'Transform your space with our professional renovation and remodeling services tailored to your needs.', 2, 1),
('Electrical Systems', 'Expert electrical installation, maintenance, and repair services ensuring safety and efficiency.', 3, 1),
('Fire Protection', 'Comprehensive fire protection systems installation and maintenance to keep your property safe.', 4, 1),
('Steel Fabrication', 'Custom steel fabrication services for structural and architectural applications.', 5, 1),
('Office Fit-Outs', 'Complete office design and fit-out solutions creating productive work environments.', 6, 1),
('Building Maintenance', 'Regular maintenance services to keep your building in optimal condition year-round.', 7, 1),
('Supply Services', 'Construction materials, electrical components, PPE, and office supplies delivered on time.', 8, 1);

-- Insert sample clients (placeholder entries)
INSERT INTO clients (client_name, description, sort_order, is_active) VALUES
('Client 1', 'Client Logo 1', 1, 1),
('Client 2', 'Client Logo 2', 2, 1),
('Client 3', 'Client Logo 3', 3, 1),
('Client 4', 'Client Logo 4', 4, 1),
('Client 5', 'Client Logo 5', 5, 1),
('Client 6', 'Client Logo 6', 6, 1),
('Client 7', 'Client Logo 7', 7, 1),
('Client 8', 'Client Logo 8', 8, 1);

CREATE TABLE IF NOT EXISTS neet_jee_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    target_exam ENUM('NEET', 'JEE Main', 'JEE Advanced') NOT NULL,
    preferred_city ENUM('Delhi', 'Noida') NOT NULL,
    current_class VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS coaching_institutes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coaching_name VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    area VARCHAR(100) NOT NULL,
    courses_offered VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    fee_range VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS applicants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    resume_path VARCHAR(255),
    score INTEGER,
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS job_applications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    applicant_id INTEGER,
    job_title VARCHAR(255),
    department VARCHAR(100),
    match_score DECIMAL(5,2),
    ai_feedback TEXT,
    recruiter_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES applicants(id)
);

CREATE TABLE IF NOT EXISTS jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    description TEXT,
    requirements TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample job
INSERT INTO jobs (title, department, description, requirements) VALUES (
    'Senior PHP Developer',
    'Engineering',
    'We are looking for a skilled PHP developer with experience in Laravel, REST APIs, and modern web development practices.',
    'PHP, Laravel, MySQL, JavaScript, REST APIs, Git, 3+ years experience'
);

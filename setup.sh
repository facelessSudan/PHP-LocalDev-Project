#!/bin/bash

# Local Resume AI Agent Setup Script
# This script will help you set up the entire project locally

echo " Setting up Local Resume AI Agent..."

# Check prerequisites
echo " Checking prerequisites..."

# Check PHP
if ! command -v php &> /dev/null; then
    echo " PHP is not installed. Please install PHP 8.0 or higher."
    exit 1
fi

# Check Composer
if ! command -v composer &> /dev/null; then
    echo " Composer is not installed. Please install Composer."
    exit 1
fi

# Check Node.js
if ! command -v node &> /dev/null; then
    echo " Node.js is not installed. Please install Node.js 16 or higher."
    exit 1
fi

# Check Node.js version(must be >= 20.18.1)
REQUIRED_NODE_MAJOR=20
REQUIRED_NODE_MINOR=18
REQUIRED_NODE_PATCH=1

if command -v node &> /dev/null; then
    NODE_VERSION=$(node -v | sed 's/v//')
    NODE_MAJOR=$(echo "$NODE_VERSION" | cut -d. -f1)
    NODE_MINOR=$(echo "$NODE_VERSION" | cut -d. -f2)
    NODE_PATCH=$(echo "$NODE_VERSION" | cut -d. -f3)

    if [ "$NODE_MAJOR" -lt "$REQUIRED_NODE_MAJOR" ] || \
       { [ "$NODE_MAJOR" -eq "$REQUIRED_NODE_MAJOR" ] && [ "$NODE_MINOR" -lt "$REQUIRED_NODE_MINOR" ]; } || \
       { [ "$NODE_MAJOR" -eq "$REQUIRED_NODE_MAJOR" ] && [ "$NODE_MINOR" -eq "$REQUIRED_NODE_MINOR" ] && [ "$NODE_PATCH" -lt "$REQUIRED_NODE_PATCH" ]; }; then
        echo " Node.js >= 20.18.1 is required. Found v$NODE_VERSION"
        echo " Installing Node.js 20.18.1 via nvm..."

        if ! command -v nvm &> /dev/null; then
            curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
            export NVM_DIR="$HOME/.nvm"
            [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
        fi

        nvm install 20.18.1
        nvm use 20.18.1
        nvm alias default 20.18.1
    else
        echo " Node.js version is compatible: v$NODE_VERSION"
    fi
else
    echo " Node.js is not installed. Installing Node.js 20.18.1..."
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
    export NVM_DIR="$HOME/.nvm"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
    nvm install 20.18.1
    nvm use 20.18.1
    nvm alias default 20.18.1
fi
echo " All prerequisites are installed!"

# Fix global npm install permissions
NPM_PREFIX=$(npm config get prefix)
if [ "$NPM_PREFIX" = "/usr/local" ]; then
    echo " Fixing global npm permissions..."
    mkdir -p ~/.npm-global
    npm config set prefix '~/.npm-global'
    export PATH="$HOME/.npm-global/bin:$PATH"
    echo 'export PATH="$HOME/.npm-global/bin:$PATH"' >> ~/.bashrc
    source ~/.bashrc
fi

# Create project directories
echo " Creating project directories..."
mkdir -p database
mkdir -p logs
mkdir -p uploads/resumes
mkdir -p config

# Install PHP dependencies
echo " Installing PHP dependencies..."
composer install

# Install n8n locally (if not already installed)
if [ ! -f node_modules/.bin/n8n ]; then
    echo " Installing n8n locally..."
    npm install n8n
fi

# Create SQLite database
echo " Creating SQLite database..."
cat > config/schema.sql << 'EOF'
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
EOF

sqlite3 database/recruitment.db < config/schema.sql

# Create start script
echo " Creating start script..."
cat > start.sh << 'EOF'
#!/bin/bash

echo "Starting Local Resume AI Agent services..."

# Function to kill process on port
kill_port() {
    local port=$1
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null ; then
        echo "Killing process on port $port"
        kill -9 $(lsof -Pi :$port -sTCP:LISTEN -t)
    fi
}

# Kill existing processes
kill_port 8000
kill_port 5678
kill_port 1025
kill_port 8025

# Start services
echo "Starting MailHog..."
mailhog &
MAILHOG_PID=$!

echo "Starting n8n..."
n8n start &
N8N_PID=$!

echo "Starting PHP server..."
php -S localhost:8000 -t public &
PHP_PID=$!

echo ""
echo " All services started!"
echo ""
echo " Access points:"
echo "   - Application: http://localhost:8000"
echo "   - n8n Workflow: http://localhost:5678"
echo "   - MailHog UI: http://localhost:8025"
echo ""
echo "Press Ctrl+C to stop all services"

# Wait for interrupt
trap 'echo "Stopping services..."; kill $MAILHOG_PID $N8N_PID $PHP_PID; exit' INT
wait
EOF

chmod +x start.sh

# Install MailHog (if not already installed)
if ! command -v mailhog &> /dev/null; then
    echo " Installing MailHog..."
    # For Linux/MacOS
    if [[ "$OSTYPE" == "darwin"* ]]; then
        brew install mailhog
    else
        # For Linux
        wget https://github.com/mailhog/MailHog/releases/download/v1.0.1/MailHog_linux_amd64
        chmod +x MailHog_linux_amd64
        sudo mv MailHog_linux_amd64 /usr/local/bin/mailhog
    fi
fi

# Create test file
echo " Creating test file..."
cat > test_local.php << 'EOF'
<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Test database connection
try {
    $db = new PDO('sqlite:' . __DIR__ . '/database/recruitment.db');
    echo " Database connection successful\n";
    
    // Test query
    $stmt = $db->query("SELECT COUNT(*) FROM jobs");
    $count = $stmt->fetchColumn();
    echo " Found $count jobs in database\n";
    
} catch (Exception $e) {
    echo " Database error: " . $e->getMessage() . "\n";
}

// Test file system
$uploadDir = __DIR__ . '/uploads/resumes/';
if (is_writable($uploadDir)) {
    echo " Upload directory is writable\n";
} else {
    echo " Upload directory is not writable\n";
}

echo "\n Basic tests completed!\n";
EOF


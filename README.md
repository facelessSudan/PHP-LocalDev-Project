# Local Resume AI Agent Setup

I developed this guide to  help you run the entire Resume AI Agent project locally without any external services or subscriptions. This idea was informed by the fact that I needed to pay for a number of service subscritpions for my project to run on cloud.

## Prerequisites
- PHP 8.x+
- Composer
- Node.js 16+ (for n8n)
- SQLite (comes with PHP)
- Local web server

## Replace Cloud Services with Local Alternatives

### 1. n8n (Local Installation)
Instead of using n8n cloud, run it locally:

```bash
# Install n8n globally
npm install -g n8n

# Start n8n
n8n start

# Access n8n at http://localhost:5678
```

### 2. Google Sheets replaced by Local SQLite Database
Create a local database to store applicant data:

### 3. Email Service replaced by Local Mail (using PHPMailer with MailHog)
Install MailHog for local email testing:

```bash
# On macOS
brew install mailhog

# On Windows/Linux - Download from https://github.com/mailhog/MailHog
# Start MailHog
mailhog

# SMTP: localhost:1025
# Web UI: http://localhost:8025
```

### 4. AI Scoring → Local LLM or Rule-Based Scoring
Since it's for development, use a simple rule-based scoring system or local LLM:

#### Alternatively use Local LLM (I chose using Ollama)
```bash
# Install Ollama
curl -fsSL https://ollama.ai/install.sh | sh

# Pull a model
ollama pull llama2

# Run Ollama
ollama serve
```

## Folder Structure

```
PHP-Directory/
├── composer.json
├── composer.lock
├── vendor/
├── .env.local                    # Local environment config
├── setup.sh
├── public/
│   ├── index.php
│   └── .htaccess
├── src/
│   ├── AIrecruitementAgent.php
│   ├── webhook_handler.php
│   ├── form_handler.php
│   ├── views/
│   │   ├── application_form.php
│   │   ├── thank_you_note.php
│   │   └── error.php
│   └── Services/
│       ├── DatabaseService.php   # SQLite connection
│       ├── LocalMailService.php  # PHPMailer with MailHog
│       ├── LocalAIService.php    # Local AI scoring
│       └── FileStorageService.php # Local file handling
├── config/
│   └── database.php
├── database/
│   └── recruitment.db
├── logs/
├── uploads/
│   └── resumes/
├── tests/
│   └── test_local_workflow.php
└── README.md
```

```

## n8n Workflow Configuration

Create a local n8n workflow with these nodes:

1. **Webhook Node**: Receive data from PHP
2. **Function Node**: Parse resume and extract text
3. **HTTP Request Node**: Call local AI service for scoring
4. **IF Node**: Check if score meets threshold
5. **Email Node**: Send to candidate (using MailHog)
6. **Email Node**: Notify recruiter
7. **SQLite Node**: Update database

## Running the Project Locally

1. **Start Local Services**:
```bash
# Terminal 1: Start PHP server
php -S localhost:8000 -t public

# Terminal 2: Start n8n
n8n start

# Terminal 3: Start MailHog
mailhog

# Terminal 4: Start Ollama (if using)
ollama serve
```

2. **Initialize Database**:
```bash
sqlite3 database/recruitment.db < config/schema.sql
```

3. **Configure n8n Workflow**:
- Open http://localhost:5678
- Import the workflow JSON (provided)
- Update webhook URL in PHP config

4. **Test the Application**:
- Go to http://localhost:8000
- Upload a test resume
- Check MailHog at http://localhost:8025
- View n8n execution at http://localhost:5678

## Advantages of This Local Setup

1. **No External Dependencies**: Everything runs on your machine
2. **No API Keys Required**: No need for Google, OpenAI, or other service credentials
3. **Full Control**: Debug and modify any component
4. **Cost-Free**: No subscription fees
5. **Privacy**: All data stays on your local machine
6. **Educational**: Perfect for learning and showcasing

This setup provides a complete, functional system that runs entirely on your local machine, perfect for a school project demonstration.

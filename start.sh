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
npx n8n start &  
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

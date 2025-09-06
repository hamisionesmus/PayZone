#!/bin/bash

# PayZone Deployment Script
# This script handles deployment to production environment

set -e

echo "🚀 Starting PayZone deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
DOCKER_USERNAME=${DOCKER_USERNAME:-""}
DOCKER_PASSWORD=${DOCKER_PASSWORD:-""}
ENVIRONMENT=${1:-"production"}
TAG=${2:-"latest"}

# Validate inputs
if [ -z "$DOCKER_USERNAME" ] || [ -z "$DOCKER_PASSWORD" ]; then
    echo -e "${RED}Error: DOCKER_USERNAME and DOCKER_PASSWORD must be set${NC}"
    exit 1
fi

echo -e "${YELLOW}Deploying to ${ENVIRONMENT} environment with tag ${TAG}${NC}"

# Login to Docker Hub
echo "🔐 Logging into Docker Hub..."
echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USERNAME" --password-stdin

# Build and push images
echo "🏗️ Building and pushing Docker images..."

# Backend
echo "Building backend image..."
docker build -t "$DOCKER_USERNAME/payzone-backend:$TAG" ./backend
docker push "$DOCKER_USERNAME/payzone-backend:$TAG"

# Frontend
echo "Building frontend image..."
docker build -t "$DOCKER_USERNAME/payzone-frontend:$TAG" ./frontend
docker push "$DOCKER_USERNAME/payzone-frontend:$TAG"

# Deploy based on environment
case $ENVIRONMENT in
    "staging")
        echo "🚀 Deploying to staging..."
        # Add your staging deployment commands here
        # Example: kubectl apply -f k8s/staging/
        echo "Staging deployment completed"
        ;;
    "production")
        echo "🚀 Deploying to production..."
        # Add your production deployment commands here
        # Example: kubectl apply -f k8s/production/
        echo "Production deployment completed"
        ;;
    *)
        echo -e "${RED}Error: Unknown environment ${ENVIRONMENT}${NC}"
        exit 1
        ;;
esac

# Run database migrations if needed
echo "🗄️ Running database migrations..."
# Add database migration commands here
# Example: docker run --rm your-migration-image

# Health check
echo "🔍 Running health checks..."
# Add health check commands here
# Example: curl -f http://your-app/health || exit 1

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo "🌐 Application should be available at: http://your-domain.com"
echo "📊 Monitor the application health and logs"
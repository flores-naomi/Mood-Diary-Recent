# 1. Use Railway FrankenPHP base image
FROM ghcr.io/railwayapp/frankenphp:latest

# 2. Install PDO MySQL driver
RUN apk add --no-cache php8-pdo_mysql

# 3. Set working directory in container
WORKDIR /app

# 4. Copy all project files into container
COPY . .

# 5. Expose port 8080
EXPOSE 8080

# 6. Start FrankenPHP server
CMD ["frankenphp", "-S", "0.0.0.0:8080", "index.php"]

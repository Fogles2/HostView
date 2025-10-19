# HostView API Documentation

RESTful API endpoints for HostView integration.

## Authentication

All API requests require authentication via session cookies or API key.

### Session Authentication
Login via web interface and use session cookies for API requests.

### API Key Authentication
```http
Authorization: Bearer YOUR_API_KEY
```

## Base URL
```
https://yourdomain.com/api
```

## Endpoints

### Dashboard Statistics
```http
GET /dashboard/stats
```

**Response:**
```json
{
  "success": true,
  "data": {
    "clients": 150,
    "services": 320,
    "revenue": 15750.50,
    "invoices": 89
  },
  "timestamp": 1635789600
}
```

### Clients

#### List Clients
```http
GET /clients?page=1&limit=20
```

#### Get Client Details
```http
GET /clients/{id}
```

### Invoices

#### List Invoices
```http
GET /invoices?page=1&status=paid
```

#### Get Invoice Details
```http
GET /invoices/{id}
```

### Health Check
```http
GET /health
```

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2025-10-18T20:45:00Z",
  "version": "1.0.0",
  "checks": {
    "database": "healthy",
    "fossbilling": "healthy"
  }
}
```

## Error Responses

```json
{
  "success": false,
  "error": "Error message",
  "code": "ERROR_CODE"
}
```

### Status Codes
- `200` - Success
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `500` - Internal Server Error

## Rate Limiting

- **Limit**: 60 requests per minute
- **Headers**: `X-RateLimit-Limit`, `X-RateLimit-Remaining`
# FOSSBilling Integration Setup

Step-by-step guide to integrate HostView with your FOSSBilling instance.

## Prerequisites

- Active FOSSBilling installation
- Admin access to FOSSBilling
- API access enabled

## FOSSBilling Configuration

### 1. Enable API Access

1. Login to FOSSBilling admin panel
2. Navigate to **System** → **Settings** → **API**
3. Enable **Admin API**
4. Set **Allow API access** to **Yes**

### 2. Generate API Key

1. Go to **System** → **Staff** → **Manage**
2. Edit your admin user
3. Generate new API key
4. Copy the API key

### 3. Configure HostView

Update your `.env` file:

```env
# Your FOSSBilling Configuration
FOSSBILLING_ENABLED=true
FOSSBILLING_URL=https://billing.turnpage.io
FOSSBILLING_API_KEY=trGUFHOHLOt19wqDLEkhORT4iD8JNTuR
FOSSBILLING_USERNAME=admin
FOSSBILLING_TIMEOUT=30
FOSSBILLING_CACHE_TTL=300
```

## Testing Integration

### 1. Command Line Test

```bash
# Test API connection
curl -X POST "https://billing.turnpage.io/api/admin/staff/profile" \
  -H "Authorization: Basic $(echo -n 'admin:trGUFHOHLOt19wqDLEkhORT4iD8JNTuR' | base64)" \
  -H "Content-Type: application/json"
```

### 2. HostView Test

Visit: `https://yourdomain.com/api/fossbilling/test`

**Expected Response:**
```json
{
  "success": true,
  "duration": "245ms",
  "data": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@turnpage.io"
  }
}
```

## Available API Endpoints

HostView integrates with these FOSSBilling endpoints:

- `/api/admin/staff/profile` - Admin profile
- `/api/admin/client/get_list` - Client list
- `/api/admin/client/get` - Client details
- `/api/admin/invoice/get_list` - Invoice list
- `/api/admin/invoice/get` - Invoice details
- `/api/admin/order/get_list` - Order/service list
- `/api/admin/product/get_list` - Product list
- `/api/admin/stats/get_summary` - System statistics

## Data Synchronization

### Cache Settings

- **Dashboard Stats**: 5 minutes
- **Client List**: 15 minutes
- **Invoice Data**: 10 minutes
- **Product List**: 30 minutes

### Auto-Refresh

- Dashboard updates every 30 seconds
- Manual refresh available on all pages
- Failed requests retry automatically

## Troubleshooting

### Common Issues

1. **"Unauthorized" Error**
   - Check API key in FOSSBilling
   - Verify admin username
   - Ensure API access is enabled

2. **"Connection Failed"**
   - Check FOSSBilling URL
   - Verify SSL certificate
   - Check firewall settings

3. **"No Data Available"**
   - Verify FOSSBilling has data
   - Check API permissions
   - Review error logs

### Debug Mode

Enable debugging in `.env`:
```env
APP_DEBUG=true
FOSSBILLING_DEBUG=true
```

### Log Files

- Application: `storage/logs/app.log`
- FOSSBilling API: `storage/logs/fossbilling_api.log`

For additional support, contact [support@turnpage.io](mailto:support@turnpage.io).
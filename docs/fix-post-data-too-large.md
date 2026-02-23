# Fix: "The POST data is too large" on File Upload

## Problem

When uploading a file larger than **~8MB** (specifically a 12MB file in this case), the application threw the error:

```
The POST data is too large.
```

This error occurs **before Laravel even processes the request** — it is rejected at the PHP level.

---

## Root Cause

PHP has two built-in INI directives that limit file uploads:

| Directive             | Default / Found Value | Role                                                               |
| --------------------- | --------------------- | ------------------------------------------------------------------ |
| `upload_max_filesize` | `2M`                  | Maximum size of a **single uploaded file**                         |
| `post_max_size`       | `8M`                  | Maximum size of the **entire HTTP POST body** (file + form fields) |

Since the file was **12MB**, it exceeded both limits. PHP silently drops the upload data and Laravel receives an empty request, returning this error.

> **Note:** The Laravel validation rule in `FileController.php` was already set to `max:102400` (100MB), so **no Laravel code changes were needed** — the bottleneck was entirely at the PHP configuration level.

---

## Environment

- **PHP Version:** 8.4 (CLI)
- **Active `php.ini`:** `/etc/php/8.4/cli/php.ini`
- **Dev Server:** `php artisan serve` (uses the CLI `php.ini`)

---

## Fix Applied

### Step 1 — Identify the active `php.ini`

```bash
php --ini
# Output: Loaded Configuration File: /etc/php/8.4/cli/php.ini
```

### Step 2 — Verify the current limits

```bash
php -r "echo ini_get('post_max_size') . PHP_EOL . ini_get('upload_max_filesize');"
# Output:
# 8M
# 2M
```

### Step 3 — Update `php.ini` limits

```bash
sudo sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 128M/' /etc/php/8.4/cli/php.ini
sudo sed -i 's/^post_max_size = .*/post_max_size = 256M/' /etc/php/8.4/cli/php.ini
```

### Step 4 — Verify the new limits

```bash
php -r "echo ini_get('post_max_size') . PHP_EOL . ini_get('upload_max_filesize');"
# Output:
# 256M
# 128M
```

### Step 5 — Restart `php artisan serve`

Since `php artisan serve` caches PHP settings at startup, the server must be restarted to apply the new values:

```bash
pkill -f "artisan serve"
php artisan serve
```

---

## Result

| Directive             | Before | After  |
| --------------------- | ------ | ------ |
| `upload_max_filesize` | `2M`   | `128M` |
| `post_max_size`       | `8M`   | `256M` |

File uploads up to **128MB** now work correctly.

---

## Important Rules

- `post_max_size` must **always be larger** than `upload_max_filesize`, because the POST body contains the file data plus any additional form fields.
- Any change to `php.ini` requires a **server restart** to take effect.
- If you later deploy to a production server (Apache/Nginx + PHP-FPM), you will need to update the corresponding `php.ini` for that environment (e.g. `/etc/php/8.4/fpm/php.ini`) and reload PHP-FPM:
    ```bash
    sudo systemctl reload php8.4-fpm
    ```

---

## Related Files

| File                                      | Notes                                                                       |
| ----------------------------------------- | --------------------------------------------------------------------------- |
| `/etc/php/8.4/cli/php.ini`                | PHP config file that was modified                                           |
| `app/Http/Controllers/FileController.php` | Laravel validation — already set to `max:102400` (100MB), no changes needed |

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Test Playground</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
            margin: 0;
            padding: 24px;
        }
        .container {
            max-width: 980px;
            margin: 0 auto;
            display: grid;
            gap: 20px;
        }
        .card {
            background: white;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        h2, h3 {
            margin-top: 0;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            box-sizing: border-box;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }
        button {
            padding: 10px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 8px;
            margin-bottom: 8px;
            background: #2563eb;
            color: white;
        }
        button.secondary {
            background: #4b5563;
        }
        button.danger {
            background: #dc2626;
        }
        pre {
            background: #111827;
            color: #f9fafb;
            padding: 12px;
            border-radius: 6px;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        .hint {
            color: #6b7280;
            font-size: 13px;
            margin-top: -8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Authentication Test Playground</h2>
            <p class="hint">Use this page to test the full sign-in module: register, login, fetch profile, logout, forgot password, and reset password.</p>
            <pre id="result">Ready.</pre>
        </div>

        <div class="grid">
            <div class="card">
                <h3>Register</h3>
                <form id="registerForm">
                    <label>Name</label>
                    <input type="text" name="name" required>
                    <label>Email</label>
                    <input type="email" name="email" required>
                    <label>Password</label>
                    <input type="password" name="password" required>
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" required>
                    <label>Role</label>
                    <select name="role">
                        <option value="player">player</option>
                        <option value="organizer">organizer</option>
                        <option value="admin">admin</option>
                    </select>
                    <button type="submit">Register</button>
                </form>
            </div>

            <div class="card">
                <h3>Login</h3>
                <form id="loginForm">
                    <label>Email</label>
                    <input type="email" name="email" required>
                    <label>Password</label>
                    <input type="password" name="password" required>
                    <button type="submit">Login</button>
                    <button type="button" id="meBtn" class="secondary">Fetch /api/me</button>
                    <button type="button" id="logoutBtn" class="danger">Logout</button>
                </form>
            </div>

            <div class="card">
                <h3>Forgot Password</h3>
                <form id="forgotForm">
                    <label>Email</label>
                    <input type="email" name="email" required>
                    <button type="submit">Send Reset Link</button>
                </form>
            </div>

            <div class="card">
                <h3>Reset Password</h3>
                <form id="resetForm">
                    <label>Token</label>
                    <input type="text" name="token" required>
                    <label>Email</label>
                    <input type="email" name="email" required>
                    <label>New Password</label>
                    <input type="password" name="password" required>
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" required>
                    <button type="submit">Reset Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const result = document.getElementById('result');
        let token = null;

        function showResult(data) {
            result.textContent = JSON.stringify(data, null, 2);
        }

        async function request(url, options = {}) {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': 'Bearer ' + token } : {})
                },
                ...options
            });

            const data = await response.json().catch(() => ({}));
            showResult(data);
            return { response, data };
        }

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = Object.fromEntries(new FormData(e.target));
            const { data } = await request('{{ url('/api/register') }}', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            if (data.token) {
                token = data.token;
            }
        });

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = Object.fromEntries(new FormData(e.target));
            const { data } = await request('{{ url('/api/login') }}', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            if (data.token) {
                token = data.token;
            }
        });

        document.getElementById('meBtn').addEventListener('click', async () => {
            if (!token) {
                showResult({ message: 'No token available. Login first.' });
                return;
            }
            await request('{{ url('/api/me') }}');
        });

        document.getElementById('logoutBtn').addEventListener('click', async () => {
            if (!token) {
                showResult({ message: 'No token available. Login first.' });
                return;
            }
            const { data } = await request('{{ url('/api/logout') }}', { method: 'POST' });
            if (data.message) {
                token = null;
            }
        });

        document.getElementById('forgotForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = Object.fromEntries(new FormData(e.target));
            await request('{{ url('/api/forgot-password') }}', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
        });

        document.getElementById('resetForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = Object.fromEntries(new FormData(e.target));
            await request('{{ url('/api/reset-password') }}', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
        });
    </script>
</body>
</html>

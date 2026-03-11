<?php
include 'admin_check.php';
include "admin_db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Redirect Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .status {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #4361ee;
            color: white;
        }
        table tr:hover {
            background: #f9f9f9;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4361ee;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #3f37c9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✓ Redirect Issues Fixed!</h1>
        
        <div class="status success">
            <strong>✓ Fixed:</strong> All broken redirect URLs have been corrected
        </div>
        
        <h2>Issues Resolved:</h2>
        <table>
            <tr>
                <th>File</th>
                <th>Issue</th>
                <th>Fix</th>
                <th>Status</th>
            </tr>
            <tr>
                <td><code>admin_delete_user.php</code></td>
                <td>Redirected to non-existent <code>admin_users.php</code></td>
                <td>Now redirects to <code>admin_user.php</code></td>
                <td>✓ Fixed</td>
            </tr>
            <tr>
                <td><code>admin_update_user.php</code></td>
                <td>When user not found: redirected to <code>admin_users.php</code></td>
                <td>Now redirects to <code>admin_user.php</code></td>
                <td>✓ Fixed</td>
            </tr>
            <tr>
                <td><code>admin_delete_assignment.php</code></td>
                <td>None - was correct</td>
                <td>Already redirects to <code>admin_dashboard.php</code></td>
                <td>✓ OK</td>
            </tr>
            <tr>
                <td><code>admin_add_user.php</code></td>
                <td>None - was correct</td>
                <td>Already redirects to <code>admin_dashboard.php</code></td>
                <td>✓ OK</td>
            </tr>
        </table>
        
        <h2>Correct Admin File Names:</h2>
        <div class="info">
            <strong>Important:</strong> The correct filenames in /admin/ directory are:
            <ul>
                <li><code>admin_user.php</code> - Lists all users (NOT admin_users.php)</li>
                <li><code>admin_add_user.php</code> - Add new user</li>
                <li><code>admin_update_user.php</code> - Edit user</li>
                <li><code>admin_delete_user.php</code> - Delete user (FIXED)</li>
                <li><code>admin_dashboard.php</code> - Main dashboard</li>
                <li><code>admin_delete_assignment.php</code> - Delete assignment</li>
            </ul>
        </div>
        
        <h2>Testing the Fix:</h2>
        <ol>
            <li>Go to <a href="admin_user.php">User List</a></li>
            <li>Try deleting a user - should now show success/error message</li>
            <li>Should redirect back to user list without 404 error</li>
        </ol>
        
        <a href="admin_user.php" class="btn">← Go to User List</a>
        <a href="admin_dashboard.php" class="btn">← Go to Dashboard</a>
    </div>
</body>
</html>

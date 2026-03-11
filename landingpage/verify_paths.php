<?php
// Simple path verification page
include '../include.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Path Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        code { background: #f0f0f0; padding: 2px 5px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        table th { background: #4361ee; color: white; }
    </style>
</head>
<body>
    <h1>Path Configuration Verification</h1>
    <p>All paths have been centralized. Here's the verification:</p>
    
    <table>
        <tr>
            <th>Constant Name</th>
            <th>Value</th>
            <th>Exists?</th>
            <th>Type</th>
        </tr>
        
        <tr>
            <td><code>BASE_DIR</code></td>
            <td><?php echo BASE_DIR; ?></td>
            <td><?php echo file_exists(BASE_DIR) ? '✓ YES' : '✗ NO'; ?></td>
            <td>File System</td>
        </tr>
        
        <tr>
            <td><code>LANDINGPAGE_DIR</code></td>
            <td><?php echo LANDINGPAGE_DIR; ?></td>
            <td><?php echo file_exists(LANDINGPAGE_DIR) ? '✓ YES' : '✗ NO'; ?></td>
            <td>File System</td>
        </tr>
        
        <tr>
            <td><code>ADMIN_DIR</code></td>
            <td><?php echo ADMIN_DIR; ?></td>
            <td><?php echo file_exists(ADMIN_DIR) ? '✓ YES' : '✗ NO'; ?></td>
            <td>File System</td>
        </tr>
        
        <tr>
            <td><code>USERDASHBOARD_DIR</code></td>
            <td><?php echo USERDASHBOARD_DIR; ?></td>
            <td><?php echo file_exists(USERDASHBOARD_DIR) ? '✓ YES' : '✗ NO'; ?></td>
            <td>File System</td>
        </tr>
        
        <tr>
            <td><code>LOGINSIGNUP_DIR</code></td>
            <td><?php echo LOGINSIGNUP_DIR; ?></td>
            <td><?php echo file_exists(LOGINSIGNUP_DIR) ? '✓ YES' : '✗ NO'; ?></td>
            <td>File System</td>
        </tr>
        
        <tr>
            <td><code>ASSIGNMENTS_DIR</code></td>
            <td><?php echo ASSIGNMENTS_DIR; ?></td>
            <td><?php echo file_exists(ASSIGNMENTS_DIR) ? '✓ YES' : '✗ NO'; ?></td>
            <td>File System</td>
        </tr>
        
        <tr>
            <td><code>UPLOADS_DIR</code></td>
            <td><?php echo UPLOADS_DIR; ?></td>
            <td><?php echo file_exists(UPLOADS_DIR) ? '✓ YES' : '✗ NO'; ?></td>
            <td>File System</td>
        </tr>
        
        <tr style="background: #f0f0f0;">
            <td><code>WEB_ASSIGNMENTS</code></td>
            <td><?php echo WEB_ASSIGNMENTS; ?></td>
            <td>N/A</td>
            <td>Web Path</td>
        </tr>
        
        <tr style="background: #f0f0f0;">
            <td><code>WEB_UPLOADS</code></td>
            <td><?php echo WEB_UPLOADS; ?></td>
            <td>N/A</td>
            <td>Web Path</td>
        </tr>
        
        <tr style="background: #f0f0f0;">
            <td><code>WEB_ADMIN</code></td>
            <td><?php echo WEB_ADMIN; ?></td>
            <td>N/A</td>
            <td>Web Path</td>
        </tr>
        
        <tr style="background: #f0f0f0;">
            <td><code>WEB_DASHBOARD</code></td>
            <td><?php echo WEB_DASHBOARD; ?></td>
            <td>N/A</td>
            <td>Web Path</td>
        </tr>
        
        <tr style="background: #f0f0f0;">
            <td><code>WEB_LOGIN</code></td>
            <td><?php echo WEB_LOGIN; ?></td>
            <td>N/A</td>
            <td>Web Path</td>
        </tr>
    </table>
    
    <h2>Verification Summary</h2>
    <?php
    $all_exist = true;
    $dirs_to_check = [
        'BASE_DIR' => BASE_DIR,
        'LANDINGPAGE_DIR' => LANDINGPAGE_DIR,
        'ADMIN_DIR' => ADMIN_DIR,
        'USERDASHBOARD_DIR' => USERDASHBOARD_DIR,
        'LOGINSIGNUP_DIR' => LOGINSIGNUP_DIR,
        'ASSIGNMENTS_DIR' => ASSIGNMENTS_DIR,
        'UPLOADS_DIR' => UPLOADS_DIR,
    ];
    
    foreach ($dirs_to_check as $name => $path) {
        if (!file_exists($path)) {
            $all_exist = false;
        }
    }
    
    if ($all_exist) {
        echo '<div class="status success">✓ All file system paths exist and are accessible!</div>';
    } else {
        echo '<div class="status error">✗ Some paths are missing. Check the table above for details.</div>';
    }
    ?>
    
    <p><strong>Status:</strong> Path configuration is <strong><?php echo $all_exist ? 'WORKING' : 'NOT WORKING'; ?></strong></p>
    <p><a href="admin/admin_dashboard.php">→ Go to Admin Dashboard</a> | 
       <a href="Userdashboard/dashboard.php">→ Go to User Dashboard</a></p>
</body>
</html>

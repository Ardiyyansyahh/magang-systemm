<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Session Debug Info</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold mb-6">Session Debug Information</h1>
        
        <?php
        session_start();
        
        echo "<div class='bg-blue-50 p-4 rounded mb-4'>";
        echo "<h3 class='font-semibold mb-2'>Current Session Status:</h3>";
        echo "<p>Session ID: " . session_id() . "</p>";
        echo "<p>Session Status: " . (session_status() == PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "</p>";
        echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
        echo "</div>";
        
        echo "<div class='bg-green-50 p-4 rounded mb-4'>";
        echo "<h3 class='font-semibold mb-2'>Session Variables:</h3>";
        if (!empty($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                echo "<p><strong>$key:</strong> $value</p>";
            }
        } else {
            echo "<p class='text-red-600'>No session variables found!</p>";
        }
        echo "</div>";
        
        echo "<div class='bg-yellow-50 p-4 rounded mb-4'>";
        echo "<h3 class='font-semibold mb-2'>Session Configuration:</h3>";
        echo "<p>session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . " seconds</p>";
        echo "<p>session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . " seconds</p>";
        echo "<p>session.use_strict_mode: " . ini_get('session.use_strict_mode') . "</p>";
        echo "<p>session.cookie_httponly: " . ini_get('session.cookie_httponly') . "</p>";
        echo "</div>";
        
        echo "<div class='bg-purple-50 p-4 rounded mb-4'>";
        echo "<h3 class='font-semibold mb-2'>Cookies:</h3>";
        if (!empty($_COOKIE)) {
            foreach ($_COOKIE as $key => $value) {
                if (strpos($key, 'PHPSESSID') !== false) {
                    echo "<p><strong>$key:</strong> $value</p>";
                }
            }
        } else {
            echo "<p class='text-red-600'>No session cookies found!</p>";
        }
        echo "</div>";
        
        // Test session timeout
        if (isset($_SESSION['last_activity'])) {
            $time_diff = time() - $_SESSION['last_activity'];
            echo "<div class='bg-red-50 p-4 rounded mb-4'>";
            echo "<h3 class='font-semibold mb-2'>Session Timeout Check:</h3>";
            echo "<p>Last Activity: " . date('Y-m-d H:i:s', $_SESSION['last_activity']) . "</p>";
            echo "<p>Time Since Last Activity: $time_diff seconds</p>";
            echo "<p>Max Lifetime: " . ini_get('session.gc_maxlifetime') . " seconds</p>";
            if ($time_diff > ini_get('session.gc_maxlifetime')) {
                echo "<p class='text-red-600 font-semibold'>⚠️ SESSION EXPIRED!</p>";
            } else {
                echo "<p class='text-green-600 font-semibold'>✅ Session Active</p>";
            }
            echo "</div>";
        }
        ?>
        
        <div class="mt-6 flex gap-4">
            <a href="dashboard-dosen.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Back to Dashboard
            </a>
            <a href="../login.html" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Go to Login
            </a>
        </div>
    </div>
</body>
</html>

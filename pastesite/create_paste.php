<!DOCTYPE html>
<html lang="en-US">

<head>
    <link rel="icon" type="image/png" href="favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="mask-icon" href="favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="msapplication-config" content="favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <!-- HTML Meta Tags -->
   <title>SharePaste | Easiest Paste Tool</title>
    <meta name="description"
        content="SharePaste - Easily create and share text snippets with a custom URL. No sign-up required! Paste and share code, notes, and more. Discover the simplest way to share content securely and efficiently. Try SharePaste today!">

    <!-- Google / Search Engine Tags -->
    <meta itemprop="name" content="SharePaste | Easiest Paste Tool">
    <meta itemprop="description"
        content="SharePaste - Easily create and share text snippets with a custom URL. No sign-up required! Paste and share code, notes, and more. Discover the simplest way to share content securely and efficiently. Try SharePaste today!">
    <meta itemprop="image" content="http://sharepaste.xyz/site.webp">

    <!-- Facebook Meta Tags -->
    <meta property="og:url" content="https://sharepaste.xyz">
    <meta property="og:type" content="website">
    <meta property="og:title" content="SharePaste | Easiest Paste Tool">
    <meta property="og:description"
        content="SharePaste - Easily create and share text snippets with a custom URL. No sign-up required! Paste and share code, notes, and more. Discover the simplest way to share content securely and efficiently. Try SharePaste today!">
    <meta property="og:image" content="http://sharepaste.xyz/site.webp">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SharePaste | Easiest Paste Tool">
    <meta name="twitter:description"
        content="SharePaste - Easily create and share text snippets with a custom URL. No sign-up required! Paste and share code, notes, and more. Discover the simplest way to share content securely and efficiently. Try SharePaste today!">
    <meta name="twitter:image" content="http://sharepaste.xyz/site.webp">

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="header">
        <img src="img/logo.webp" alt="Logo">
    </div>
    <h1>#1 Easiest Paste Tool</h1>
    <div class="panel text-center">
        <?php
        // Database configuration
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "sharepaste";

        // Create a connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Function to generate a random custom URL
        function generateCustomURL()
        {
            // Implement your custom URL generation logic here
            // For simplicity, let's use a random 12-character string
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $custom_url = '';
            for ($i = 0; $i < 12; $i++) {
                $index = rand(0, strlen($characters) - 1);
                $custom_url .= $characters[$index];
            }
            return $custom_url;
        }

        // Get data from the form submission
        $content = $_POST['content'];
        $custom_url = $_POST['custom_url'];
        $highlighting = $_POST['highlightingLang'];

        // Check if the paste content is empty
        if (empty($content)) {
            die("Error: Paste content cannot be empty.");
        }

        // Check if the custom URL exceeds the character limit
        if (strlen($custom_url) > 12) {
            die("Error: Custom URL should not exceed 12 characters.");
        }

        // Check if the custom URL is already taken
        if (!empty($custom_url)) {
            $sql_check_url = "SELECT id FROM pastes WHERE custom_url = ?";
            $stmt_check_url = $conn->prepare($sql_check_url);
            $stmt_check_url->bind_param("s", $custom_url);
            $stmt_check_url->execute();

            $result_check_url = $stmt_check_url->get_result();
            if ($result_check_url->num_rows > 0) {
                die("Error: The custom URL is already taken. Please choose a different one.");
            }
        }

        // Generate custom URL if not provided
        if (empty($custom_url)) {
            $custom_url = generateCustomURL();
        }

        // Check if a file is uploaded and its size
        if (!empty($_FILES['file']['name'])) {
            $file_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_type = $_FILES['file']['type'];

            $max_file_size = 5 * 1024 * 1024; // 5MB

            if ($file_size > $max_file_size) {
                die("Error: File size should not exceed 5MB.");
            }

            // Move the uploaded file to the destination directory
            move_uploaded_file($file_tmp, "uploads/" . $file_name);

            // Insert the file URL into the database
            $content .= "\n\n[File: http://sharepaste.xyz/uploads/$file_name]";
        }

        // Save the data to the database using prepared statements
        $sql = "INSERT INTO pastes (content, custom_url, `highlighting`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $content, $custom_url, $highlighting);
        if ($stmt->execute()) {
            echo "<h2>Paste created successfully!</h2>
            <h3>http://sharepaste.xyz/view_paste.php?url=" . urlencode($custom_url) . "</h3>";
        } else {
            echo "<h2>Error:</h2><h3>" . $sql . "<br>" . $conn->error . "</h3>";
        }

        // Close the prepared statement and the connection
        $stmt->close();
        $conn->close();
        ?>
        <a class="btn btn-fl" href="/">Create new paste</a>
        <a class="terms" href="terms.php">Terms of Service</a>
    </div>
</body>

</html>

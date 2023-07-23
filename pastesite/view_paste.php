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
    <!-- Add CodeMirror CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.css">
</head>

<body>
    <div class="header">
        <img src="img/logo.webp" alt="Logo">
    </div>
    <h1>#1 Easiest Paste Tool</h1>
    <div class="panel">
        <?php
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

        // Get the custom URL from the query parameter
        $custom_url = $_GET['url'];

        // Retrieve the paste content and highlighting from the database using prepared statements
        $sql = "SELECT content, highlighting FROM pastes WHERE custom_url = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $custom_url);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $paste_content = $row['content']; // Retrieve the paste content
            $highlighting = $row['highlighting']; // Retrieve the highlighting

            if ($highlighting != null || !empty($highlighting)) {
                echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.js"></script>';

                if ($highlighting == 'text/x-csrc' || $highlighting == 'text/x-c++src' || $highlighting == 'text/x-java' || $highlighting == 'text/x-objectivec') echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/clike/clike.min.js"></script>';
                else echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/'.$highlighting.'/'.$highlighting.'.min.js"></script>';
            }

            // Display the paste content inside a textarea with CodeMirror
            echo '
            <h2 class="text-center">View Paste</h2>
            <textarea id="paste-content" readonly>' . htmlspecialchars($paste_content) . '</textarea>
            <div id="copy-button" class="btn btn-fl text-center" href="/">Copy</div>
            <button id="export-png" class="btn btn-fl">Export as PNG</button>
            <button id="export-raw" class="btn btn-fl">Export as Raw Text</button>
            ';

        } else {
            echo '<h2>Paste not found.</h2>';
        }

        // Close the prepared statement and the connection
        $stmt->close();
        $conn->close();
        ?>
        <a class="terms" href="terms.php">Terms of Service</a>
    </div>

    <!-- Add Clipboard.js library script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

    <?php if ($highlighting != null || !empty($highlighting)) { ?>
    <script>
        // Initialize CodeMirror on the textarea
        document.addEventListener('DOMContentLoaded', function () {
            var textarea = document.getElementById('paste-content');
            
            var editor = CodeMirror.fromTextArea(textarea, {
                lineNumbers: true, // Optional: Show line numbers
                mode: '<?php echo $highlighting; ?>', // Replace 'javascript' with appropriate language for highlighting
                readOnly: true
            });
        });
    </script>
    <?php } ?>
    
    <script>
        // JavaScript code for Clipboard.js
        document.addEventListener('DOMContentLoaded', function () {
            var copyButton = document.getElementById('copy-button');

            var clipboard = new ClipboardJS(copyButton, {
                text: function () {
                    var pasteContent = document.getElementById('paste-content');
                    return pasteContent.value;
                }
            });

            clipboard.on('success', function (e) {
                alert('Copied to clipboard!');
                e.clearSelection();
            });

            clipboard.on('error', function (e) {
                alert('Unable to copy. Please select and copy manually.');
            });

            // JavaScript code for exporting as PNG
            document.getElementById('export-png').addEventListener('click', function () {
                const pasteContent = document.getElementById('paste-content');
                domtoimage.toBlob(pasteContent)
                    .then(function (blob) {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'paste.png';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    });
            });


            // JavaScript code for exporting as raw text
            document.getElementById('export-raw').addEventListener('click', function () {
                const pasteContent = document.getElementById('paste-content').value;
                const blob = new Blob([pasteContent], {
                    type: 'text/plain'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'paste.txt';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });

        });
    </script>
</body>

</html>

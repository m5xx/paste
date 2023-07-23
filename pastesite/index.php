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

    <!-- Highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.min.js"></script>
</head>
<body>
    <div class="header">
        <img src="img/logo.webp" alt="Logo">
    </div>
    <h1>#1 Easiest Paste Tool</h1>

    <div class="panel">
        <form action="create_paste.php" method="post">
            <label for="content">Paste your text:</label>
            <textarea id="content" name="content" rows="10" cols="50"></textarea>

            <div style="margin-top:2rem;">
                <label for="highlightingLang">Highlighting</label>
                <select name="highlightingLang" id="highlightingLang">
                    <option value="">None</option>
                    <option value="text/x-csrc">C</option>
                    <option value="text/x-c++src">C++</option>
                    <option value="text/x-java">Java</option>
                    <option value="text/x-objectivec">Objective-C</option>
                    <option value="javascript">JavaScript</option>
                    <option value="python">Python</option>
                    <option value="css">CSS</option>
                    <option value="xml">HTML/XML</option>
                    <option value="sql">SQL</option>
                    <option value="ruby">Ruby</option>
                    <option value="swift">Swift</option>
                    <option value="go">Go</option>
                    <option value="dart">Dart</option>
                    <option value="lua">Lua</option>
                    <option value="perl">Perl</option>
                    <option value="r">R</option>
                    <option value="shell">Bash/Shell</option>
                    <option value="octave">MATLAB</option>
                    <option value="yaml">JSON/YAML</option>
                </select>
            </div>

            <div style="margin-top:2rem;">
                <label for="custom_url">Custom URL (optional):</label>
                <input type="text" id="custom_url" name="custom_url">
            </div>

            <h3 class="m-0">Total Pastes: <?php echo getTotalPastesCount(); ?></h3> <!-- Display the paste count here -->
            <input type="submit" value="Create Paste">
        </form>
        <a class="terms" href="terms.php">Terms of Service |</a>
        <a class="dono" href="https://www.buymeacoffee.com/sharepaste">BuyMeACoffee |</a>
        <a class="about" href="about.php">About</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var textarea = document.getElementById('content');
            var editor = CodeMirror.fromTextArea(textarea, {
                mode: 'null', // Default mode, you can change it to any other mode you prefer.
                lineNumbers: true,
                theme: 'default',
            });

            var select = document.getElementById('highlightingLang');
            select.addEventListener('change', function () {
                var selectedMode = select.value;
                if (selectedMode) {
                    // Load the mode dynamically if not already loaded
                    if (!CodeMirror.modes.hasOwnProperty(selectedMode)) {
                        var script = document.createElement('script');

                        switch (selectedMode) {
                            case 'text/x-csrc':
                            case 'text/x-c++src':
                            case 'text/x-java':
                            case 'text/x-objectivec':
                                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/clike/clike.min.js';
                                break;
                            default:
                                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/' + selectedMode + '/' + selectedMode + '.min.js';
                                break;
                        }

                        
                        script.onload = function () {
                            editor.setOption('mode', selectedMode);
                        };
                        document.head.appendChild(script);
                    } else {
                        editor.setOption('mode', selectedMode);
                    }
                } else {
                    // If "None" or no option is selected, set to plaintext mode.
                    editor.setOption('mode', 'null');
                }
            });
        });
    </script>

</body>

</html>

<?php
// Function to get the total number of pastes from the database
function getTotalPastesCount() {
    // Replace the database credentials with your actual credentials
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

    // Retrieve the paste count from the database using prepared statements
    $sql = "SELECT COUNT(*) AS total_pastes FROM pastes";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_pastes = $row['total_pastes'];

    // Close the connection
    $stmt->close();
    $conn->close();

    return $total_pastes;
}
?>

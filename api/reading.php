<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}

// Include configuration
include '../config/config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bible Reading - The Good Book Log</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Handlee&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include the sidebar -->
        <?php include '../templates/sidebar.html'; ?>
        
        <div class="main-content">
            <div class="bible-container">
                <div class="books-list">
                    <h2>Books of the Bible</h2>
                    <div class="books-wrapper">
                        <?php
                        // Books and chapters array
                        $books = [
                            'Genesis' => 50, 'Exodus' => 40, 'Leviticus' => 27, 'Numbers' => 36, 'Deuteronomy' => 34,
                            'Joshua' => 24, 'Judges' => 21, 'Ruth' => 4, '1 Samuel' => 31, '2 Samuel' => 24,
                            '1 Kings' => 22, '2 Kings' => 25, '1 Chronicles' => 29, '2 Chronicles' => 36, 'Ezra' => 10,
                            'Nehemiah' => 13, 'Esther' => 10, 'Job' => 42, 'Psalms' => 150, 'Proverbs' => 31,
                            'Ecclesiastes' => 12, 'Song of Solomon' => 8, 'Isaiah' => 66, 'Jeremiah' => 52, 'Lamentations' => 5,
                            'Ezekiel' => 48, 'Daniel' => 12, 'Hosea' => 14, 'Joel' => 3, 'Amos' => 9,
                            'Obadiah' => 1, 'Jonah' => 4, 'Micah' => 7, 'Nahum' => 3, 'Habakkuk' => 3,
                            'Zephaniah' => 3, 'Haggai' => 2, 'Zechariah' => 14, 'Malachi' => 4,
                            'Matthew' => 28, 'Mark' => 16, 'Luke' => 24, 'John' => 21, 'Acts' => 28,
                            'Romans' => 16, '1 Corinthians' => 16, '2 Corinthians' => 13, 'Galatians' => 6, 'Ephesians' => 6,
                            'Philippians' => 4, 'Colossians' => 4, '1 Thessalonians' => 5, '2 Thessalonians' => 3, '1 Timothy' => 6,
                            '2 Timothy' => 4, 'Titus' => 3, 'Philemon' => 1, 'Hebrews' => 13, 'James' => 5,
                            '1 Peter' => 5, '2 Peter' => 3, '1 John' => 5, '2 John' => 1, '3 John' => 1,
                            'Jude' => 1, 'Revelation' => 22
                        ];

                        // Display books and chapters
                        foreach ($books as $book => $chapters) {
                            echo "<div class='book-item'>";
                            echo "<div class='book-header'>";
                            echo "<span class='book-name'>$book</span>";
                            echo "<button class='toggle-btn' aria-label='Toggle chapters'>";
                            echo "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>";
                            echo "<polyline points='6 9 12 15 18 9'></polyline>";
                            echo "</svg>";
                            echo "</button>";
                            echo "</div>";
                            echo "<div class='chapter-list'>";
                            for ($i = 1; $i <= $chapters; $i++) {
                                echo "<button class='chapter-btn' data-book='$book' data-chapter='$i'>Chapter $i</button>";
                            }
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
                <div class="chapter-content">
                    <div class="welcome-message">
                        <h1>Welcome to Bible Reading</h1>
                        <p>Select a book and chapter to start reading.</p>
                    </div>
                    <div id="chapter-text"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="../public/assets/js/script.js"></script>
</body>
</html>
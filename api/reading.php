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
    <style>
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex-grow: 1;
            padding: 2rem;
            margin-left: 250px; /* Adjust this value to match your sidebar width */
        }
        .bible-container {
            display: flex;
            gap: 2rem;
        }
        .books-list {
            flex: 0 0 300px;
            overflow-y: auto;
            max-height: calc(100vh - 4rem);
        }
        .chapter-content {
            flex: 1;
        }
        .book-item {
            margin-bottom: 1rem;
        }
        .book-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--color-cream-200);
            padding: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .book-name {
            font-weight: 500;
        }
        .toggle-btn {
            background: none;
            border: none;
            cursor: pointer;
        }
        .chapter-list {
            display: none;
            padding: 0.5rem;
            background-color: var(--color-cream-100);
            border-radius: 0 0 4px 4px;
        }
        .chapter-btn {
            display: inline-block;
            margin: 0.25rem;
            padding: 0.25rem 0.5rem;
            background-color: var(--color-cream-300);
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .chapter-btn:hover {
            background-color: var(--color-bronze);
            color: var(--color-cream-100);
        }
        .welcome-message {
            text-align: center;
            margin-top: 2rem;
        }
        .welcome-message h1 {
            font-family: var(--font-accent);
            color: var(--color-brown-900);
        }
        #chapter-text {
            line-height: 1.6;
            font-size: 1.1rem;
        }
    </style>
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
    <script src="../public/assets/js/darkMode.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const bookItems = document.querySelectorAll('.book-item');
        const chapterBtns = document.querySelectorAll('.chapter-btn');
        const chapterText = document.getElementById('chapter-text');

        bookItems.forEach(item => {
            const header = item.querySelector('.book-header');
            const chapterList = item.querySelector('.chapter-list');
            header.addEventListener('click', () => {
                chapterList.style.display = chapterList.style.display === 'block' ? 'none' : 'block';
            });
        });

        chapterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const book = this.getAttribute('data-book').toLowerCase();  // Ensure the book name is lowercase
                const chapter = this.getAttribute('data-chapter');
                fetchChapter(book, chapter);
            });
        });

        // Function to fetch chapter data from the Bible API
        function fetchChapter(book, chapter) {
            const url = `https://bible-api.com/${book}%20${chapter}?translation=kjv`;

    // Display loading message before the fetch
    chapterText.innerHTML = `<p>Loading chapter...</p>`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json(); // Parse as JSON
        })
        .then(data => {
            if (data.verses) {
                // Display chapter title and verses
                chapterText.innerHTML = `<h2>${capitalize(book)} Chapter ${chapter}</h2>`;
                let chapterContent = `<ul>`;
                data.verses.forEach(verse => {
                    chapterContent += `<li><strong>Verse ${verse.verse}:</strong> ${verse.text}</li>`;
                });
                chapterContent += `</ul>`;
                chapterText.innerHTML += chapterContent;
            } else {
                chapterText.innerHTML = `<p>Sorry, the chapter content could not be found.</p>`;
            }
        })
        .catch(error => {
            // If there's an error, display the error message instead
            chapterText.innerHTML = `<p>Sorry, there was an error fetching the chapter. Please try again later.</p>`;
            console.error('Error fetching chapter:', error);
        });
        }

        // Utility function to capitalize book names
        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    });
    </script>
</body>
</html>


document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;

    // Check for saved dark mode preference
    const isDarkMode = localStorage.getItem('darkMode') === 'true';

    // Set initial dark mode state
    if (isDarkMode) {
        body.classList.add('dark-mode');
    }

    darkModeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', body.classList.contains('dark-mode'));
    });

    // Bible reading functionality
    const bookHeaders = document.querySelectorAll('.book-header');
    const chapterContent = document.getElementById('chapter-text');

    // Handle book expansion
    bookHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const bookItem = header.closest('.book-item');
            const chapterList = bookItem.querySelector('.chapter-list');
            const toggleBtn = header.querySelector('.toggle-btn');

            // Close other open chapter lists
            document.querySelectorAll('.chapter-list.active').forEach(list => {
                if (list !== chapterList) {
                    list.classList.remove('active');
                    list.closest('.book-item').querySelector('.toggle-btn').classList.remove('active');
                }
            });

            // Toggle current chapter list visibility
            chapterList.classList.toggle('active');
            toggleBtn.classList.toggle('active');
        });
    });

    // Handle chapter selection
    document.querySelectorAll('.chapter-btn').forEach(button => {
        button.addEventListener('click', () => {
            const book = button.getAttribute('data-book');
            const chapter = button.getAttribute('data-chapter');

            // Show loading state
            chapterContent.innerHTML = `
                <div class="welcome-message">
                    <h1>${book} - Chapter ${chapter}</h1>
                    <p>Loading chapter content...</p>
                </div>
            `;

            // Here, you would add the logic to load chapter content dynamically
        });
    });

});

document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;

    darkModeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
    });

    const verses = [
        { text: "For God so loved the world that he gave his one and only Son, that whoever believes in him shall not perish but have eternal life.", source: "John 3:16" },
        { text: "The Lord is my shepherd, I lack nothing.", source: "Psalm 23:1" },
        { text: "I can do all this through him who gives me strength.", source: "Philippians 4:13" },
    ];

    const verseElement = document.getElementById('verse');
    const verseSourceElement = document.getElementById('verse-source');

    let currentVerseIndex = 0;

    function changeVerse() {
        const { text, source } = verses[currentVerseIndex];
        verseElement.textContent = text;
        verseSourceElement.textContent = source;
        currentVerseIndex = (currentVerseIndex + 1) % verses.length;
    }

    setInterval(changeVerse, 5000);

    const searchForm = document.getElementById('searchForm');
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const searchTerm = e.target.querySelector('input').value;
        console.log('Searching for:', searchTerm);
        // Implement search functionality here
    });

    const newsletterForm = document.getElementById('newsletterForm');
    newsletterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = e.target.querySelector('input').value;
        console.log('Signing up with email:', email);
        // Implement newsletter signup functionality here
    });
});
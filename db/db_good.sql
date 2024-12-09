-- Create the Users table (renamed to users_gbl)
CREATE TABLE users_gbl (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the Reading Logs table (renamed to reading_logs_gbl)
CREATE TABLE reading_logs_gbl (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book VARCHAR(50) NOT NULL,
    chapter VARCHAR(20) NOT NULL,
    verses VARCHAR(50),
    date DATE NOT NULL,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users_gbl(user_id) ON DELETE CASCADE
);

-- Create the Goals table (renamed to goals_gbl)
CREATE TABLE goals_gbl (
    goal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    goal_name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('In Progress', 'Completed', 'Missed') DEFAULT 'In Progress',
    FOREIGN KEY (user_id) REFERENCES users_gbl(user_id) ON DELETE CASCADE
);

-- Create the Habit Tracking table (renamed to habit_tracking_gbl)
CREATE TABLE habit_tracking_gbl (
    habit_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    streak INT DEFAULT 0,
    longest_streak INT DEFAULT 0,
    last_read_date DATE,
    FOREIGN KEY (user_id) REFERENCES users_gbl(user_id) ON DELETE CASCADE
);

-- Create the Analytics table (renamed to analytics_gbl)
CREATE TABLE analytics_gbl (
    analytics_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    books_read INT DEFAULT 0,
    chapters_read INT DEFAULT 0,
    verses_read INT DEFAULT 0,
    average_reading_time FLOAT DEFAULT 0.0,
    FOREIGN KEY (user_id) REFERENCES users_gbl(user_id) ON DELETE CASCADE
);

-- Create the Verse of the Day table (renamed to verse_of_the_day_gbl)
CREATE TABLE verse_of_the_day_gbl (
    verse_id INT AUTO_INCREMENT PRIMARY KEY,
    book VARCHAR(50) NOT NULL,
    chapter INT NOT NULL,
    verse INT NOT NULL,
    text TEXT NOT NULL
);

-- Example data for Verse of the Day
INSERT INTO verse_of_the_day_gbl (book, chapter, verse, text) 
VALUES ('John', 3, 16, 'For God so loved the world that He gave His one and only Son, that whoever believes in Him shall not perish but have eternal life.');

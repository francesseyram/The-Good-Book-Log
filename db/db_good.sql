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

/*
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
*/

-- Create the Verse of the Day table (renamed to verse_of_the_day_gbl)
CREATE TABLE verse_of_the_day_gbl (
    verse_id INT AUTO_INCREMENT PRIMARY KEY,
    book VARCHAR(50) NOT NULL,
    chapter INT NOT NULL,
    verse INT NOT NULL,
    text TEXT NOT NULL
);

-- Create the User Reading Plans table (renamed to user_reading_plans_gbl)
CREATE TABLE user_reading_plans_gbl (
    user_plan_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE NOT NULL,
    progress FLOAT DEFAULT 0.0,
    status ENUM('Active', 'Paused', 'Completed') DEFAULT 'Active',
    FOREIGN KEY (user_id) REFERENCES users_gbl(user_id) ON DELETE CASCADE
);

-- Create the Reading Plans table
CREATE TABLE reading_plans_gbl (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL, -- Number of days
    category VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progress FLOAT DEFAULT 0.0
);

-- Create the Plan Chapters table
CREATE TABLE plan_chapters_gbl (
    chapter_id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    day_number INT NOT NULL, -- Day in the plan
    book VARCHAR(50) NOT NULL,
    chapter INT NOT NULL,
    start_verse INT DEFAULT NULL,
    end_verse INT DEFAULT NULL,
    FOREIGN KEY (plan_id) REFERENCES reading_plans_gbl(plan_id) ON DELETE CASCADE
);

-- Create the Read Chapters table
CREATE TABLE read_chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    chapter_id INT NOT NULL, -- Foreign key to plan_chapters_gbl
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users_gbl(user_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES reading_plans_gbl(plan_id) ON DELETE CASCADE,
    FOREIGN KEY (chapter_id) REFERENCES plan_chapters_gbl(chapter_id) ON DELETE CASCADE
);


INSERT INTO verse_of_the_day_gbl (book, chapter, verse, text) VALUES
('Jeremiah', 29, 11, '“For I know the plans I have for you,” declares the LORD, “plans to prosper you and not to harm you, plans to give you a hope and a future.”'),
('Isaiah', 40, 31, 'But those who hope in the LORD will renew their strength. They will soar on wings like eagles; they will run and not grow weary, they will walk and not be faint.'),
('Philippians', 4, 13, 'I can do all this through him who gives me strength.'),
('Romans', 8, 28, 'And we know that in all things God works for the good of those who love him, who have been called according to his purpose.'),
('Psalm', 23, 1, 'The Lord is my shepherd, I lack nothing.'),
('Matthew', 19, 26, 'Jesus looked at them and said, “With man this is impossible, but with God all things are possible.”'),
('Isaiah', 41, 10, 'So do not fear, for I am with you; do not be dismayed, for I am your God. I will strengthen you and help you; I will uphold you with my righteous right hand.'),
('2 Corinthians', 5, 7, 'For we live by faith, not by sight.'),
('Psalm', 46, 1, 'God is our refuge and strength, an ever-present help in trouble.'),
('Romans', 12, 2, 'Do not conform to the pattern of this world, but be transformed by the renewing of your mind. Then you will be able to test and approve what God’s will is—his good, pleasing and perfect will.'),
('Joshua', 1, 9, 'Have I not commanded you? Be strong and courageous. Do not be afraid; do not be discouraged, for the Lord your God will be with you wherever you go.'),
('John', 3, 16, 'For God so loved the world that he gave his one and only Son, that whoever believes in him shall not perish but have eternal life.'),
('Psalm', 121, 1, 'I lift up my eyes to the mountains—where does my help come from? My help comes from the LORD, the Maker of heaven and earth.'),
('Ephesians', 3, 20, 'Now to him who is able to do immeasurably more than all we ask or imagine, according to his power that is at work within us.'),
('Proverbs', 3, 5, 'Trust in the LORD with all your heart and lean not on your own understanding.');

INSERT INTO reading_plans_gbl (title, duration, category, description) 
VALUES 
('The Birth of Christ', 7, 'Christmas', 'A 7-day plan focusing on the prophecy, birth, and significance of Jesus.'),
('Advent Reflection', 25, 'Christmas', 'A 25-day Advent plan to prepare for Christmas.'),
('The Nativity Story', 10, 'Christmas', 'Explore the nativity story in detail over 10 days.');

INSERT INTO plan_chapters_gbl (plan_id, day_number, book, chapter, start_verse, end_verse) 
VALUES 
(1, 1, 'Isaiah', 9, 6, 7),  -- Prophecy of Jesus
(1, 2, 'Micah', 5, 2, NULL), -- Bethlehem Prophecy
(1, 3, 'Luke', 1, 26, 38), -- Announcement to Mary
(1, 4, 'Luke', 2, 1, 7),  -- Birth of Jesus
(1, 5, 'Luke', 2, 8, 20), -- Visit of the Shepherds
(1, 6, 'Matthew', 2, 1, 12), -- Visit of the Magi
(1, 7, 'John', 1, 1, 14); -- Jesus as the Word made Flesh



INSERT INTO plan_chapters_gbl (plan_id, day_number, book, chapter, start_verse, end_verse) 
VALUES 
(3, 1, 'Luke', 1, 5, 25),    -- Zechariah and Elizabeth
(3, 2, 'Luke', 1, 26, 38),   -- Announcement to Mary
(3, 3, 'Luke', 1, 39, 56),   -- Mary visits Elizabeth
(3, 4, 'Luke', 1, 57, 80),   -- Birth of John the Baptist
(3, 5, 'Luke', 2, 1, 7),     -- Birth of Jesus
(3, 6, 'Luke', 2, 8, 20),    -- Visit of the Shepherds
(3, 7, 'Matthew', 1, 18, 25),-- Joseph's dream
(3, 8, 'Matthew', 2, 1, 12), -- Visit of the Magi
(3, 9, 'Matthew', 2, 13, 23),-- Escape to Egypt and return
(3, 10, 'John', 1, 1, 14);   -- Jesus as the Word made Flesh

INSERT INTO plan_chapters_gbl (plan_id, day_number, book, chapter, start_verse, end_verse) 
VALUES 
(2, 1, 'Isaiah', 7, 14, NULL),    -- Prophecy of Immanuel
(2, 2, 'Isaiah', 9, 6, 7),       -- A Child is Born
(2, 3, 'Micah', 5, 2, NULL),     -- Bethlehem Prophecy
(2, 4, 'Jeremiah', 33, 14, 16),  -- Righteous Branch
(2, 5, 'Luke', 1, 5, 25),        -- Zechariah and Elizabeth
(2, 6, 'Luke', 1, 26, 38),       -- Announcement to Mary
(2, 7, 'Luke', 1, 39, 56),       -- Mary Visits Elizabeth
(2, 8, 'Luke', 1, 57, 80),       -- Birth of John the Baptist
(2, 9, 'Matthew', 1, 18, 25),    -- Joseph's Dream
(2, 10, 'Luke', 2, 1, 7),        -- Birth of Jesus
(2, 11, 'Luke', 2, 8, 14),       -- Angel announces the Good News
(2, 12, 'Luke', 2, 15, 20),      -- Shepherds Visit Jesus
(2, 13, 'Matthew', 2, 1, 12),    -- Visit of the Magi
(2, 14, 'Matthew', 2, 13, 18),   -- Escape to Egypt
(2, 15, 'Matthew', 2, 19, 23),   -- Return to Nazareth
(2, 16, 'John', 1, 1, 14),       -- Jesus as the Word
(2, 17, 'Isaiah', 53, 1, 12),    -- Suffering Servant
(2, 18, 'Psalm', 98, 1, 9),      -- Joy to the World
(2, 19, 'Zephaniah', 3, 14, 17), -- Rejoice and Be Glad
(2, 20, 'Philippians', 2, 1, 11),-- Christ's Humility
(2, 21, 'Galatians', 4, 4, 7),   -- God sent His Son
(2, 22, 'Revelation', 1, 5, 8),  -- Alpha and Omega
(2, 23, 'Isaiah', 11, 1, 10),    -- Root of Jesse
(2, 24, 'Luke', 2, 21, 35),      -- Presentation in the Temple
(2, 25, 'Luke', 2, 36, 40);      -- Anna and Simeon Rejoice


-- A. ERD and Database Specification

CREATE TABLE Users (
    user_id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255),
    dob VARCHAR(255),
    avatar_url VARCHAR(255),
    PRIMARY KEY (user_id)
);

CREATE TABLE Recipes (
    recipe_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (recipe_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Notes (
    note_id INT NOT NULL AUTO_INCREMENT,
    recipe_id INT NOT NULL,
    user_id INT NOT NULL,
    note VARCHAR(1300) NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (note_id),
    FOREIGN KEY (recipe_id) REFERENCES Recipes(recipe_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Access (
    access_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (access_id),
    FOREIGN KEY (recipe_id) REFERENCES Recipes(recipe_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    UNIQUE KEY unique_access (recipe_id, user_id)
);

-- B. Data Storage Queries

-- 1- Sign-Up Page: Insert a new user (avatar assumed uploaded)
INSERT INTO Users (
    first_name,
    last_name,
    email,
    password,
    dob,
    avatar_url
) VALUES (
    'micha',
    'carene',
    'mc@gmail.com',
    '0987abc',
    '25-4-1997',
    'images/mc.jpg'
);

-- 2- Create Recipe Page: Insert a new recipe
INSERT INTO Recipes (
    user_id,
    title,
    ingredients,
    instructions,
    created_at
) VALUES (
    1,
    'Beef Stew',
    'beef cubes, beef broth, bay leaves, rosemary, carrots, onions, all purpose flour, olive oil, paprika',
    '1. marinate your beef cubes with your spices of choice including paprika and flour 2. brown your beef and then remove and saute your vegetables  3. pour one cup of broth into the pot and place beef cubes back in and let it cook with some rosemary and bay leaves for 1 hour 30 minutes 4. serve with chapati or cheesy garlic bread and enjoy',
    CURRENT_TIMESTAMP
);

-- Insert access for creator of recipe (status=1)
INSERT INTO Access (
    user_id,
    recipe_id,
    status
) VALUES (
    1,
    1,
    1
);

-- 3- View Recipe Page: Insert a new cooking note
INSERT INTO Notes (
    recipe_id,
    user_id,
    note,
    timestamp
) VALUES (
    1,
    1,
    'For spices I recommend using garlic powder!',
    CURRENT_TIMESTAMP
);

-- 4- Share/Manage Access Page

-- Grant access for user 2 to recipe 1
INSERT INTO Access (
    user_id,
    recipe_id,
    status
) VALUES (
    2,
    1,
    1
);

-- Revoke access for user 2 from recipe 1
UPDATE Access
SET status = 0
WHERE user_id = 2 AND recipe_id = 1;

-- C. Data Retrieval Queries

-- 1- Login Form: Get user info matching email and password
SELECT
    user_id,
    CONCAT(first_name, ' ', last_name) AS screen_name,
    avatar_url
FROM Users
WHERE email = 'mc@gmail.com' AND password = '0987abc';

-- Recipe List Page: For user 1, get recipes with access=1 ordered by newest
SELECT
    r.title,
    r.created_at,
    MAX(n.timestamp) AS date_of_last_note,
    COUNT(n.note_id) AS total_note_count
FROM Recipes r
JOIN Access a ON r.recipe_id = a.recipe_id
LEFT JOIN Notes n ON r.recipe_id = n.recipe_id
WHERE a.user_id = 1 AND a.status = 1
GROUP BY r.recipe_id, r.title, r.created_at
ORDER BY r.created_at DESC;

-- View Recipe Page: Get recipe info for recipe_id=1
SELECT
    r.title,
    r.created_at,
    MAX(n.timestamp) AS date_of_last_note
FROM Recipes r
LEFT JOIN Notes n ON r.recipe_id = n.recipe_id
WHERE r.recipe_id = 1
GROUP BY r.recipe_id, r.title, r.created_at;

-- Get all notes for recipe_id=1 ordered oldest first
SELECT
    n.note,
    n.timestamp,
    CONCAT(u.first_name, ' ', u.last_name) AS screen_name,
    u.avatar_url
FROM Notes n
JOIN Users u ON n.user_id = u.user_id
WHERE n.recipe_id = 1
ORDER BY n.timestamp ASC;

-- Share/Manage Access Page: Return all users and their access status for recipe 1
SELECT
    u.user_id,
    CONCAT(u.first_name, ' ', u.last_name) AS screen_name,
    u.avatar_url,
    IFNULL(a.status, 0) AS access_status
FROM Users u
LEFT JOIN Access a ON u.user_id = a.user_id AND a.recipe_id = 1
ORDER BY u.first_name, u.last_name;

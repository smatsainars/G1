-- Database schema for the Issue Tracker.

CREATE TABLE projects (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)  NOT NULL,
    description TEXT          NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE issues (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    project_id  INT UNSIGNED  NOT NULL,
    title       VARCHAR(150)  NOT NULL,
    description TEXT          NULL,
    status      ENUM('open', 'in_progress', 'done') NOT NULL DEFAULT 'open',
    priority    ENUM('low', 'medium', 'high')       NOT NULL DEFAULT 'medium',
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_issues_project
        FOREIGN KEY (project_id) REFERENCES projects (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data
INSERT INTO projects (name, description) VALUES
    ('Phone repair shop', 'Small site for a phone repair'),
    ('Car rental app', 'App where people can book a car for a few days.'),
    ('Home stuff', NULL);

INSERT INTO issues (project_id, title, description, status, priority) VALUES
    (1, 'Contact form not working', 'Sent 2 test messages, nothing arrived. Check the mail settings?', 'open', 'high'),
    (1, 'Wrong price for iPhone screen repair', 'Site says 89 EUR, shop says 120 EUR.', 'in_progress', 'medium'),
    (1, 'Add shop opening hours to footer', NULL, 'done', 'low'),
    (2, 'App crashes when choosing return date', 'Only happens on older Android phones.', 'open', 'high'),
    (2, 'Car photos load very slow', 'Images are like 5 MB each, need to make them smaller.', 'open', 'medium'),
    (3, 'Fix kitchen tap', 'Drips all night.', 'open', 'low'),
    (3, 'Change winter tyres', 'Before November.', 'open', 'medium');
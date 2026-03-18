INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES
('Admin', 'admin@cinehall.com', '$2y$10$123456789012345678901uQ0YpH1', 'admin', NOW(), NOW()),
('Khaoula', 'khaoula@gmail.com', '$2y$10$123456789012345678901uQ0YpH1', 'user', NOW(), NOW());

INSERT INTO genres (id, name) VALUES
(1, 'Action'),
(2, 'Comedy'),
(3, 'Drama');

INSERT INTO films (genre_id, title, description, duration, min_age, created_at, updated_at) VALUES
(1, 'Fast & Furious', 'Action movie', 120, 12, NOW(), NOW()),
(2, 'Mr Bean', 'Funny comedy movie', 90, 0, NOW(), NOW()),
(3, 'Titanic', 'Romantic drama', 180, 10, NOW(), NOW());

INSERT INTO rooms (name, type, capacity, total_seats, created_at, updated_at) VALUES
('Room 1', 'normale', 50, 50, NOW(), NOW()),
('Room 2', 'vip', 20, 20, NOW(), NOW());

INSERT INTO seances (film_id, room_id, start_time, language, type, created_at, updated_at) VALUES
(1, 1, '2026-03-20 18:00:00', 'FR', 'normale', NOW(), NOW()),
(2, 2, '2026-03-21 20:00:00', 'EN', 'vip', NOW(), NOW());

INSERT INTO reservations (user_id, seance_id, status, created_at, updated_at) VALUES
(2, 1, 'pending', NOW(), NOW()),
(2, 2, 'paid', NOW(), NOW());

INSERT INTO reservation_seat (reservation_id, seat_number, seat_type) VALUES
(1, 'A1', 'single'),
(1, 'A2', 'single'),
(2, 'B1', 'couple');

INSERT INTO tickets (reservation_id, user_id, qr_code, created_at, updated_at) VALUES
(2, 2, 'QR123456789', NOW(), NOW());
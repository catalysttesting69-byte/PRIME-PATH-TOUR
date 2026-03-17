-- Migration to create separate tables for packages and excursions
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    price DECIMAL(10, 2),
    duration_days INT,
    highlights TEXT, -- JSON or comma-separated
    route TEXT,      -- JSON or comma-separated
    type VARCHAR(50) DEFAULT 'safari', -- safari, zanzibar, combined
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS excursions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(100),
    price VARCHAR(50), -- formatted price like '$35'
    description TEXT,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert initial data for packages
INSERT INTO packages (title, image_url, price, duration_days, highlights, route, type) VALUES
('6 Days 5 Nights Zanzibar Discovery Tour Package', 'https://images.unsplash.com/photo-1590523741831-ab7e8b8f9c7f?w=640&h=420&fit=crop', 1250.00, 6, 'Stone Town & Spice Farms,Mnemba Snorkeling & Turtles', '5 nights Zanzibar', 'zanzibar'),
('7 Days 6 Nights Zanzibar Discovery Experience', 'https://images.unsplash.com/photo-1544551763-8dd44758c2dd?w=640&h=420&fit=crop', 1450.00, 7, 'Nakupenda & Prison Island,Safari Blue Experience', '6 nights Zanzibar', 'zanzibar'),
('8-Day Tanzania Great Migration Safari', 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=640&h=420&fit=crop', 3950.00, 8, 'Ngorongoro Crater,Great Wildebeest Migration Spectacle', 'Arusha,Tarangire,3 days Serengeti,Ngorongoro', 'safari'),
('10-Day Tanzania Big Five & Cultural Safari', 'https://images.unsplash.com/photo-1549366021-9f761d450615?w=640&h=420&fit=crop', 4500.00, 10, 'Mto wa Mbu Village,Tarangire & Serengeti', 'Arusha,Lake Manyara,3 days Serengeti,Ngorongoro,2 days Tarangire', 'safari'),
('12 Days Tanzania Safari & Zanzibar Beach Holiday Escape', 'https://images.unsplash.com/photo-1540541338287-41700207dee6?w=640&h=420&fit=crop', 5250.00, 12, 'Romantic Honeymoon,Serengeti & Zanzibar', 'Arusha,Tarangire,Lake Manyara,3 days Serengeti,Ngorongoro,5 days Zanzibar', 'combined');

-- Insert initial data for excursions
INSERT INTO excursions (name, image_url, category, price, description) VALUES
('Stone Town Tour', 'https://images.unsplash.com/photo-1621245089855-87bd754f9d68?w=600&h=400&fit=crop', 'City Tour', '$35', 'Explore the winding alleys, historical sites, and vibrant markets of Zanzibars most historic city.'),
('Prison Island', 'https://images.unsplash.com/photo-1550064434-6c3e6dc27cc5?w=600&h=400&fit=crop', 'Island Trip', '$45', 'Take a boat ride to see the giant Aldabra tortoises and relax on pristine white sand beaches.'),
('Jozani Forest', 'https://images.unsplash.com/photo-1540569876033-6e43130d2238?w=600&h=400&fit=crop', 'Nature', '$40', 'Walk through lush landscapes and spot the rare red colobus monkeys in their natural habitat.'),
('Masingini Forest', 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=600&h=400&fit=crop', 'Nature', '$30', 'Discover hidden trails and diverse wildlife in this ancient, peaceful tropical forest reserve.'),
('Kuza Cave', 'https://images.unsplash.com/photo-1546944062-878f24458f23?w=600&h=400&fit=crop', 'Adventure', '$25', 'Swim in the crystal-clear, mineral-rich healing waters of this ancient sacred limestone cave.'),
('Turtle Aquarium', 'https://images.unsplash.com/photo-1437622368342-7a3d73a34c8f?w=600&h=400&fit=crop', 'Marine', '$35', 'Feed and swim with rescued sea turtles in a natural lagoon, a truly heartwarming experience.'),
('Horse riding', 'https://images.unsplash.com/photo-1533036496924-4fbea4078dd2?w=600&h=400&fit=crop', 'Adventure', '$60', 'Enjoy a magical sunset ride along the pristine white beaches on majestic, well-trained horses.'),
('Quad Biking', 'https://images.unsplash.com/photo-1571401314352-7e5fca067c29?w=600&h=400&fit=crop', 'Adventure', '$75', 'Embark on an adrenaline-filled off-road adventure through remote villages and rugged landscapes.');

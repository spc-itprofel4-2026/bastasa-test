CREATE TABLE school_equipment_loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    borrower_name VARCHAR(60) NOT NULL,
    equipment_name VARCHAR(40) NOT NULL UNIQUE,
    days_borrowed INT NOT NULL,
    deposit_amount DECIMAL(10,2) NOT NULL,
    is_returned TINYINT(1),
    borrowed_on DATE NOT NULL
);

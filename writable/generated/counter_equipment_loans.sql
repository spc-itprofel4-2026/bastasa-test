CREATE TABLE counter_equipment_loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    borrower_name VARCHAR(30) NOT NULL UNIQUE,
    equipment_name VARCHAR(25) NOT NULL,
    days_borrowed INT NOT NULL,
    deposit_amount DECIMAL(10,2) NOT NULL,
    is_returned TINYINT(1),
    borrowed_on DATE NOT NULL
);

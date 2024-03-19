<?php
// Include your database connection file
include 'conn.php';

// Function to create tables
function createTables() {
    global $conn;
    // SQL statements to create tables
    $sql = "
        CREATE TABLE IF NOT EXISTS `admin` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(30) NOT NULL,
            `password` varchar(60) NOT NULL,
            `firstname` varchar(50) NOT NULL,
            `lastname` varchar(50) NOT NULL,
            `photo` varchar(200) NOT NULL,
            `created_on` date NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

        CREATE TABLE IF NOT EXISTS `attendance` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `employee_id` int(11) NOT NULL,
            `date` date NOT NULL,
            `time_in` time NOT NULL,
            `time_out` time NOT NULL,
            `late` int(1) NOT NULL,
            `status` int(1) NOT NULL,
            `num_hr` double NOT NULL,
            `under_day` int(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

        CREATE TABLE IF NOT EXISTS `cashadvance` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `date_advance` date NOT NULL,
            `employee_id` varchar(15) NOT NULL,
            `amount` double NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

        CREATE TABLE IF NOT EXISTS `deductions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `description` varchar(100) NOT NULL,
            `amount` double NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

        CREATE TABLE IF NOT EXISTS `employees` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `employee_id` varchar(15) NOT NULL,
            `firstname` varchar(50) NOT NULL,
            `middlename` varchar(50) NOT NULL,
            `lastname` varchar(50) NOT NULL,
            `address` text NOT NULL,
            `birthdate` date NOT NULL,
            `contact_info` varchar(100) NOT NULL,
            `gender` varchar(10) NOT NULL,
            `position_id` int(11) NOT NULL,
            `schedule_id` int(11) NOT NULL,
            `photo` varchar(200) NOT NULL,
            `created_on` date NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

        CREATE TABLE IF NOT EXISTS `overtime` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `employee_id` varchar(15) NOT NULL,
            `hours` double NOT NULL,
            `rate` double NOT NULL,
            `date_overtime` date NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

        CREATE TABLE IF NOT EXISTS `position` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `description` varchar(150) NOT NULL,
            `meaning` text NOT NULL,
            `rate` double NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

        CREATE TABLE IF NOT EXISTS `schedules` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `time_in` time NOT NULL,
            `time_out` time NOT NULL,
            `auto_time` int(1) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
    ";

    // Execute SQL queries
    if (mysqli_multi_query($conn, $sql)) {
        echo "Tables created successfully";
    } else {
        echo "Error creating tables: " . mysqli_error($conn);
    }
}

// Function to populate tables with sample data
function seedTables() {
    global $conn;
    // SQL statements to insert data into tables
    $sql = "
        -- SQL statements for inserting data...
    ";

    // Execute SQL queries
    if (mysqli_multi_query($conn, $sql)) {
        // Consume all results to avoid "Commands out of sync" error
        do {
            if ($result = mysqli_store_result($conn)) {
                mysqli_free_result($result);
            }
        } while (mysqli_next_result($conn));

        echo "Data inserted successfully";
    } else {
        echo "Error inserting data: " . mysqli_error($conn);
    }
}

createTables();
seedTables();

// Close database connection
mysqli_close($conn);
?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles.css">
        <title>Install Database</title>
    </head>
    <body>
    <div class="container">
        <h2>Database Connection Setup</h2>

        <ol>
            <li>
                Create an empty mysql database for this project and set up a database user that will be allowed to connect the database from the server where this project will be runnning. The user should have full admin rights to the database scheme including create table rights.
            </li>
            <li>
                Fill in the form with database connection credentials and submit.
            </li>
            <li>
                When the form submitted, the automated script will create the configuration file for the project that will keep the creedentials for database connection and will set up the database with needed tables for this project.
            </li>
            <li>
                After the process is finished you will be automatically redirected to the project root and will be able to use it.
            </li>
        </ol>

        <form action="Install.php" method="POST">
            <label for="db_server">Database Server:</label><br>
            <input type="text" id="db_server" name="db_server" value="localhost" required><br><br>

            <label for="db_username">Database Username:</label><br>
            <input type="text" id="db_username" name="db_username" value="root" required><br><br>

            <label for="db_password">Database Password:</label><br>
            <input type="password" id="db_password" name="db_password"><br><br>

            <label for="db_name">Database Name:</label><br>
            <input type="text" id="db_name" name="db_name" value="manosecurity" required><br><br>

            <input type="submit" value="Install">
        </form>
    </div>
    </body>
    </html>
    <?php
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{

    $db_server = $_POST['db_server'];
    $db_username = $_POST['db_username'];
    $db_password = $_POST['db_password'];
    $db_name = $_POST['db_name'];

    $configContent = "<?php\n\n";
    $configContent .= "define('DB_SERVER', '$db_server');\n";
    $configContent .= "define('DB_USERNAME', '$db_username');\n";
    $configContent .= "define('DB_PASSWORD', '$db_password');\n";
    $configContent .= "define('DB_NAME', '$db_name');\n\n";
    $configContent .= "\$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);\n\n";
    $configContent .= "if (\$conn->connect_error) {\n";
    $configContent .= "    die('Connection failed: ' . \$conn->connect_error);\n";
    $configContent .= "}\n";

    file_put_contents('Config.php', $configContent);

    require_once 'Config.php';

    echo "Configuration file created successfully.";

    $sql = "
        DROP TABLE IF EXISTS `users`;
        CREATE TABLE `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(256) NOT NULL,
          `password` varchar(64) NOT NULL,
          `first_name` varchar(64) NOT NULL,
          `last_name` varchar(64) NOT NULL,
          `balance` float NOT NULL,
          `registered_on` datetime NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
    ";

    if ($conn->multi_query($sql))
    {
        $conn->close();
        echo "Table 'users' created successfully.";
        header("Location: /");
    }
    else
    {
        $conn->close();
        echo "Error creating table: " . $conn->error;
    }
}
else
{
    echo 'Request method is not allowed';
}
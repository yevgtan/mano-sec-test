<?php
session_start();

if (!isset($_SESSION['credit'])) {
    $_SESSION['credit'] = 10;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slot machine game</title>

    <link rel="stylesheet" type="text/css" href="styles.css">

    <!-- Latest jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>
<body>
<div>Hello, <?= $_SESSION['fname'] ?> <a href="/Logout.php">Logout</a></div>
<div class="container">
    <h1>Welcome to Slot machine game</h1>
    <p>Twist the slot to win!</p>
    <h3>Rules of game:</h3>
    <p>Every twist costs you 1 credit.</p>
    <p>In order to win the roll, you have to get the same symbol in each block. There are 4 possible symbols:</p>
    <ul>
        <li>C for Cherry - 10 credits reward</li>
        <li>L for Lemon - 20 credits reward</li>
        <li>O for Orange - 30 credits reward</li>
        <li>W for Watermelon - 40 credits reward</li>
    </ul>

    <div>
        Account balance: <span id="balance"><?php echo $_SESSION['balance']; ?></span><br>
        Credits: <span id="credits"><?php echo $_SESSION['credit']; ?></span><br>
        <button id="withdrawButton">Withdraw Funds</button>
    </div>

    <table>
        <tr>
            <td id="item1">W</td>
            <td id="item2">W</td>
            <td id="item3">W</td>
        </tr>
    </table>

    <button class="btn" id="twistButton">Twist now</button>
</div>

<script>



    $(document).ready(function() {

        $('#twistButton').click(function() {
            $('#item1').text('X');
            $('#item2').text('X');
            $('#item3').text('X');

            $.ajax({
                url: '/Roll.php',
                method: 'GET',
                success: function(response) {
                    console.log("AJAX request successful! Data received:");
                    console.log(response.items);

                    // Update items
                    $('#item1').text(response.items[0]);
                    $('#item2').text(response.items[1]);
                    $('#item3').text(response.items[2]);

                    // Update credits
                    $('#credits').text(response.credits);

                    if(response.credits == 0)
                    {
                        $('#twistButton').prop('disabled', true);
                        alert('Game is over!');
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX request failed: " + error);
                    alert('The game is out of service. Please try again later');
                }
            });
        });

        $('#withdrawButton').click(function() {
            $.ajax({
                url: 'Withdrawal.php',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#credits').text('0');
                        $('#balance').text(response.newBalance.toFixed(2));
                        $('#twistButton').prop('disabled', true);
                        alert('Withdrawal successful!');
                    } else {
                        alert('Withdrawal failed: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });
    });
</script>

</body>
</html>

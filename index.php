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

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        p {
            color: #666;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        table {
            border-collapse: collapse;
            width: 50%;
            margin: 0 auto 30px;
            border: 1px solid #000;
            font-family: monospace;
        }

        td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
    </style>

    <!-- Latest jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>
<body>

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
        Credits: <span id="credits"><?php echo $_SESSION['credit']; ?></span>
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
                }
            });
        });
    });
</script>

</body>
</html>

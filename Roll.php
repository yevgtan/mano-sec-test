<?php
session_start();

function roll(): array
{
    $result = [];

    for($i = 0; $i < 3; $i++)
    {
        $result[$i] = rand(1, 4);
    }

    return $result;
}

function convert_roll_result(array $roll_numbers): array
{
    $roll_display_result = [];
    $converter = [
        1 => 'C',
        2 => 'L',
        3 => 'O',
        4 => 'W',
    ];

    foreach($roll_numbers as $key => $value)
    {
        $roll_display_result[$key] = $converter[$value];
    }

    return $roll_display_result;
}

function is_won($roll_results): array
{
    $result = [
        'won' => count(array_unique($roll_results)) == 1
    ];

    if($result['won'])
    {
        $result['credits_add'] = $roll_results[0] * 10;
    }
    else
    {
        $result['credits_add'] = -1;
    }

    return $result;
}

function is_to_reroll(int $chance_percent): bool
{
    $random_reroll = rand(1, 100);

    if($random_reroll < $chance_percent)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function get_final_result($roll_res, $winning_res): array
{
    $result = [
        "items" => convert_roll_result($roll_res)
    ];

    return array_merge($result, $winning_res);
}

if($_SESSION['credit'] == 0)
{
    header('Content-Type: application/json');
    echo json_encode([
        "error" => true,
        "msg" => 'You do not have credits to continue. Game over.'
    ]);
    exit;
}

$roll_res = roll();
$winning_res = is_won($roll_res);

if($winning_res['won'] && $_SESSION['credit'] >= 40 && $_SESSION['credit'] < 60 && is_to_reroll(30))
{
    $roll_res = roll();
    $winning_res = is_won($roll_res);
}

if($winning_res['won'] && $_SESSION['credit'] >= 60 && is_to_reroll(60))
{
    $roll_res = roll();
    $winning_res = is_won($roll_res);
}

$_SESSION['credit'] += $winning_res['credits_add'];

$final_res = get_final_result($roll_res, $winning_res);

unset($final_res['credits_add']);
$final_res['credits'] = $_SESSION['credit'];

header('Content-Type: application/json');
echo json_encode($final_res);
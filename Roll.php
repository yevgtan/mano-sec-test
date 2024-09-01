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

function convert_roll_result(array $rollNumbers): array
{
    $rollDisplayResult = [];
    $converter = [
        1 => 'C',
        2 => 'L',
        3 => 'O',
        4 => 'W',
    ];

    foreach($rollNumbers as $key => $value)
    {
        $rollDisplayResult[$key] = $converter[$value];
    }

    return $rollDisplayResult;
}

function is_won($rollResults): array
{
    $result = [
        'won' => count(array_unique($rollResults)) == 1
    ];

    if($result['won'])
    {
        $result['credits_add'] = $rollResults[0] * 10;
    }
    else
    {
        $result['credits_add'] = -1;
    }

    return $result;
}

function is_to_reroll(int $chancePercent): bool
{
    $randomReroll = rand(1, 100);

    if($randomReroll < $chancePercent)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function get_final_result($rollRes, $winningRes): array
{
    $result = [
        "items" => convert_roll_result($rollRes)
    ];

    return array_merge($result, $winningRes);
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

$rollRes = roll();
$winningRes = is_won($rollRes);

if($winningRes['won'] && $_SESSION['credit'] >= 40 && $_SESSION['credit'] < 60 && is_to_reroll(30))
{
    $rollRes = roll();
    $winningRes = is_won($rollRes);
}

if($winningRes['won'] && $_SESSION['credit'] >= 60 && is_to_reroll(60))
{
    $rollRes = roll();
    $winningRes = is_won($rollRes);
}

$_SESSION['credit'] += $winningRes['credits_add'];

$finalRes = get_final_result($rollRes, $winningRes);

unset($finalRes['credits_add']);
$finalRes['credits'] = $_SESSION['credit'];

header('Content-Type: application/json');
echo json_encode($finalRes);
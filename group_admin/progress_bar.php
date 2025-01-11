
<?php
// Ensure these variables are available
if (!isset($conn) || !isset($group_id)) {
    die('Required variables not set');
}

// Fetch the goal amount from the my_group table
$goal_amount = 0;
$query_goal = "SELECT goal_amount FROM my_group WHERE group_id = ?";
$stmt_goal = $conn->prepare($query_goal);
$stmt_goal->bind_param("i", $group_id);
$stmt_goal->execute();
$stmt_goal->bind_result($goal_amount);
$stmt_goal->fetch();
$stmt_goal->close();

// Fetch the total achieved amount from the savings table
$achieved_amount = 0;
$query_achieved = "SELECT SUM(amount) AS total_achieved FROM savings WHERE group_id = ?";
$stmt_achieved = $conn->prepare($query_achieved);
$stmt_achieved->bind_param("i", $group_id);
$stmt_achieved->execute();
$stmt_achieved->bind_result($achieved_amount);
$stmt_achieved->fetch();
$stmt_achieved->close();

// Calculate the progress percentage
$progress_percentage = ($goal_amount > 0) ? ($achieved_amount / $goal_amount) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Savings</title>
</head>
<body>
<div class="gauge-container">
    <div class="stats">
        <div class="stat-item">
            <div class="stat-label">Goal Amount</div>
            <div class="stat-value">BDT <?php echo number_format($goal_amount, 2); ?></div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Achieved</div>
            <div class="stat-value">BDT <?php echo number_format($achieved_amount, 2); ?></div>
        </div>
    </div>
    <div class="gauge">
        <div class="gauge-body">
            <div class="gauge-fill"></div>
            <div class="gauge-cover">
                <div class="gauge-value">0%</div>
            </div>
        </div>
        <div class="gauge-ticks">
            <div class="tick tick-0">0</div>
            <div class="tick tick-25">25</div>
            <div class="tick tick-50">50</div>
            <div class="tick tick-75">75</div>
            <div class="tick tick-100">100</div>
        </div>
    </div>
</div>

<style>
.gauge-container {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: white;
    padding: 20px;
    font-family: sans-serif;
}

.stats {
    display: flex;
    justify-content: space-around;
    width: 100%;
    max-width: 400px;
    margin-bottom: 20px;
}

.stat-item {
    text-align: center;
    padding: 10px;
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 20px;
    font-weight: bold;
    color: #333;
}

.gauge {
    width: 100%;
    max-width: 250px;
    position: relative;
    padding-bottom: 50px;
}

.gauge-body {
    width: 100%;
    height: 0;
    padding-bottom: 50%;
    background: #e6e6e6;
    position: relative;
    border-top-left-radius: 100% 200%;
    border-top-right-radius: 100% 200%;
    overflow: hidden;
}

.gauge-fill {
    position: absolute;
    top: 100%;
    left: 0;
    width: inherit;
    height: 100%;
    background:rgb(3, 70, 255);
    transform-origin: center top;
    transform: rotate(0turn);
    transition: transform 0.2s ease-out;
}

.gauge-cover {
    width: 75%;
    height: 150%;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 25%;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding-bottom: 25%;
    box-sizing: border-box;
}

.gauge-value {
    font-family: sans-serif;
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.gauge-ticks {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
}

.tick {
    position: absolute;
    font-size: 12px;
    color: #999;
}

.tick-0 { left: 7%; top: 50%; }
.tick-25 { left: 27%; top: 20%; }
.tick-50 { left: 50%; top: 10%; transform: translateX(-50%); }
.tick-75 { right: 27%; top: 20%; }
.tick-100 { right: 7%; top: 50%; }
</style>

<script>
function setGaugeValue(gauge, value) {
    if (value < 0 || value > 100) {
        return;
    }

    const gaugeFill = gauge.querySelector(".gauge-fill");
    const gaugeValue = gauge.querySelector(".gauge-value");
    
    gaugeFill.style.transform = `rotate(${value/200}turn)`;
    gaugeValue.textContent = `${Math.round(value)}%`;
}

const gauge = document.querySelector(".gauge");
let currentValue = 0;
const targetValue = <?php echo round($progress_percentage, 2); ?>; // Dynamically fetched progress percentage

const animateGauge = () => {
    if (currentValue < targetValue) {
        currentValue += 1;
        setGaugeValue(gauge, currentValue);
        requestAnimationFrame(animateGauge);
    }
};

document.addEventListener('DOMContentLoaded', animateGauge);
</script>
</body>
</html>